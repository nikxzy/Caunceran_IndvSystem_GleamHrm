@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">{{ $purchaseOrder->po_number }}</h3>
        <p class="text-muted small mb-0">{{ optional($purchaseOrder->vendor)->company_name ?? 'Unassigned vendor' }} &middot; {{ optional($purchaseOrder->hub)->name }}</p>
      </div>
      <div class="d-flex flex-wrap" style="gap:.5rem;">
        <a href="{{ route('peams.purchase-orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Purchase Orders</a>
        @if($purchaseOrder->isPending())
          <form method="POST" action="{{ route('peams.purchase-orders.approve', $purchaseOrder) }}">
            @csrf
            <button class="btn btn-primary"><i class="fas fa-stamp mr-1"></i> Approve PO</button>
          </form>
        @elseif($purchaseOrder->isApproved())
          <form method="POST" action="{{ route('peams.purchase-orders.receive', $purchaseOrder) }}" onsubmit="return confirm('Receive shipment and add stock to the hub?');">
            @csrf
            <button class="btn btn-success"><i class="fas fa-truck-loading mr-1"></i> Receive Shipment</button>
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
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0"><i class="fas fa-boxes text-primary mr-2"></i> PO Lines</h5>
            <span class="badge badge-soft-{{ $purchaseOrder->status_badge }}">{{ $purchaseOrder->status }}</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-modern mb-0">
                <thead><tr><th>Item</th><th>SKU</th><th class="text-right">Qty</th><th class="text-right">Unit Cost</th><th class="text-right">Line Total</th></tr></thead>
                <tbody>
                  @foreach($purchaseOrder->items as $line)
                    <tr>
                      <td class="font-weight-bold">{{ optional($line->item)->name ?? '—' }}</td>
                      <td><code>{{ optional($line->item)->sku ?? '—' }}</code></td>
                      <td class="text-right">{{ number_format($line->quantity) }}</td>
                      <td class="text-right">₱{{ number_format($line->unit_cost, 2) }}</td>
                      <td class="text-right font-weight-bold">₱{{ number_format($line->line_total, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot><tr><td colspan="4" class="text-right font-weight-bold">Total</td><td class="text-right font-weight-bold text-primary">₱{{ number_format($purchaseOrder->total_amount, 2) }}</td></tr></tfoot>
              </table>
            </div>
          </div>
        </div>

        @if($purchaseOrder->notes)
          <div class="card peams-card"><div class="card-header"><h5 class="card-title m-0">Notes</h5></div><div class="card-body">{{ $purchaseOrder->notes }}</div></div>
        @endif
      </div>

      <div class="col-lg-4">
        <div class="card peams-card mb-4">
          <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-info-circle text-primary mr-2"></i> PO Summary</h5></div>
          <div class="card-body">
            <div class="peams-summary-row"><span class="text-muted small">Vendor</span><strong class="small">{{ optional($purchaseOrder->vendor)->company_name ?? '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Hub</span><strong class="small">{{ optional($purchaseOrder->hub)->name ?? '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Order date</span><strong class="small">{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('M d, Y') : '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Expected</span><strong class="small">{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('M d, Y') : '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Requested by</span><strong class="small">{{ optional($purchaseOrder->requester)->full_name ?? '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Approved by</span><strong class="small">{{ optional($purchaseOrder->approver)->full_name ?? 'Pending' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Received at</span><strong class="small">{{ $purchaseOrder->received_at ? $purchaseOrder->received_at->format('M d, Y h:i A') : '—' }}</strong></div>
          </div>
        </div>

        <div class="card peams-card">
          <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-route text-primary mr-2"></i> Approval Trail</h5></div>
          <div class="card-body">
            <div class="peams-summary-row">
              <span class="small">Submitted</span>
              <span class="badge badge-soft-success"><i class="fas fa-check mr-1"></i>Done</span>
            </div>
            <div class="peams-summary-row">
              <span class="small">Approved</span>
              @if($purchaseOrder->approved_at)
                <span class="badge badge-soft-success">{{ $purchaseOrder->approved_at->format('M d, h:i A') }}</span>
              @else
                <span class="badge badge-soft-warning">Pending</span>
              @endif
            </div>
            <div class="peams-summary-row">
              <span class="small">Stock received</span>
              @if($purchaseOrder->received_at)
                <span class="badge badge-soft-success">{{ $purchaseOrder->received_at->format('M d, h:i A') }}</span>
              @else
                <span class="badge badge-soft-secondary">Not yet</span>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
