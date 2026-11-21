@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Equipment Loan Tracker</h3>
        <p class="text-muted small mb-0">Digital check-out and check-in logbook for loaner machines and store equipment</p>
      </div>
      <a href="{{ route('peams.loans.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New Equipment Loan</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row mb-3">
      <div class="col-6 col-lg-4 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-tools"></i></div>
          <div><span class="peams-stat-label">On Loan</span><span class="peams-stat-value">{{ \App\EquipmentLoan::where('status', \App\EquipmentLoan::STATUS_ON_LOAN)->count() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-4 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fas fa-exclamation-circle"></i></div>
          <div><span class="peams-stat-label">Overdue</span><span class="peams-stat-value">{{ \App\EquipmentLoan::where('status', \App\EquipmentLoan::STATUS_OVERDUE)->count() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-4 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#d1fae5;color:#047857;"><i class="fas fa-check"></i></div>
          <div><span class="peams-stat-label">Returned</span><span class="peams-stat-value">{{ \App\EquipmentLoan::where('status', \App\EquipmentLoan::STATUS_RETURNED)->count() }}</span></div>
        </div>
      </div>
    </div>

    <div class="card peams-card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="card-title m-0"><i class="fas fa-clipboard-list text-primary mr-2"></i> Loan Logbook</h5>
        <form method="GET">
          <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(\App\EquipmentLoan::$statuses as $value => $label)
              <option value="{{ $value }}" @if($status === $value) selected @endif>{{ $label }}</option>
            @endforeach
          </select>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead><tr><th>Equipment</th><th>Borrower</th><th>Hub</th><th>Borrowed</th><th>Due Back</th><th>Condition Out</th><th>Status</th><th class="text-right">Action</th></tr></thead>
            <tbody>
              @forelse($loans as $loan)
                <tr>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ optional($loan->equipment)->name ?? '—' }}</span>
                    <code>{{ optional($loan->equipment)->sku ?? '—' }}</code>
                  </td>
                  <td>
                    <span class="font-weight-bold d-block">{{ $loan->client_name }}</span>
                    <small class="text-muted">{{ optional($loan->client)->phone ?? '' }}</small>
                  </td>
                  <td class="small text-muted">{{ optional($loan->hub)->name ?? '—' }}</td>
                  <td class="small">{{ $loan->borrow_date ? $loan->borrow_date->format('M d, Y') : '—' }}</td>
                  <td class="small">
                    <span class="{{ $loan->isOverdue() ? 'text-danger font-weight-bold' : '' }}">{{ $loan->expected_return_date ? $loan->expected_return_date->format('M d, Y') : '—' }}</span>
                  </td>
                  <td class="small text-muted">{{ $loan->condition_out ?? '—' }}</td>
                  <td><span class="badge badge-soft-{{ $loan->status_badge }}">{{ $loan->status }}</span></td>
                  <td class="text-right text-nowrap">
                    @if($loan->status !== \App\EquipmentLoan::STATUS_RETURNED)
                      <button class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#returnLoan{{ $loan->id }}"><i class="fas fa-undo mr-1"></i>Return</button>
                    @else
                      <span class="small text-muted">{{ $loan->actual_return_date ? $loan->actual_return_date->format('M d, Y') : 'Closed' }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No equipment loans recorded yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($loans->hasPages())<div class="card-footer bg-white border-0">{{ $loans->links() }}</div>@endif
    </div>
  </div>
</div>

@foreach($loans as $loan)
  @if($loan->status !== \App\EquipmentLoan::STATUS_RETURNED)
    <div class="modal fade" id="returnLoan{{ $loan->id }}" tabindex="-1">
      <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('peams.loans.return', $loan) }}">
          @csrf
          <div class="modal-header"><h5 class="modal-title">Return {{ optional($loan->equipment)->name }}</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
          <div class="modal-body">
            <p class="small text-muted">Borrower: <strong>{{ $loan->client_name }}</strong> &middot; Due {{ $loan->expected_return_date ? $loan->expected_return_date->format('M d, Y') : '—' }}</p>
            <div class="form-group"><label>Condition on return</label><input name="condition_in" class="form-control" placeholder="Good, needs cleaning, damaged..."></div>
            <div class="form-group"><label>Actual return date</label><input type="date" name="actual_return_date" class="form-control" value="{{ now()->toDateString() }}"></div>
          </div>
          <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Cancel</button><button class="btn btn-success" type="submit">Confirm Return</button></div>
        </form>
      </div>
    </div>
  @endif
@endforeach
@endsection
