@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Picking Slip</h3>
        <p class="text-muted small mb-0">{{ $order->order_number }} &middot; {{ optional($order->hub)->name }}</p>
      </div>
      <div class="d-flex" style="gap:.5rem;">
        <a href="{{ route('peams.orders.show', $order) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Order</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print mr-1"></i> Print</button>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div class="card peams-card">
      <div class="card-header" style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border-radius:14px 14px 0 0;">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="mb-1 font-weight-bold text-white">Mindoro Pack &amp; Pour Warehousing Solutions</h4>
            <small class="text-white-50">Bongabong Central Hub &amp; Roxas Branch Office</small>
          </div>
          <div class="text-right">
            <div class="font-weight-bold">{{ $order->order_number }}</div>
            <small class="text-white-50">{{ $order->created_at ? $order->created_at->format('M d, Y') : '' }}</small>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-4 mb-2"><span class="text-muted small d-block">Client</span><strong>{{ optional($order->client)->name }}</strong></div>
          <div class="col-md-4 mb-2"><span class="text-muted small d-block">Contact</span><strong>{{ optional($order->client)->contact_person ?? '—' }}</strong></div>
          <div class="col-md-4 mb-2"><span class="text-muted small d-block">Fulfilling hub</span><strong>{{ optional($order->hub)->name }}</strong></div>
          <div class="col-md-8 mb-2"><span class="text-muted small d-block">Delivery address</span><strong>{{ $order->delivery_address ?: '—' }}</strong></div>
          <div class="col-md-4 mb-2"><span class="text-muted small d-block">Picked by</span><strong>____________________</strong></div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="thead-light"><tr><th style="width:40px;">✓</th><th>Item</th><th>SKU</th><th>Location</th><th class="text-right">Qty to Pick</th><th class="text-right">Picked</th></tr></thead>
            <tbody>
              @foreach($order->items as $line)
                <tr>
                  <td class="text-center"><input type="checkbox"></td>
                  <td class="font-weight-bold">{{ optional($line->item)->name ?? '—' }}</td>
                  <td><code>{{ optional($line->item)->sku ?? '—' }}</code></td>
                  <td>{{ optional($order->hub)->name }}</td>
                  <td class="text-right font-weight-bold">{{ number_format($line->quantity) }}</td>
                  <td style="width:120px;"></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="row mt-4">
          <div class="col-md-6"><span class="text-muted small d-block">Picker signature</span><div class="border-bottom mt-4"></div></div>
          <div class="col-md-6"><span class="text-muted small d-block">Supervisor signature</span><div class="border-bottom mt-4"></div></div>
        </div>

        <div class="mt-4 text-right">
          @if($order->picking_slip_status === \App\Order::PICKING_GENERATED)
            <form method="POST" action="{{ route('peams.orders.mark-picked', $order) }}">
              @csrf
              <button class="btn btn-success"><i class="fas fa-check mr-1"></i> Mark Items Picked</button>
            </form>
          @elseif($order->picking_slip_status === \App\Order::PICKING_PICKED)
            <span class="badge badge-soft-success p-2"><i class="fas fa-check mr-1"></i>Items picked — ready for driver handoff</span>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>@media print { .main-header, .main-sidebar, .main-footer, .content-header { display: none !important; } .content-wrapper { background: #fff !important; } }</style>
@endpush
