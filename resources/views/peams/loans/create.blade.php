@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">New Equipment Loan</h3>
        <p class="text-muted small mb-0">Check out a loaner machine and record the expected return date</p>
      </div>
      <a href="{{ route('peams.loans.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Loan Tracker</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <form method="POST" action="{{ route('peams.loans.store') }}">
      @csrf
      <div class="card peams-card">
        <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-tools text-primary mr-2"></i> Loan Details</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 form-group"><label>Hub *</label>
              <select name="hub_id" class="form-control" required>
                @foreach($hubs as $hub)<option value="{{ $hub->id }}">{{ $hub->name }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-8 form-group"><label>Loanable equipment *</label>
              <select name="equipment_id" class="form-control" required>
                <option value="">Select equipment</option>
                @foreach($equipment as $asset)
                  <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->sku }})</option>
                @endforeach
              </select>
              @if($equipment->isEmpty())
                <small class="text-danger">No loanable equipment is currently cataloged. Mark an equipment item as loanable in inventory.</small>
              @endif
            </div>
            <div class="col-md-6 form-group"><label>Client account</label>
              <select name="client_id" class="form-control">
                <option value="">Walk-in / not listed</option>
                @foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-6 form-group"><label>Borrower name *</label><input name="client_name" class="form-control" required value="{{ old('client_name') }}" placeholder="Store owner or authorized borrower"></div>
            <div class="col-md-4 form-group"><label>Borrow date *</label><input type="date" name="borrow_date" class="form-control" required value="{{ old('borrow_date', now()->toDateString()) }}"></div>
            <div class="col-md-4 form-group"><label>Expected return *</label><input type="date" name="expected_return_date" class="form-control" required value="{{ old('expected_return_date', now()->addDays(7)->toDateString()) }}"></div>
            <div class="col-md-4 form-group"><label>Condition on check-out</label><input name="condition_out" class="form-control" value="{{ old('condition_out') }}" placeholder="Working, with accessories..."></div>
            <div class="col-12 form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Accessories included, agreed return arrangement...">{{ old('notes') }}</textarea></div>
          </div>
        </div>
        <div class="card-footer bg-white text-right">
          <a href="{{ route('peams.loans.index') }}" class="btn btn-light">Cancel</a>
          <button class="btn btn-primary" type="submit" @if($equipment->isEmpty()) disabled @endif><i class="fas fa-sign-out-alt mr-1"></i> Check Out Equipment</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
