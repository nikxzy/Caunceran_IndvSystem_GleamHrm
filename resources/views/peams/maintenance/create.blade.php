@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Log Repair Incident</h3>
        <p class="text-muted small mb-0">Standardized incident entry for damaged packaging or broken store machinery</p>
      </div>
      <a href="{{ route('peams.maintenance.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Maintenance</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <form method="POST" action="{{ route('peams.maintenance.store') }}">
      @csrf
      <div class="card peams-card">
        <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-tools text-primary mr-2"></i> Incident Details</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 form-group"><label>Asset / item *</label>
              <select name="item_id" class="form-control" required>
                <option value="">Select asset</option>
                @foreach($assets as $asset)<option value="{{ $asset->id }}" @if(old('item_id') == $asset->id) selected @endif>{{ $asset->name }} ({{ $asset->sku }})</option>@endforeach
              </select>
            </div>
            <div class="col-md-6 form-group"><label>Hub *</label>
              <select name="hub_id" class="form-control" required>
                @foreach($hubs as $hub)<option value="{{ $hub->id }}" @if(old('hub_id') == $hub->id) selected @endif>{{ $hub->name }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-4 form-group"><label>Log type *</label>
              <select name="type" class="form-control" required>
                @foreach(\App\MaintenanceLog::$types as $value => $label)<option value="{{ $value }}" @if(old('type', 'incident') === $value) selected @endif>{{ $label }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-4 form-group"><label>Assign technician</label>
              <select name="assigned_to" class="form-control">
                <option value="">Unassigned</option>
                @foreach($technicians as $technician)<option value="{{ $technician->id }}" @if(old('assigned_to') == $technician->id) selected @endif>{{ $technician->full_name }} — {{ $technician->designation }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-4 form-group"><label>Scheduled service date</label><input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}"></div>
            <div class="col-12 form-group"><label>Description of damage / repair needed *</label><textarea name="description" class="form-control" rows="4" required placeholder="Describe the fault, affected stock, or damaged packaging...">{{ old('description') }}</textarea></div>
          </div>
          <div class="alert alert-info mb-0"><i class="fas fa-info-circle mr-1"></i> Repair costs are captured when the technician marks the job completed so accounting receives an adjusted cost log.</div>
        </div>
        <div class="card-footer bg-white text-right">
          <a href="{{ route('peams.maintenance.index') }}" class="btn btn-light">Cancel</a>
          <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Save Incident Log</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
