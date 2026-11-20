@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Attendance</h3>
        <p class="text-muted small mb-0">Log and review employee time-in and time-out records</p>
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
          <div class="card-header border-0 bg-transparent">
            <h5 class="card-title m-0">
              <i class="fas fa-clock text-primary mr-2"></i> Create Attendance
            </h5>
          </div>
          <div class="card-body">
            <form action="{{ route('attendance.store') }}" method="POST">
              @csrf
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name">Name</label>
                    <select class="form-control nameselect2" name="employee_id">
                      <option value="0">Select Employee</option>
                      @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @if($emp_id == $emp->id) selected @endif>{{ $emp->fullname }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="date">Select Date</label>
                    <input type="date" name="date" id="date" class="form-control datepickstyle" value="{{ $current_date }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="in_out">Check</label>
                    <select class="form-control custom-select" name="in_out">
                      <option value="in" @if($selected_in_out == "in") selected @endif>Time In</option>
                      <option value="out" @if($selected_in_out == "out") selected @endif>Time Out</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="time">Time at</label>
                    <div class="input-group">
                      <input class="form-control time tp" name="time" value="{{ $current_time }}" />
                      <div class="input-group-append">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row" id="totalhours">
                <div class="col-md-4">
                  <div class="p-2 rounded bg-light">
                    <small class="text-muted d-block">Is Delay?</small>
                    <span class="font-weight-bold text-dark">{{ isset($attendance_summary->is_delay) ? $attendance_summary->is_delay : '—' }}</span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-2 rounded bg-light">
                    <small class="text-muted d-block">Total Hours</small>
                    <span class="font-weight-bold text-dark">{{ isset($attendance_summary->total_time) ? $attendance_summary->total_time / 60 : '—' }}</span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-2 rounded bg-light">
                    <small class="text-muted d-block">Total Checks</small>
                    <span class="font-weight-bold text-dark">{{ $attendances->count() ? $attendances->count() / 2 : '—' }}</span>
                  </div>
                </div>
              </div>

              <hr>
              <button class="btn btn-success create-btn" id="add-btn" type="submit">
                <i class="fas fa-check-circle mr-1"></i> Create
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card peams-card">
          <div class="card-header border-0 bg-transparent">
            <h5 class="card-title m-0">
              <i class="fas fa-list-ul text-primary mr-2"></i> Checks for {{ \Carbon\Carbon::parse($current_date)->format('M d, Y') }}
            </h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover table-modern mb-0">
                <thead>
                  <tr>
                    <th>Check Time</th>
                    <th>At Time</th>
                    <th class="text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($attendances as $att)
                    <tr>
                      <td>
                        <span class="badge {{ $att->in_out == 'in' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                          {{ ucfirst($att->in_out) }}
                        </span>
                      </td>
                      <td class="font-weight-bold text-dark">
                        {{ \Carbon\Carbon::parse($att->time)->format('h:i a') }}
                      </td>
                      <td class="text-right text-nowrap">
                        <button class="btn btn-sm btn-outline-primary peams-action-btn mr-1" data-toggle="modal" data-target="#edit{{ $att->id }}" title="Edit check">
                          <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger peams-action-btn" data-toggle="modal" data-target="#confirm-delete{{ $att->id }}" title="Delete check">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center py-5 text-muted">
                        <i class="far fa-clock d-block mb-2" style="font-size: 1.8rem; opacity: 0.4;"></i>
                        Time not added yet.
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

    @foreach($attendances as $att)
      <div class="modal fade" id="edit{{ $att->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form action="{{ route('attendance.update', ['id' => $att->id]) }}" method="post">
              @csrf
              <div class="modal-header">
                <h4 class="modal-title">Edit Attendance</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="date">Date</label>
                  <input type="date" name="date" id="date{{ $att->id }}" class="form-control datepickstyle" value="{{ $current_date }}">
                </div>
                <div class="form-group">
                  <label for="in_out">Check</label>
                  <select class="form-control custom-select" name="in_out">
                    <option value="in" @if($att->in_out == "in") selected @endif>Time In</option>
                    <option value="out" @if($att->in_out == "out") selected @endif>Time Out</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="time">Time at</label>
                  <div class="input-group">
                    <input class="form-control time tp" name="time" value="{{ \Carbon\Carbon::parse($att->time)->format('h:i a') }}" />
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
                <h4 class="modal-title">Delete Attendance Check</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Are you sure you want to delete this check
                <strong>{{ $att->in_out }}</strong> on
                <strong>{{ \Carbon\Carbon::parse($att->date.' '.$att->time)->format('Y-m-d h:i a') }}</strong>?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
  $(document).ready(function () {
    $('#delay').hide();

    $('#date').on('change', function (e) {
      var url = '{{ route('attendance.create', ['id' => $emp_id]) }}?date=' + $(this).val();
      if (url) {
        window.location = url;
      }
      return false;
    });

    $(".nameselect2").select2().on('change.select2', function (e) {
      var url = '{{ url('/attendance/create') }}/' + $(this).val() + '?date={{ $current_date }}';
      if (url) {
        window.location = url;
      }
      return false;
    });
  });
</script>
@endsection
