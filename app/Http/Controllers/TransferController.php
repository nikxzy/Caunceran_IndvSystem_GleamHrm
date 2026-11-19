<?php

namespace App\Http\Controllers;

use App\Hub;
use App\InventoryStock;
use App\Item;
use App\Services\AuditLogger;
use App\Services\InventoryService;
use App\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Session;

class TransferController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $status = $request->get('status');

        $transfers = StockTransfer::with(['fromHub', 'toHub', 'requester', 'receiver', 'items.item'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->all());

        return view('peams.transfers.index', compact('transfers', 'status'));
    }

    public function create(Request $request)
    {
        $hubs = Hub::active()->orderBy('name')->get();
        $fromHub = $request->get('from_hub_id', $hubs->first() ? $hubs->first()->id : null);
        $items = Item::active()->orderBy('name')->get();
        $stocks = InventoryStock::with('item')
            ->when($fromHub, function ($query) use ($fromHub) {
                $query->where('hub_id', $fromHub);
            })
            ->get();

        return view('peams.transfers.create', compact('hubs', 'items', 'stocks', 'fromHub'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_hub_id' => 'required|exists:hubs,id',
            'to_hub_id' => 'required|exists:hubs,id|different:from_hub_id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $lines = collect($data['items'])->filter(function ($line) {
            return ! empty($line['item_id']) && (int) $line['quantity'] > 0;
        });

        if ($lines->isEmpty()) {
            return back()->withInput()->with('error', 'Add at least one item to the transfer request.');
        }

        $transfer = DB::transaction(function () use ($data, $lines) {
            $transfer = StockTransfer::create([
                'transfer_number' => 'TR-'.now()->format('Ymd').'-'.str_pad(StockTransfer::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                'from_hub_id' => $data['from_hub_id'],
                'to_hub_id' => $data['to_hub_id'],
                'requested_by' => auth()->id(),
                'status' => StockTransfer::STATUS_REQUESTED,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $transfer->items()->create([
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                ]);
            }

            return $transfer;
        });

        AuditLogger::record('transfer_requested', 'Transfer '.$transfer->transfer_number.' requested', null, $transfer);
        Session::flash('success', 'Transfer requested. Dispatch it when the stock leaves the source hub.');

        return redirect()->route('peams.transfers.show', $transfer);
    }

    public function show(StockTransfer $transfer)
    {
        $transfer->load(['items.item', 'fromHub', 'toHub', 'requester', 'receiver']);

        return view('peams.transfers.show', compact('transfer'));
    }

    public function dispatch(StockTransfer $transfer)
    {
        try {
            $this->inventory->dispatchTransfer($transfer, auth()->id());
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AuditLogger::record('transfer_dispatched', 'Transfer '.$transfer->transfer_number.' is in transit', null, $transfer);
        Session::flash('success', 'Transfer dispatched. Source hub stock has been deducted.');

        return redirect()->route('peams.transfers.show', $transfer);
    }

    public function receive(StockTransfer $transfer)
    {
        try {
            $this->inventory->receiveTransfer($transfer, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AuditLogger::record('transfer_received', 'Transfer '.$transfer->transfer_number.' received at destination hub', null, $transfer);
        Session::flash('success', 'Transfer received. Destination hub stock updated.');

        return redirect()->route('peams.transfers.show', $transfer);
    }
}
