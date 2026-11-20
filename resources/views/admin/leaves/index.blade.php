@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Leave Applications</h3>
        <p class="text-muted small mb-0">Review and manage employee leave filings</p>
      </div>
      <a href="{{ route('leaves') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus mr-1"></i> Apply for Leave
      </a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    @php
      $leaveList = $leaves ?? collect();
    @endphp

    <div class="card peams-card">
      <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">
          <i class="fas fa-calendar-alt text-primary mr-2"></i> Leave Records
        </h5>
        <span class="badge badge-soft-primary">{{ $leaveList->count() }} Total</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="leave-applications" class="table table-hover table-modern">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>Date From</th>
                <th>Date To</th>
                <th>Subject</th>
                <th class="text-center">Status</th>
                <th class="text-right" style="width: 150px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($leaveList as $leave)
                <tr>
                  <td>
                    <span class="font-weight-bold text-dark d-block">
                      {{ optional($leave->employee)->firstname }} {{ optional($leave->employee)->lastname }}
                    </span>
                    <small class="text-muted">Employee #{{ $leave->employee_id }}</small>
                  </td>
                  <td>
                    <span class="badge badge-soft-secondary">{{ optional($leave->leaveType)->name ?? 'N/A' }}</span>
                  </td>
                  <td><small class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($leave->datefrom)->format('M d, Y') }}</small></td>
                  <td><small class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($leave->dateto)->format('M d, Y') }}</small></td>
                  <td><small class="text-secondary">{{ $leave->subject ?? $leave->reason ?? '—' }}</small></td>
                  <td class="text-center">
                    @if(strtolower($leave->status) == 'approved')
                      <span class="badge badge-soft-success">Approved</span>
                    @elseif(strtolower($leave->status) == 'declined')
                      <span class="badge badge-soft-danger">Declined</span>
                    @else
                      <span class="badge badge-soft-warning">Pending</span>
                    @endif
                  </td>
                  <td class="text-right text-nowrap">
                    @if(Auth::user()->isAllowed('LeaveController:show'))
                      <a href="{{ route('leave.show', ['id' => $leave->id]) }}" class="btn btn-sm btn-outline-primary peams-action-btn mr-1" title="View Leave">
                        <i class="fas fa-eye"></i>
                      </a>
                    @endif
                    @if(strtolower($leave->status) == 'pending' || $leave->status == '')
                      @if(Auth::user()->isAllowed('LeaveController:edit'))
                        <a href="{{ route('leave.edit', ['id' => $leave->id]) }}" class="btn btn-sm btn-outline-warning peams-action-btn mr-1" title="Edit Leave">
                          <i class="fas fa-pencil-alt"></i>
                        </a>
                      @endif
                      @if(Auth::user()->isAllowed('LeaveController:destroy'))
                        <form action="{{ route('leave.destroy', $leave->employee_id) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this leave application?');">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger peams-action-btn" title="Delete Leave">
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </form>
                      @endif
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-alt d-block mb-2" style="font-size: 1.8rem; opacity: 0.4;"></i>
                    No leave applications found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function () {
    $('#leave-applications').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "columnDefs": [{ "orderable": false, "targets": 6 }]
    });
  });
</script>
@endsection
