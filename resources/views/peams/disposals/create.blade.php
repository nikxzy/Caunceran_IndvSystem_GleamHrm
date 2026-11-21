@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Record Asset Disposal</h3>
        <p class="text-muted small mb-0">Write off defective packaging or retire irreparable machinery with a full audit trail</p>
      </div>
      <a href="{{ route('peams.disposals.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Disposal Log</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <form method="POST" action="{{ route('peams.disposals.store') }}">
      @csrf
      <div class="card peams-card">
        <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-dumpster text-primary mr-2"></i> Disposal Details</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 form-group"><label>Item *</label>
              <select name="item_id" class="form-control" required>
                <option value="">Select item</option>
                @foreach($items as $item)
                  @php $row = $stock->firstWhere('item_id', $item->id); @endphp
                  <option value="{{ $item->id }}" @if(old('item_id') == $item->id) selected @endif>{{ $item->name }} ({{ $item->sku }}) — on hand {{ $row ? $row->quantity : 0 }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 form-group"><label>Hub *</label>
              <select name="hub_id" class="form-control" required>
                @foreach($hubs as $hub)<option value="{{ $hub->id }}" @if((int)$hubId === (int)$hub->id) selected @endif>{{ $hub->name }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-3 form-group"><label>Quantity *</label><input type="number" min="1" name="quantity" class="form-control" required value="{{ old('quantity', 1) }}"></div>
            <div class="col-md-3 form-group"><label>Disposal type *</label>
              <select name="type" class="form-control" required>
                @foreach(\App\AssetDisposal::$types as $value => $label)<option value="{{ $value }}" @if(old('type') === $value) selected @endif>{{ $label }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-3 form-group"><label>Estimated value (₱)</label><input type="number" step="0.01" min="0" name="estimated_value" class="form-control" value="{{ old('estimated_value', 0) }}"></div>
            <div class="col-md-3 form-group"><label>Disposal method</label><input name="disposal_method" class="form-control" value="{{ old('disposal_method') }}" placeholder="Scrap, return to supplier, donate..."></div>
            <div class="col-12 form-group"><label>Reason *</label><textarea name="reason" class="form-control" rows="3" required placeholder="Describe the damage or obsolescence and why the asset cannot be recovered...">{{ old('reason') }}</textarea></div>
          </div>
          <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Saving this record immediately deducts the quantity from hub stock and writes a disposal movement to the ledger.</div>
        </div>
        <div class="card-footer bg-white text-right">
          <a href="{{ route('peams.disposals.index') }}" class="btn btn-light">Cancel</a>
          <button class="btn btn-danger" type="submit"><i class="fas fa-dumpster mr-1"></i> Record Disposal</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
