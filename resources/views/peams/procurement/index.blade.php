@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Procurement &amp; Purchase Orders</h3>
        <p class="text-muted small mb-0">Digital PO approval workflow replacing email and chat approval chains</p>
      </div>
      <a href="{{ route('peams.purchase-orders.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New Purchase Order</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row mb-3">
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#e2e8f0;color:#475569;"><i class="fas fa-file-invoice"></i></div>
          <div><span class="peams-stat-label">All POs</span><span class="peams-stat-value">{{ $counts['all'] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-hourglass-half"></i></div>
          <div><span class="peams-stat-label">Pending</span><span class="peams-stat-value">{{ $counts[\App\PurchaseOrder::STATUS_PENDING] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-stamp"></i></div>
          <div><span class="peams-stat-label">Approved</span><span class="peams-stat-value">{{ $counts[\App\PurchaseOrder::STATUS_APPROVED] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#d1fae5;color:#047857;"><i class="fas fa-truck-loading"></i></div>
          <div><span class="peams-stat-label">Received</span><span class="peams-stat-value">{{ $counts[\App\PurchaseOrder::STATUS_RECEIVED] }}</span></div>
        </div>
      </div>
    </div>

    <div class="card peams-card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
        <h5 class="card-title m-0"><i class="fas fa-clipboard-check text-primary mr-2"></i> Purchase Orders</h5>
        <form method="GET">
          <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(\App\PurchaseOrder::$statuses as $value => $label)<option value="{{ $value }}" @if($status === $value) selected @endif>{{ $label }}</option>@endforeach
          </select>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead><tr><th>PO #</th><th>Vendor</th><th>Hub</th><th class="text-right">Total</th><th>Status</th><th>Requested by</th><th>Date</th><th class="text-right">Action</th></tr></thead>
            <tbody>
              @forelse($purchaseOrders as $po)
                <tr>
                  <td><a href="{{ route('peams.purchase-orders.show', $po) }}" class="font-weight-bold text-primary">{{ $po->po_number }}</a></td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ optional($po->vendor)->company_name ?? 'Unassigned vendor' }}</span>
                    <small class="text-muted">{{ optional($po->vendor)->contact_person ?? '' }}</small>
                  </td>
                  <td class="small text-muted">{{ optional($po->hub)->name ?? '—' }}</td>
                  <td class="text-right font-weight-bold">₱{{ number_format($po->total_amount, 2) }}</td>
                  <td><span class="badge badge-soft-{{ $po->status_badge }}">{{ $po->status }}</span></td>
                  <td class="small text-muted">{{ optional($po->requester)->full_name ?? '—' }}</td>
                  <td class="small text-muted">{{ $po->order_date ? $po->order_date->format('M d, Y') : '—' }}</td>
                  <td class="text-right"><a href="{{ route('peams.purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No purchase orders found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($purchaseOrders->hasPages())<div class="card-footer bg-white border-0">{{ $purchaseOrders->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
