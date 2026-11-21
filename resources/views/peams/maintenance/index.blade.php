@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Maintenance, Repair &amp; Incidents</h3>
        <p class="text-muted small mb-0">Preventive service schedule and standardized repair incident log with cost tracking</p>
      </div>
      <div class="d-flex flex-wrap" style="gap:.5rem;">
        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#schedulePreventiveModal"><i class="fas fa-calendar-plus mr-1"></i> Schedule Preventive</button>
        <a href="{{ route('peams.maintenance.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Log Incident</a>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row mb-3">
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-wrench"></i></div>
          <div><span class="peams-stat-label">Open Jobs</span><span class="peams-stat-value">{{ $openIncidents }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fas fa-clock"></i></div>
          <div><span class="peams-stat-label">Overdue</span><span class="peams-stat-value">{{ $overdue }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-calendar-check"></i></div>
          <div><span class="peams-stat-label">Service Due (7d)</span><span class="peams-stat-value">{{ $dueAssets->count() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#d1fae5;color:#047857;"><i class="fas fa-peso-sign"></i></div>
          <div><span class="peams-stat-label">Repair Cost</span><span class="peams-stat-value">₱{{ number_format($logs->sum('cost'), 0) }}</span></div>
        </div>
      </div>
    </div>

    @if($dueAssets->count())
      <div class="card peams-card mb-4">
        <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-bell text-warning mr-2"></i> Preventive Maintenance Due</h5></div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern mb-0">
              <thead><tr><th>Machine</th><th>SKU</th><th>Last Serviced</th><th>Next Service</th><th class="text-right">Action</th></tr></thead>
              <tbody>
                @foreach($dueAssets as $asset)
                  <tr>
                    <td class="font-weight-bold">{{ $asset->name }}</td>
                    <td><code>{{ $asset->sku }}</code></td>
                    <td class="small text-muted">{{ $asset->last_serviced_at ? $asset->last_serviced_at->format('M d, Y') : 'Never' }}</td>
                    <td><span class="badge badge-soft-warning">{{ $asset->next_service_at ? $asset->next_service_at->format('M d, Y') : '—' }}</span></td>
                    <td class="text-right">
                      <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#schedulePreventiveModal" data-item="{{ $asset->id }}">Schedule</button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif

    <div class="card peams-card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
        <h5 class="card-title m-0"><i class="fas fa-tools text-primary mr-2"></i> Maintenance &amp; Incident Log</h5>
        <form method="GET" class="d-flex flex-wrap" style="gap:.5rem;">
          <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(\App\MaintenanceLog::$statuses as $value => $label)<option value="{{ $value }}" @if($status === $value) selected @endif>{{ $label }}</option>@endforeach
          </select>
          <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All types</option>
            @foreach(\App\MaintenanceLog::$types as $value => $label)<option value="{{ $value }}" @if($type === $value) selected @endif>{{ $label }}</option>@endforeach
          </select>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead><tr><th>Asset</th><th>Type</th><th>Hub</th><th>Scheduled</th><th>Technician</th><th class="text-right">Cost</th><th>Status</th><th class="text-right">Action</th></tr></thead>
            <tbody>
              @forelse($logs as $log)
                <tr>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ optional($log->item)->name ?? '—' }}</span>
                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($log->description, 56) }}</small>
                  </td>
                  <td><span class="badge badge-soft-{{ $log->type === 'preventive' ? 'primary' : ($log->type === 'incident' ? 'danger' : 'warning') }}">{{ ucfirst($log->type) }}</span></td>
                  <td class="small text-muted">{{ optional($log->hub)->name ?? '—' }}</td>
                  <td class="small {{ $log->isDue() ? 'text-danger font-weight-bold' : 'text-muted' }}">{{ $log->scheduled_date ? $log->scheduled_date->format('M d, Y') : '—' }}</td>
                  <td class="small">{{ optional($log->assignee)->full_name ?? 'Unassigned' }}</td>
                  <td class="text-right small">₱{{ number_format($log->cost, 2) }}</td>
                  <td><span class="badge badge-soft-{{ $log->status_badge }}">{{ $log->status }}</span></td>
                  <td class="text-right">
                    <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#updateLog{{ $log->id }}">Update</button>
                  </td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No maintenance or incident logs found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($logs->hasPages())<div class="card-footer bg-white border-0">{{ $logs->links() }}</div>@endif
    </div>
  </div>
</div>

{{-- Schedule preventive --}}
<div class="modal fade" id="schedulePreventiveModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('peams.maintenance.schedule-preventive') }}">
      @csrf
      <div class="modal-header"><h5 class="modal-title"><i class="fas fa-calendar-plus text-primary mr-2"></i> Schedule Preventive Maintenance</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group"><label>Machine *</label>
          <select name="item_id" id="preventiveItem" class="form-control" required>
            @foreach($dueAssets as $asset)<option value="{{ $asset->id }}">{{ $asset->name }}</option>@endforeach
            @if($dueAssets->isEmpty())
              @foreach(\App\Item::equipment()->active()->get() as $asset)<option value="{{ $asset->id }}">{{ $asset->name }}</option>@endforeach
            @endif
          </select>
        </div>
        <div class="form-group"><label>Hub *</label>
          <select name="hub_id" class="form-control" required>
            @foreach(\App\Hub::active()->orderBy('name')->get() as $hub)<option value="{{ $hub->id }}">{{ $hub->name }}</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>Service date *</label><input type="date" name="scheduled_date" class="form-control" required value="{{ now()->addDays(7)->toDateString() }}"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Schedule</button></div>
    </form>
  </div>
</div>

@foreach($logs as $log)
  <div class="modal fade" id="updateLog{{ $log->id }}" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('peams.maintenance.status', $log) }}">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Update Log #{{ $log->id }}</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
          <p class="small text-muted">{{ optional($log->item)->name }} &middot; reported by {{ optional($log->reporter)->full_name ?? '—' }}</p>
          <div class="form-group"><label>Status *</label>
            <select name="status" class="form-control" required>
              @foreach(\App\MaintenanceLog::$statuses as $value => $label)<option value="{{ $value }}" @if($log->status === $value) selected @endif>{{ $label }}</option>@endforeach
            </select>
          </div>
          <div class="form-group"><label>Repair / service cost (₱)</label><input type="number" step="0.01" min="0" name="cost" class="form-control" value="{{ $log->cost }}"></div>
          <div class="form-group"><label>Completion date</label><input type="date" name="completed_date" class="form-control" value="{{ $log->completed_date ? $log->completed_date->toDateString() : '' }}"></div>
          <div class="form-group"><label>Resolution notes</label><textarea name="resolution_notes" class="form-control" rows="3">{{ $log->resolution_notes }}</textarea></div>
        </div>
        <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Save Update</button></div>
      </form>
    </div>
  </div>
@endforeach
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  $('#schedulePreventiveModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    if (button && button.data('item')) { $('#preventiveItem').val(button.data('item')); }
  });
});
</script>
@endsection
