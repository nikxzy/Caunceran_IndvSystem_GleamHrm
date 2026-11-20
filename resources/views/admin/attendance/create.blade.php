@extends('layouts.master')

@section('content')
<!-- Page Header -->
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Add Attendance</h3>
        <p class="text-muted small mb-0">Log a check-in / check-out entry for an employee</p>
      </div>
      <div class="mt-2 mt-sm-0">
        <a href="{{ route('leaves') }}" class="btn btn-outline-primary">
          <i class="fas fa-calendar-plus mr-1"></i> Add Leave
        </a>
        <a href="{{ route('attendance') }}" class="btn btn-outline-secondary ml-1">
          <i class="fas fa-arrow-left mr-1"></i> Back to Logs
        </a>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row">
      <div class="col-lg-8">
        <div class="card peams-card">
          <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0">
              <i class="fas fa-clock text-primary mr-2"></i> Attendance Entry
            </h5>
            <span class="badge badge-soft-primary">
              <i class="fas fa-calendar-day mr-1"></i> {{ \Carbon\Carbon::parse($current_date)->format('M d, Y') }}
            </span>
          </div>
          <div class="card-body">
            <form class="form-horizontal" action="{{ route('attendance.storeAttendanceSummaryToday') }}" method="POST">
              @csrf
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Employee <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="employee_id" id="employee_id" required>
                      <option value="">Select Employee</option>
                      @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @if($emp_id == $emp->id) selected @endif>{{ $emp->firstname }} {{ $emp->lastname }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control date" id="attendance_date" name="date" value="{{ $current_date }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Time In <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="time_in" value="{{ $current_time }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Time Out</label>
                    <input type="time" class="form-control" name="time_out" value="">
                  </div>
                </div>
              </div>
              <hr>
              <div class="d-flex justify-content-end">
                <a href="{{ route('attendance') }}" class="btn btn-light border mr-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-check-circle mr-1"></i> Save Attendance
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card peams-card">
          <div class="card-header border-0 bg-transparent">
            <h5 class="card-title m-0">
              <i class="fas fa-clipboard-check text-success mr-2"></i> Day Summary
            </h5>
          </div>
          <div class="card-body pt-0">
            <div class="peams-summary-row">
              <span class="text-muted small">Is Delay?</span>
              <span class="badge {{ isset($attendance_summary->is_delay) && strtolower($attendance_summary->is_delay) === 'yes' ? 'badge-soft-danger' : 'badge-soft-success' }}">
                {{ $attendance_summary->is_delay ?? 'No record' }}
              </span>
            </div>
            <div class="peams-summary-row">
              <span class="text-muted small">Total Hours</span>
              <span class="font-weight-bold text-dark">
                @if(isset($attendance_summary->total_time))
                  {{ number_format(($attendance_summary->total_time / 60), 2, '.', '') }} hrs
                @else
                  —
                @endif
              </span>
            </div>
            <div class="peams-summary-row">
              <span class="text-muted small">Total Checks</span>
              <span class="font-weight-bold text-dark">{{ $attendances->count() }}</span>
            </div>
            <div class="peams-summary-row">
              <span class="text-muted small">First Check-in</span>
              <span class="font-weight-bold text-dark">
                {{ isset($attendance_summary->first_timestamp_in) && $attendance_summary->first_timestamp_in ? date('g:i A', strtotime($attendance_summary->first_timestamp_in)) : '—' }}
              </span>
            </div>
            <div class="peams-summary-row mb-0">
              <span class="text-muted small">Last Check-out</span>
              <span class="font-weight-bold text-dark">
                {{ isset($attendance_summary->last_timestamp_out) && $attendance_summary->last_timestamp_out ? date('g:i A', strtotime($attendance_summary->last_timestamp_out)) : '—' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card peams-card">
      <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">
          <i class="fas fa-list-ul text-info mr-2"></i> Logged Checks
        </h5>
        <span class="badge badge-soft-primary">{{ $attendances->count() }} Total</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="attendance-checks" class="table table-hover table-modern">
            <thead>
              <tr>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Comment</th>
                <th class="text-right" style="width: 130px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($attendances as $att)
                <tr>
                  <td>
                    <span class="badge badge-soft-success">
                      {{ $att->timestamp_in ? \Carbon\Carbon::parse($att->timestamp_in)->format('h:i A') : '—' }}
                    </span>
                  </td>
                  <td>
                    <span class="badge badge-soft-secondary">
                      {{ $att->timestamp_out ? \Carbon\Carbon::parse($att->timestamp_out)->format('h:i A') : '—' }}
                    </span>
                  </td>
                  <td><small class="text-secondary">{{ $att->comment ?: '—' }}</small></td>
                  <td class="text-right text-nowrap">
                    <a class="btn btn-sm btn-outline-primary peams-action-btn mr-1" data-toggle="modal" data-target="#edit{{ $att->id }}" title="Edit Check">
                      <i class="fas fa-pencil-alt"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-danger peams-action-btn" data-toggle="modal" data-target="#confirm-delete{{ $att->id }}" title="Delete Check">
                      <i class="fas fa-trash-alt"></i>
                    </a>

                    <div class="modal fade" id="edit{{ $att->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <form action="{{ route('attendance.update', ['id' => $att->id]) }}" method="post">
                            @csrf
                            <div class="modal-header">
                              <h4 class="modal-title">Edit Attendance</h4>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                              <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" class="form-control" name="date" value="{{ $att->date }}">
                              </div>
                              <div class="form-group">
                                <label for="time_in">Time In</label>
                                <input type="time" class="form-control" name="time_in" value="{{ $att->timestamp_in ? \Carbon\Carbon::parse($att->timestamp_in)->toTimeString() : '' }}">
                              </div>
                              <div class="form-group mb-0">
                                <label for="time_out">Time Out</label>
                                <input type="time" class="form-control" name="time_out" @if($att->timestamp_out != null) value="{{ \Carbon\Carbon::parse($att->timestamp_out)->toTimeString() }}" @endif>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    <div class="modal fade" id="confirm-delete{{ $att->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <form action="{{ route('attendance.deletechecktime', ['id' => $att->id]) }}" method="post">
                            @csrf
                            <div class="modal-header">
                              <h4 class="modal-title">Delete Check</h4>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                              Are you sure you want to delete this check-in
                              <strong>{{ $att->timestamp_in ? \Carbon\Carbon::parse($att->timestamp_in)->format('Y-m-d h:i A') : '' }}</strong>?
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-5 text-muted">
                    <i class="fas fa-clock d-block mb-2" style="font-size: 1.8rem; opacity: 0.4;"></i>
                    No attendance checks logged for this day yet.
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
<script type="text/javascript">
  $(document).ready(function () {
    if ($.fn.DataTable) {
      $('#attendance-checks').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "columnDefs": [{ "orderable": false, "targets": 3 }]
      });
    }

    $('#attendance_date').on('change', function () {
      var empId = $('#employee_id').val() || 0;
      window.location = '{{ route('attendance.create') }}/' + empId + '/' + $(this).val();
    });

    $('#employee_id').on('change', function () {
      if (!this.value) {
        return;
      }
      window.location = '{{ route('attendance.create') }}/' + $(this).val() + '/{{ $current_date }}';
    });
  });
</script>
@endsection
