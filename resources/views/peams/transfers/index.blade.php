@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Inter-Branch Stock Movement</h3>
        <p class="text-muted small mb-0">Real-time transfers between Bongabong Central Hub and Roxas Branch Office</p>
      </div>
      <a href="{{ route('peams.transfers.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New Transfer</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row mb-3">
      <div class="col-6 col-lg-4 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-file-alt"></i></div>
          <div><span class="peams-stat-label">Requested</span><span class="peams-stat-value">{{ \App\StockTransfer::where('status', \App\StockTransfer::STATUS_REQUESTED)->count() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-4 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-truck-moving"></i></div>
          <div><span class="peams-stat-label">In Transit</span><span class="peams-stat-value">{{ \App\StockTransfer::where('status', \App\StockTransfer::STATUS_IN_TRANSIT)->count() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-4 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#d1fae5;color:#047857;"><i class="fas fa-check-double"></i></div>
          <div><span class="peams-stat-label">Received</span><span class="peams-stat-value">{{ \App\StockTransfer::where('status', \App\StockTransfer::STATUS_RECEIVED)->count() }}</span></div>
        </div>
      </div>
    </div>

    <div class="card peams-card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="card-title m-0"><i class="fas fa-exchange-alt text-primary mr-2"></i> Transfer Ledger</h5>
        <form method="GET">
          <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(\App\StockTransfer::$statuses as $value => $label)
              <option value="{{ $value }}" @if($status === $value) selected @endif>{{ $label }}</option>
            @endforeach
          </select>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead><tr><th>Transfer #</th><th>From</th><th>To</th><th>Items</th><th>Status</th><th>Requested by</th><th>Date</th><th class="text-right">Action</th></tr></thead>
            <tbody>
              @forelse($transfers as $transfer)
                <tr>
                  <td><a href="{{ route('peams.transfers.show', $transfer) }}" class="font-weight-bold text-primary">{{ $transfer->transfer_number }}</a></td>
                  <td><span class="small font-weight-bold">{{ optional($transfer->fromHub)->name ?? '—' }}</span></td>
                  <td><span class="small font-weight-bold">{{ optional($transfer->toHub)->name ?? '—' }}</span></td>
                  <td><span class="badge badge-soft-secondary">{{ $transfer->items->sum('quantity') }} units / {{ $transfer->items->count() }} lines</span></td>
                  <td><span class="badge badge-soft-{{ $transfer->status_badge }}">{{ $transfer->status }}</span></td>
                  <td class="small text-muted">{{ optional($transfer->requester)->full_name ?? '—' }}</td>
                  <td class="small text-muted">{{ $transfer->created_at ? $transfer->created_at->format('M d, Y') : '—' }}</td>
                  <td class="text-right"><a href="{{ route('peams.transfers.show', $transfer) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No stock transfers recorded yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($transfers->hasPages())<div class="card-footer bg-white border-0">{{ $transfers->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
