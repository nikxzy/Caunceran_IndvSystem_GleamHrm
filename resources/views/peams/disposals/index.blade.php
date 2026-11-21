@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Digital Asset Disposal Log</h3>
        <p class="text-muted small mb-0">Retirement registry for irreparable machinery and defective packaging</p>
      </div>
      <a href="{{ route('peams.disposals.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Record Disposal</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row mb-3">
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#e2e8f0;color:#475569;"><i class="fas fa-dumpster"></i></div>
          <div><span class="peams-stat-label">Disposals</span><span class="peams-stat-value">{{ $stats['total'] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fas fa-box-open"></i></div>
          <div><span class="peams-stat-label">Damaged Packaging</span><span class="peams-stat-value">{{ $stats['packaging'] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-industry"></i></div>
          <div><span class="peams-stat-label">Scrap Machinery</span><span class="peams-stat-value">{{ $stats['machinery'] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#d1fae5;color:#047857;"><i class="fas fa-peso-sign"></i></div>
          <div><span class="peams-stat-label">Written-off Value</span><span class="peams-stat-value">₱{{ number_format($stats['value'], 0) }}</span></div>
        </div>
      </div>
    </div>

    <div class="card peams-card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
        <h5 class="card-title m-0"><i class="fas fa-clipboard-list text-primary mr-2"></i> Disposal Registry</h5>
        <form method="GET" class="d-flex flex-wrap" style="gap:.5rem;">
          <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All types</option>
            @foreach(\App\AssetDisposal::$types as $value => $label)<option value="{{ $value }}" @if($type === $value) selected @endif>{{ $label }}</option>@endforeach
          </select>
          <select name="hub_id" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All hubs</option>
            @foreach($hubs as $hub)<option value="{{ $hub->id }}" @if((int)$hubId === (int)$hub->id) selected @endif>{{ $hub->name }}</option>@endforeach
          </select>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead><tr><th>Reference</th><th>Item</th><th>Hub</th><th class="text-right">Qty</th><th>Type</th><th>Reason</th><th class="text-right">Value</th><th>Disposed</th></tr></thead>
            <tbody>
              @forelse($disposals as $disposal)
                <tr>
                  <td class="font-weight-bold text-primary">{{ $disposal->reference_no }}</td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ optional($disposal->item)->name ?? '—' }}</span>
                    <code>{{ optional($disposal->item)->sku ?? '—' }}</code>
                  </td>
                  <td class="small text-muted">{{ optional($disposal->hub)->name ?? '—' }}</td>
                  <td class="text-right font-weight-bold">{{ number_format($disposal->quantity) }}</td>
                  <td><span class="badge badge-soft-{{ $disposal->type === 'damaged_packaging' ? 'warning' : 'danger' }}">{{ \App\AssetDisposal::$types[$disposal->type] ?? $disposal->type }}</span></td>
                  <td class="small text-muted">{{ \Illuminate\Support\Str::limit($disposal->reason, 60) }}</td>
                  <td class="text-right small">₱{{ number_format($disposal->estimated_value, 2) }}</td>
                  <td class="small text-muted">{{ $disposal->disposed_at ? $disposal->disposed_at->format('M d, Y') : '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No asset disposals recorded yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($disposals->hasPages())<div class="card-footer bg-white border-0">{{ $disposals->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
