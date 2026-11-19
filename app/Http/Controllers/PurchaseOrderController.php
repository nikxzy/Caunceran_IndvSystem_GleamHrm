<?php

namespace App\Http\Controllers;

use App\Hub;
use App\Item;
use App\PurchaseOrder;
use App\Services\AuditLogger;
use App\Services\InventoryService;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class PurchaseOrderController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $status = $request->get('status');

        $purchaseOrders = PurchaseOrder::with(['vendor', 'hub', 'requester'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->all());

        $counts = [
            'all' => PurchaseOrder::count(),
            PurchaseOrder::STATUS_PENDING => PurchaseOrder::where('status', PurchaseOrder::STATUS_PENDING)->count(),
            PurchaseOrder::STATUS_APPROVED => PurchaseOrder::where('status', PurchaseOrder::STATUS_APPROVED)->count(),
            PurchaseOrder::STATUS_RECEIVED => PurchaseOrder::where('status', PurchaseOrder::STATUS_RECEIVED)->count(),
        ];

        return view('peams.procurement.index', compact('purchaseOrders', 'counts', 'status'));
    }

    public function create()
    {
        $hubs = Hub::active()->orderBy('name')->get();
        $vendors = Vendor::orderBy('company_name')->get();
        $items = Item::active()->orderBy('name')->get();

        return view('peams.procurement.create', compact('hubs', 'vendors', 'items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'hub_id' => 'required|exists:hubs,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $lines = collect($data['items'])->filter(function ($line) {
            return ! empty($line['item_id']) && (int) $line['quantity'] > 0;
        });

        if ($lines->isEmpty()) {
            return back()->withInput()->with('error', 'Add at least one line item to the purchase order.');
        }

        $purchaseOrder = DB::transaction(function () use ($data, $lines) {
            $total = $lines->sum(function ($line) {
                return $line['quantity'] * $line['unit_cost'];
            });

            $po = PurchaseOrder::create([
                'po_number' => 'PO-'.now()->format('Ymd').'-'.str_pad(PurchaseOrder::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                'vendor_id' => $data['vendor_id'] ?? null,
                'hub_id' => $data['hub_id'],
                'requested_by' => auth()->id(),
                'status' => PurchaseOrder::STATUS_PENDING,
                'order_date' => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'total_amount' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $po->items()->create([
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                ]);
            }

            return $po;
        });

        AuditLogger::record('po_created', 'Purchase order '.$purchaseOrder->po_number.' submitted for approval', null, $purchaseOrder);
        Session::flash('success', 'Purchase order submitted for approval.');

        return redirect()->route('peams.purchase-orders.show', $purchaseOrder);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.item', 'vendor', 'hub', 'requester', 'approver']);

        return view('peams.procurement.show', compact('purchaseOrder'));
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        if (! $purchaseOrder->isPending()) {
            return back()->with('error', 'Only pending purchase orders can be approved.');
        }

        $purchaseOrder->update([
            'status' => PurchaseOrder::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLogger::record('po_approved', 'Approved purchase order '.$purchaseOrder->po_number, null, $purchaseOrder);
        Session::flash('success', 'Purchase order approved. Warehouse can now receive the shipment.');

        return redirect()->route('peams.purchase-orders.show', $purchaseOrder);
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        try {
            $this->inventory->receivePurchaseOrder($purchaseOrder, auth()->id());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AuditLogger::record('po_received', 'Received shipment for '.$purchaseOrder->po_number.' and updated stock', null, $purchaseOrder);
        Session::flash('success', 'Shipment received. Stock levels updated in real time.');

        return redirect()->route('peams.purchase-orders.show', $purchaseOrder);
    }
}
