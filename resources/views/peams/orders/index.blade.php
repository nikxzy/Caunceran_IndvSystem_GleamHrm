@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Order Assignment &amp; Check-out</h3>
        <p class="text-muted small mb-0">Real-time stock reservation, picking, driver handoff, and digital proof of delivery</p>
      </div>
      <a href="{{ route('peams.orders.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New Sales Order</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row mb-3">
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-hourglass-half"></i></div>
          <div><span class="peams-stat-label">Pending Handoff</span><span class="peams-stat-value">{{ \App\Order::where('status', \App\Order::STATUS_PENDING_HANDOFF)->count() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-truck"></i></div>
          <div><span class="peams-stat-label">In Transit</span><span class="peams-stat-value">{{ \App\Order::where('status', \App\Order::STATUS_IN_TRANSIT)->count() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#d1fae5;color:#047857;"><i class="fas fa-check-double"></i></div>
          <div><span class="peams-stat-label">Delivered</span><span class="peams-stat-value">{{ \App\Order::where('status', \App\Order::STATUS_DELIVERED)->count() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#e2e8f0;color:#475569;"><i class="fas fa-ban"></i></div>
          <div><span class="peams-stat-label">Cancelled</span><span class="peams-stat-value">{{ \App\Order::where('status', \App\Order::STATUS_CANCELLED)->count() }}</span></div>
        </div>
      </div>
    </div>

    <div class="card peams-card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.75rem;">
        <h5 class="card-title m-0"><i class="fas fa-file-invoice text-primary mr-2"></i> Sales Orders</h5>
        <form method="GET" class="d-flex flex-wrap" style="gap:.5rem;">
          <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(\App\Order::$statuses as $value => $label)
              <option value="{{ $value }}" @if($status === $value) selected @endif>{{ $label }}</option>
            @endforeach
          </select>
          <select name="hub_id" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All hubs</option>
            @foreach($hubs as $hub)
              <option value="{{ $hub->id }}" @if((int)$hubId === (int)$hub->id) selected @endif>{{ $hub->name }}</option>
            @endforeach
          </select>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead>
              <tr>
                <th>Order #</th><th>Client</th><th>Hub</th><th>Picking</th>
                <th class="text-right">Total</th><th>Status</th><th>Reserved</th><th class="text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                <tr>
                  <td>
                    <a href="{{ route('peams.orders.show', $order) }}" class="font-weight-bold text-primary">{{ $order->order_number }}</a>
                    <small class="d-block text-muted">{{ $order->created_at ? $order->created_at->format('M d, Y') : '—' }}</small>
                  </td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ optional($order->client)->name ?? '—' }}</span>
                    <small class="text-muted">{{ optional($order->client)->contact_person ?? '' }}</small>
                  </td>
                  <td class="small text-muted">{{ optional($order->hub)->name ?? '—' }}</td>
                  <td><span class="badge badge-soft-{{ $order->picking_badge }}">{{ $order->picking_slip_status }}</span></td>
                  <td class="text-right font-weight-bold">₱{{ number_format($order->total_amount, 2) }}</td>
                  <td><span class="badge badge-soft-{{ $order->status_badge }}">{{ $order->status }}</span></td>
                  <td>
                    @if($order->isReserved())
                      <span class="badge badge-soft-success"><i class="fas fa-lock mr-1"></i>Reserved</span>
                    @else
                      <span class="badge badge-soft-secondary">Unreserved</span>
                    @endif
                  </td>
                  <td class="text-right"><a href="{{ route('peams.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No sales orders found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($orders->hasPages())<div class="card-footer bg-white border-0">{{ $orders->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
