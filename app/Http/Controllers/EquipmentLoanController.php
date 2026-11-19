<?php

namespace App\Http\Controllers;

use App\Client;
use App\Employee;
use App\EquipmentLoan;
use App\Hub;
use App\Item;
use App\Services\AuditLogger;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Session;

class EquipmentLoanController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $status = $request->get('status');

        EquipmentLoan::open()->get()->each->refreshOverdueStatus();

        $loans = EquipmentLoan::with(['equipment', 'hub', 'client', 'handler'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->all());

        return view('peams.loans.index', compact('loans', 'status'));
    }

    public function create()
    {
        $hubs = Hub::active()->orderBy('name')->get();
        $clients = Client::active()->orderBy('name')->get();
        $equipment = Item::equipment()->loanable()->active()->orderBy('name')->get();

        return view('peams.loans.create', compact('hubs', 'clients', 'equipment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'required|string|max:191',
            'equipment_id' => 'required|exists:items,id',
            'hub_id' => 'required|exists:hubs,id',
            'borrow_date' => 'required|date',
            'expected_return_date' => 'required|date|after_or_equal:borrow_date',
            'condition_out' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        $equipment = Item::findOrFail($data['equipment_id']);
        if (! $equipment->is_loanable) {
            return back()->withInput()->with('error', 'The selected equipment is not marked as loanable.');
        }

        try {
            $loan = DB::transaction(function () use ($data) {
                $loan = EquipmentLoan::create([
                    'client_name' => $data['client_name'],
                    'client_id' => $data['client_id'] ?? null,
                    'equipment_id' => $data['equipment_id'],
                    'hub_id' => $data['hub_id'],
                    'borrow_date' => $data['borrow_date'],
                    'expected_return_date' => $data['expected_return_date'],
                    'status' => EquipmentLoan::STATUS_ON_LOAN,
                    'condition_out' => $data['condition_out'] ?? null,
                    'handled_by' => auth()->id(),
                    'notes' => $data['notes'] ?? null,
                ]);

                return $this->inventory->loanEquipment($loan, auth()->id());
            });
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        AuditLogger::record('equipment_loaned', 'Equipment loan #'.$loan->id.' issued to '.$loan->client_name, null, $loan);
        Session::flash('success', 'Equipment loan recorded and stock updated.');

        return redirect()->route('peams.loans.index');
    }

    public function returnLoan(Request $request, EquipmentLoan $loan)
    {
        if ($loan->status === EquipmentLoan::STATUS_RETURNED) {
            return back()->with('error', 'This equipment has already been returned.');
        }

        $data = $request->validate([
            'condition_in' => 'nullable|string|max:191',
            'actual_return_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($loan, $data) {
            $this->inventory->returnEquipment($loan, auth()->id());

            $loan->update([
                'status' => EquipmentLoan::STATUS_RETURNED,
                'actual_return_date' => $data['actual_return_date'] ?? now()->toDateString(),
                'condition_in' => $data['condition_in'] ?? null,
            ]);
        });

        AuditLogger::record('equipment_returned', 'Equipment loan #'.$loan->id.' returned by '.$loan->client_name, null, $loan);
        Session::flash('success', 'Equipment returned and stock restored.');

        return redirect()->route('peams.loans.index');
    }
}
