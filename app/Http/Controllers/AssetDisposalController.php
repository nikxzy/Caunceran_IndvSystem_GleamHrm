<?php

namespace App\Http\Controllers;

use App\AssetDisposal;
use App\Hub;
use App\InventoryStock;
use App\Item;
use App\Services\AuditLogger;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Session;

class AssetDisposalController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $type = $request->get('type');
        $hubId = $request->get('hub_id');

        $disposals = AssetDisposal::with(['item', 'hub', 'approver', 'disposer'])
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($hubId, function ($query) use ($hubId) {
                $query->where('hub_id', $hubId);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->all());

        $hubs = Hub::active()->orderBy('name')->get();

        $stats = [
            'total' => AssetDisposal::count(),
            'packaging' => AssetDisposal::where('type', 'damaged_packaging')->count(),
            'machinery' => AssetDisposal::where('type', 'scrap_machinery')->count(),
            'value' => (float) AssetDisposal::sum('estimated_value'),
        ];

        return view('peams.disposals.index', compact('disposals', 'hubs', 'type', 'hubId', 'stats'));
    }

    public function create(Request $request)
    {
        $hubs = Hub::active()->orderBy('name')->get();
        $hubId = $request->get('hub_id');
        $items = Item::active()->orderBy('name')->get();
        $stock = InventoryStock::with('item')
            ->when($hubId, function ($query) use ($hubId) {
                $query->where('hub_id', $hubId);
            })
            ->get();

        return view('peams.disposals.create', compact('hubs', 'items', 'stock', 'hubId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'hub_id' => 'required|exists:hubs,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:damaged_packaging,scrap_machinery,obsolete',
            'reason' => 'required|string',
            'disposal_method' => 'nullable|string|max:191',
            'estimated_value' => 'nullable|numeric|min:0',
        ]);

        try {
            $disposal = DB::transaction(function () use ($data) {
                $disposal = AssetDisposal::create([
                    'reference_no' => 'DSP-'.now()->format('Ymd').'-'.str_pad(AssetDisposal::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                    'item_id' => $data['item_id'],
                    'hub_id' => $data['hub_id'],
                    'quantity' => $data['quantity'],
                    'type' => $data['type'],
                    'reason' => $data['reason'],
                    'disposal_method' => $data['disposal_method'] ?? null,
                    'estimated_value' => $data['estimated_value'] ?? 0,
                    'approved_by' => auth()->id(),
                    'disposed_by' => auth()->id(),
                    'disposed_at' => now(),
                ]);

                return $this->inventory->dispose($disposal, auth()->id());
            });
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        AuditLogger::record('asset_disposed', 'Asset disposal '.$disposal->reference_no.' recorded', null, $disposal);
        Session::flash('success', 'Asset disposal logged and stock written off.');

        return redirect()->route('peams.disposals.index');
    }
}
