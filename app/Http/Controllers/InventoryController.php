<?php

namespace App\Http\Controllers;

use App\Hub;
use App\InventoryStock;
use App\Item;
use App\Services\AuditLogger;
use App\Services\InventoryService;
use App\StockMovement;
use Illuminate\Http\Request;
use Session;

class InventoryController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $hubs = Hub::active()->orderBy('name')->get();
        $hubId = $request->get('hub_id', $hubs->first() ? $hubs->first()->id : null);
        $search = $request->get('q');
        $type = $request->get('type');
        $lowOnly = $request->boolean('low');

        $items = Item::with(['stocks' => function ($query) use ($hubId) {
            $query->where('hub_id', $hubId);
        }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%');
                });
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderBy('name')
            ->paginate(20)
            ->appends($request->all());

        if ($lowOnly) {
            $items->setCollection($items->getCollection()->filter(function ($item) {
                return $item->stocks->first() && $item->stocks->first()->quantity <= $item->reorder_level;
            })->values());
        }

        $movements = StockMovement::with(['item', 'hub', 'employee'])
            ->when($hubId, function ($query) use ($hubId) {
                $query->where('hub_id', $hubId);
            })
            ->latest()
            ->take(15)
            ->get();

        return view('peams.inventory.index', compact('hubs', 'hubId', 'items', 'movements', 'search', 'type', 'lowOnly'));
    }

    public function store(Request $request)
    {
        $data = $this->validateItem($request);
        $item = Item::create($data);

        $hubs = Hub::active()->get();
        foreach ($hubs as $hub) {
            InventoryStock::firstOrCreate(
                ['hub_id' => $hub->id, 'item_id' => $item->id],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
        }

        AuditLogger::record('catalog_created', 'Created item '.$item->sku.' - '.$item->name, null, $item);
        Session::flash('success', 'Item added to the catalog.');

        return redirect()->route('peams.inventory.index');
    }

    public function update(Request $request, Item $item)
    {
        $data = $this->validateItem($request, $item->id);
        $item->update($data);

        AuditLogger::record('catalog_updated', 'Updated item '.$item->sku, null, $item);
        Session::flash('success', 'Item updated.');

        return redirect()->route('peams.inventory.index');
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'hub_id' => 'required|exists:hubs,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $this->inventory->adjust($data['hub_id'], $data['item_id'], $data['quantity'], auth()->id(), $data['notes'] ?? null);

        AuditLogger::record('stock_adjusted', 'Manual stock count adjustment for item #'.$data['item_id']);
        Session::flash('success', 'Stock level adjusted.');

        return redirect()->route('peams.inventory.index', ['hub_id' => $data['hub_id']]);
    }

    protected function validateItem(Request $request, $ignoreId = null)
    {
        return $request->validate([
            'sku' => 'required|string|max:60|unique:items,sku'.($ignoreId ? ','.$ignoreId : ''),
            'name' => 'required|string|max:191',
            'type' => 'required|in:packaging,equipment',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
            'unit_cost' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'is_loanable' => 'nullable|boolean',
            'requires_preventive_maintenance' => 'nullable|boolean',
            'service_interval_days' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]) + [
            'is_loanable' => $request->boolean('is_loanable'),
            'requires_preventive_maintenance' => $request->boolean('requires_preventive_maintenance'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
