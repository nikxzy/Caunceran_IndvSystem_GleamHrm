@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">{{ $order->order_number }}</h3>
        <p class="text-muted small mb-0">{{ optional($order->client)->name }} &middot; {{ optional($order->hub)->name }}</p>
      </div>
      <div class="d-flex flex-wrap" style="gap:.5rem;">
        <a href="{{ route('peams.orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Orders</a>
        @if($order->status !== \App\Order::STATUS_CANCELLED && $order->status !== \App\Order::STATUS_DELIVERED)
          <form method="POST" action="{{ route('peams.orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order and release reserved stock?');">
            @csrf
            <button class="btn btn-outline-danger"><i class="fas fa-ban mr-1"></i> Cancel Order</button>
          </form>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row">
      <div class="col-lg-8">
        <div class="card peams-card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="card-title m-0"><i class="fas fa-boxes text-primary mr-2"></i> Order Lines</h5>
            <div>
              <span class="badge badge-soft-{{ $order->status_badge }}">{{ $order->status }}</span>
              <span class="badge badge-soft-{{ $order->picking_badge }}">Picking: {{ $order->picking_slip_status }}</span>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-modern mb-0">
                <thead><tr><th>Item</th><th>SKU</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Line Total</th></tr></thead>
                <tbody>
                  @foreach($order->items as $line)
                    <tr>
                      <td class="font-weight-bold">{{ optional($line->item)->name ?? '—' }}</td>
                      <td><code>{{ optional($line->item)->sku ?? '—' }}</code></td>
                      <td class="text-right">{{ number_format($line->quantity) }}</td>
                      <td class="text-right">₱{{ number_format($line->unit_price, 2) }}</td>
                      <td class="text-right font-weight-bold">₱{{ number_format($line->line_total, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot><tr><td colspan="4" class="text-right font-weight-bold">Total</td><td class="text-right font-weight-bold text-primary">₱{{ number_format($order->total_amount, 2) }}</td></tr></tfoot>
              </table>
            </div>
          </div>
        </div>

        <div class="card peams-card mb-4">
          <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-route text-primary mr-2"></i> Fulfilment Workflow</h5></div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-4 col-md-3 mb-3">
                <div class="p-3 rounded h-100 {{ $order->isReserved() ? 'bg-success text-white' : 'bg-light' }}" style="border-radius:12px;">
                  <i class="fas fa-lock fa-lg mb-2"></i><div class="small font-weight-bold">Stock Reserved</div>
                </div>
              </div>
              <div class="col-4 col-md-3 mb-3">
                <div class="p-3 rounded h-100 {{ $order->picking_slip_status !== \App\Order::PICKING_PENDING ? 'bg-primary text-white' : 'bg-light' }}" style="border-radius:12px;">
                  <i class="fas fa-clipboard-check fa-lg mb-2"></i><div class="small font-weight-bold">Picking Slip</div>
                </div>
              </div>
              <div class="col-4 col-md-3 mb-3">
                <div class="p-3 rounded h-100 {{ $order->status === \App\Order::STATUS_IN_TRANSIT || $order->status === \App\Order::STATUS_DELIVERED ? 'bg-primary text-white' : 'bg-light' }}" style="border-radius:12px;">
                  <i class="fas fa-truck fa-lg mb-2"></i><div class="small font-weight-bold">Dispatched</div>
                </div>
              </div>
              <div class="col-4 col-md-3 mb-3">
                <div class="p-3 rounded h-100 {{ $order->status === \App\Order::STATUS_DELIVERED ? 'bg-success text-white' : 'bg-light' }}" style="border-radius:12px;">
                  <i class="fas fa-signature fa-lg mb-2"></i><div class="small font-weight-bold">Digital POD</div>
                </div>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-md-6 mb-2"><span class="text-muted small d-block">Delivery address</span><strong>{{ $order->delivery_address ?: 'Not specified' }}</strong></div>
              <div class="col-md-6 mb-2"><span class="text-muted small d-block">Created by</span><strong>{{ optional($order->creator)->full_name ?? 'System' }}</strong></div>
              <div class="col-md-6 mb-2"><span class="text-muted small d-block">Assigned driver</span><strong>{{ optional($order->driver)->full_name ?? 'Not assigned' }}</strong></div>
              <div class="col-md-6 mb-2"><span class="text-muted small d-block">Verification code</span><strong>{{ $order->verification_code ?? 'Issued on dispatch' }}</strong></div>
              @if($order->notes)
                <div class="col-12 mt-2"><span class="text-muted small d-block">Notes</span>{{ $order->notes }}</div>
              @endif
            </div>
          </div>
        </div>

        @if($order->status === \App\Order::STATUS_IN_TRANSIT)
          <div class="card peams-card mb-4">
            <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-signature text-primary mr-2"></i> Digital Proof of Delivery</h5></div>
            <form method="POST" action="{{ route('peams.orders.deliver', $order) }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 form-group"><label>Received by (customer name) *</label><input name="received_by_name" class="form-control" required value="{{ old('received_by_name') }}"></div>
                  <div class="col-md-6 form-group"><label>Delivery photo / signature sheet</label><input type="file" name="photo" class="form-control-file" accept="image/*"></div>
                  <div class="col-12 form-group"><label>Delivery notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Condition on arrival, partial delivery notes...">{{ old('notes') }}</textarea></div>
                </div>
                <p class="small text-muted mb-0"><i class="fas fa-shield-alt mr-1"></i>This replaces the paper delivery sheet and records delivery clearance against verification code <strong>{{ $order->verification_code }}</strong>.</p>
              </div>
              <div class="card-footer bg-white text-right"><button class="btn btn-success"><i class="fas fa-check mr-1"></i> Confirm Delivery</button></div>
            </form>
          </div>
        @endif

        @if($order->deliveryProof)
          <div class="card peams-card mb-4">
            <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-receipt text-success mr-2"></i> Delivery Receipt</h5></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-2"><span class="text-muted small d-block">Received by</span><strong>{{ $order->deliveryProof->received_by_name }}</strong></div>
                <div class="col-md-6 mb-2"><span class="text-muted small d-block">Delivered at</span><strong>{{ $order->deliveryProof->delivered_at ? $order->deliveryProof->delivered_at->format('M d, Y h:i A') : '—' }}</strong></div>
                <div class="col-md-6 mb-2"><span class="text-muted small d-block">Delivered by</span><strong>{{ optional($order->deliveryProof->deliverer)->full_name ?? '—' }}</strong></div>
                <div class="col-md-6 mb-2"><span class="text-muted small d-block">Verification code</span><strong>{{ $order->deliveryProof->verification_code }}</strong></div>
                @if($order->deliveryProof->photo_path)
                  <div class="col-12 mt-2"><img src="{{ asset($order->deliveryProof->photo_path) }}" class="img-fluid rounded border" style="max-height:220px;" alt="Proof of delivery"></div>
                @endif
              </div>
            </div>
          </div>
        @endif
      </div>

      <div class="col-lg-4">
        <div class="card peams-card mb-4">
          <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-tasks text-primary mr-2"></i> Next Action</h5></div>
          <div class="card-body">
            @if($order->status === \App\Order::STATUS_CANCELLED)
              <p class="text-danger mb-0"><i class="fas fa-ban mr-1"></i>This order was cancelled.</p>
            @elseif($order->status === \App\Order::STATUS_DELIVERED)
              <p class="text-success mb-0"><i class="fas fa-check-circle mr-1"></i>Delivered and signed off digitally.</p>
            @elseif($order->picking_slip_status === \App\Order::PICKING_PENDING)
              <p class="text-muted small">Stock is reserved. Generate the picking slip for warehouse staff.</p>
              <form method="POST" action="{{ route('peams.orders.generate-picking-slip', $order) }}">
                @csrf
                <button class="btn btn-primary btn-block"><i class="fas fa-clipboard-list mr-1"></i> Generate Picking Slip</button>
              </form>
            @elseif($order->picking_slip_status === \App\Order::PICKING_GENERATED)
              <p class="text-muted small">Picking slip is ready. Open it, pick the items, then mark as picked.</p>
              <a href="{{ route('peams.orders.picking-slip', $order) }}" class="btn btn-outline-primary btn-block mb-2"><i class="fas fa-print mr-1"></i> View Picking Slip</a>
              <form method="POST" action="{{ route('peams.orders.mark-picked', $order) }}">
                @csrf
                <button class="btn btn-success btn-block"><i class="fas fa-check mr-1"></i> Mark Items Picked</button>
              </form>
            @elseif($order->status === \App\Order::STATUS_PENDING_HANDOFF)
              <p class="text-muted small">Items are picked. Assign a driver and dispatch the order.</p>
              <form method="POST" action="{{ route('peams.orders.dispatch', $order) }}">
                @csrf
                <div class="form-group"><label>Driver</label>
                  <select name="driver_id" class="form-control">
                    <option value="">Unassigned</option>
                    @foreach(\App\Employee::where('designation', 'Delivery Driver')->get() as $driver)
                      <option value="{{ $driver->id }}" @if((int) old('driver_id') === (int) $driver->id) selected @endif>{{ $driver->full_name }}</option>
                    @endforeach
                  </select>
                </div>
                <button class="btn btn-primary btn-block"><i class="fas fa-truck mr-1"></i> Dispatch Order</button>
              </form>
            @elseif($order->status === \App\Order::STATUS_IN_TRANSIT)
              <p class="text-muted small mb-0">Order is in transit. Capture the digital proof of delivery on the left once the customer receives it.</p>
            @endif
          </div>
        </div>

        <div class="card peams-card">
          <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-clock text-primary mr-2"></i> Timeline</h5></div>
          <div class="card-body">
            <div class="peams-summary-row"><span class="text-muted small">Stock reserved</span><strong class="small">{{ $order->reserved_at ? $order->reserved_at->format('M d, h:i A') : '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Picking slip</span><strong class="small">{{ $order->picking_slip_generated_at ? $order->picking_slip_generated_at->format('M d, h:i A') : '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Picked</span><strong class="small">{{ $order->picked_at ? $order->picked_at->format('M d, h:i A') : '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Dispatched</span><strong class="small">{{ $order->dispatched_at ? $order->dispatched_at->format('M d, h:i A') : '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Delivered</span><strong class="small">{{ $order->delivered_at ? $order->delivered_at->format('M d, h:i A') : '—' }}</strong></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
