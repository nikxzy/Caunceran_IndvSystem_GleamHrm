<?php

namespace App\Http\Controllers;

use App\Client;
use App\DeliveryProof;
use App\Employee;
use App\Hub;
use App\Item;
use App\Order;
use App\Services\AuditLogger;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Session;

class OrderController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $status = $request->get('status');
        $hubId = $request->get('hub_id');

        $orders = Order::with(['client', 'hub', 'creator'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($hubId, function ($query) use ($hubId) {
                $query->where('hub_id', $hubId);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->all());

        $hubs = Hub::active()->orderBy('name')->get();

        return view('peams.orders.index', compact('orders', 'hubs', 'status', 'hubId'));
    }

    public function create(Request $request)
    {
        $hubs = Hub::active()->orderBy('name')->get();
        $clients = Client::active()->orderBy('name')->get();
        $items = Item::active()->orderBy('name')->get();
        $selectedHub = $request->get('hub_id', $hubs->first() ? $hubs->first()->id : null);

        return view('peams.orders.create', compact('hubs', 'clients', 'items', 'selectedHub'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'hub_id' => 'required|exists:hubs,id',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $lines = collect($data['items'])->filter(function ($line) {
            return ! empty($line['item_id']) && (int) $line['quantity'] > 0;
        });

        if ($lines->isEmpty()) {
            return back()->withInput()->with('error', 'Add at least one line item to the order.');
        }

        try {
            $order = DB::transaction(function () use ($data, $lines) {
                $total = $lines->sum(function ($line) {
                    return $line['quantity'] * $line['unit_price'];
                });

                $order = Order::create([
                    'order_number' => 'SO-'.now()->format('Ymd').'-'.str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                    'client_id' => $data['client_id'],
                    'hub_id' => $data['hub_id'],
                    'created_by' => auth()->id(),
                    'status' => Order::STATUS_PENDING_HANDOFF,
                    'picking_slip_status' => Order::PICKING_PENDING,
                    'total_amount' => $total,
                    'delivery_address' => $data['delivery_address'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                foreach ($lines as $line) {
                    $order->items()->create([
                        'item_id' => $line['item_id'],
                        'quantity' => $line['quantity'],
                        'unit_price' => $line['unit_price'],
                    ]);
                }

                return $this->inventory->reserveOrder($order, auth()->id());
            });
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        AuditLogger::record('order_created', 'Created '.$order->order_number.' with stock reserved', null, $order);
        Session::flash('success', 'Order created and stock reserved successfully.');

        return redirect()->route('peams.orders.show', $order);
    }

    public function show(Order $order)
    {
        $order->load(['items.item', 'client', 'hub', 'creator', 'driver', 'deliveryProof.deliverer']);

        return view('peams.orders.show', compact('order'));
    }

    public function generatePickingSlip(Order $order)
    {
        if ($order->picking_slip_status !== Order::PICKING_PENDING) {
            return back()->with('error', 'A picking slip has already been generated for this order.');
        }

        $order->update([
            'picking_slip_status' => Order::PICKING_GENERATED,
            'picking_slip_generated_at' => now(),
        ]);

        AuditLogger::record('picking_slip_generated', 'Picking slip generated for '.$order->order_number, null, $order);
        Session::flash('success', 'Picking slip generated.');

        return redirect()->route('peams.orders.picking-slip', $order);
    }

    public function pickingSlip(Order $order)
    {
        $order->load(['items.item', 'client', 'hub', 'creator']);

        return view('peams.orders.picking-slip', compact('order'));
    }

    public function markPicked(Order $order)
    {
        if ($order->picking_slip_status !== Order::PICKING_GENERATED) {
            return back()->with('error', 'Generate the picking slip before marking items as picked.');
        }

        $order->update([
            'picking_slip_status' => Order::PICKING_PICKED,
            'picked_at' => now(),
        ]);

        AuditLogger::record('order_picked', 'Order '.$order->order_number.' picked and ready for handoff', null, $order);
        Session::flash('success', 'Order marked as picked. It is now ready for driver handoff.');

        return redirect()->route('peams.orders.show', $order);
    }

    public function dispatch(Request $request, Order $order)
    {
        $data = $request->validate([
            'driver_id' => 'nullable|exists:employees,id',
        ]);

        if (! $order->canDispatch()) {
            return back()->with('error', 'Order must be picked before it can be dispatched.');
        }

        try {
            DB::transaction(function () use ($order, $data) {
                $this->inventory->issueOrder($order, auth()->id());

                $order->update([
                    'status' => Order::STATUS_IN_TRANSIT,
                    'driver_id' => $data['driver_id'] ?? null,
                    'dispatched_at' => now(),
                    'verification_code' => strtoupper(Str::random(6)),
                ]);
            });
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AuditLogger::record('order_dispatched', 'Order '.$order->order_number.' handed to driver', null, $order);
        Session::flash('success', 'Order dispatched. Driver can now record proof of delivery.');

        return redirect()->route('peams.orders.show', $order);
    }

    public function deliver(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_IN_TRANSIT) {
            return back()->with('error', 'Only in-transit orders can be delivered.');
        }

        $data = $request->validate([
            'received_by_name' => 'required|string|max:191',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = time().'_'.preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/pod'), $name);
            $photoPath = 'uploads/pod/'.$name;
        }

        DB::transaction(function () use ($order, $data, $photoPath) {
            DeliveryProof::create([
                'order_id' => $order->id,
                'received_by_name' => $data['received_by_name'],
                'verification_code' => $order->verification_code,
                'photo_path' => $photoPath,
                'delivered_by' => auth()->id(),
                'delivered_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);

            $order->update([
                'status' => Order::STATUS_DELIVERED,
                'delivered_at' => now(),
            ]);
        });

        AuditLogger::record('order_delivered', 'Digital proof of delivery captured for '.$order->order_number, null, $order);
        Session::flash('success', 'Delivery verified and digital receipt recorded.');

        return redirect()->route('peams.orders.show', $order);
    }

    public function cancel(Order $order)
    {
        if ($order->status === Order::STATUS_DELIVERED) {
            return back()->with('error', 'Delivered orders cannot be cancelled.');
        }

        try {
            DB::transaction(function () use ($order) {
                if ($order->status === Order::STATUS_PENDING_HANDOFF) {
                    $this->inventory->releaseOrder($order, auth()->id());
                }

                $order->update(['status' => Order::STATUS_CANCELLED]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AuditLogger::record('order_cancelled', 'Cancelled '.$order->order_number, null, $order);
        Session::flash('success', 'Order cancelled and reserved stock released.');

        return redirect()->route('peams.orders.show', $order);
    }
}
