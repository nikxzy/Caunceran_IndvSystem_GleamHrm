@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">{{ $transfer->transfer_number }}</h3>
        <p class="text-muted small mb-0">{{ optional($transfer->fromHub)->name }} <i class="fas fa-arrow-right mx-1"></i> {{ optional($transfer->toHub)->name }}</p>
      </div>
      <a href="{{ route('peams.transfers.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Transfers</a>
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
            <h5 class="card-title m-0"><i class="fas fa-boxes text-primary mr-2"></i> Transfer Items</h5>
            <span class="badge badge-soft-{{ $transfer->status_badge }}">{{ $transfer->status }}</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-modern mb-0">
                <thead><tr><th>Item</th><th>SKU</th><th class="text-right">Quantity</th></tr></thead>
                <tbody>
                  @foreach($transfer->items as $line)
                    <tr>
                      <td class="font-weight-bold">{{ optional($line->item)->name ?? '—' }}</td>
                      <td><code>{{ optional($line->item)->sku ?? '—' }}</code></td>
                      <td class="text-right font-weight-bold">{{ number_format($line->quantity) }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot><tr><td colspan="2" class="text-right font-weight-bold">Total units</td><td class="text-right font-weight-bold text-primary">{{ number_format($transfer->items->sum('quantity')) }}</td></tr></tfoot>
              </table>
            </div>
          </div>
        </div>

        @if($transfer->notes)
          <div class="card peams-card mb-4">
            <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-sticky-note text-primary mr-2"></i> Notes</h5></div>
            <div class="card-body">{{ $transfer->notes }}</div>
          </div>
        @endif
      </div>

      <div class="col-lg-4">
        <div class="card peams-card mb-4">
          <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-tasks text-primary mr-2"></i> Movement Control</h5></div>
          <div class="card-body">
            @if($transfer->isRequested())
              <p class="text-muted small">Confirm that the stock has left {{ optional($transfer->fromHub)->name }}. Source stock is deducted immediately.</p>
              <form method="POST" action="{{ route('peams.transfers.dispatch', $transfer) }}">
                @csrf
                <button class="btn btn-primary btn-block"><i class="fas fa-truck mr-1"></i> Dispatch Transfer</button>
              </form>
            @elseif($transfer->isInTransit())
              <p class="text-muted small">Confirm arrival at {{ optional($transfer->toHub)->name }}. Destination stock is added immediately.</p>
              <form method="POST" action="{{ route('peams.transfers.receive', $transfer) }}">
                @csrf
                <button class="btn btn-success btn-block"><i class="fas fa-check mr-1"></i> Receive at Hub</button>
              </form>
            @else
              <p class="text-success mb-0"><i class="fas fa-check-circle mr-1"></i>Transfer completed. Both hub stock records are reconciled.</p>
            @endif
          </div>
        </div>

        <div class="card peams-card">
          <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-clock text-primary mr-2"></i> Timeline</h5></div>
          <div class="card-body">
            <div class="peams-summary-row"><span class="text-muted small">Requested by</span><strong class="small">{{ optional($transfer->requester)->full_name ?? '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Requested at</span><strong class="small">{{ $transfer->created_at ? $transfer->created_at->format('M d, h:i A') : '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Dispatched</span><strong class="small">{{ $transfer->dispatched_at ? $transfer->dispatched_at->format('M d, h:i A') : '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Received by</span><strong class="small">{{ optional($transfer->receiver)->full_name ?? '—' }}</strong></div>
            <div class="peams-summary-row"><span class="text-muted small">Received at</span><strong class="small">{{ $transfer->received_at ? $transfer->received_at->format('M d, h:i A') : '—' }}</strong></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
