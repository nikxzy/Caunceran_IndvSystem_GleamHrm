@extends('layouts.master')

@section('content')
<link href="{{ asset('assets/backend/plugins/fullcalendar-3.9.0/fullcalendar.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/backend/plugins/fullcalendar-3.9.0/fullcalendar.print.css') }}" rel="stylesheet" media="print">

<!-- Page Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="peams-page-title m-0">Attendance Logs</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Attendance</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card peams-card mb-4">
      <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between">
        <div>
          <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Add Attendance
          </a>
          <a href="{{ route('leaves') }}" class="btn btn-outline-primary btn-sm ml-1">
            <i class="fas fa-calendar-plus mr-1"></i> Add Leave
          </a>
        </div>
        <div class="mt-2 mt-sm-0" style="min-width: 220px;">
          <select class="form-control" id="selectOffice">
            <option value="0" @if($branch_id == 0) selected @endif>All Offices</option>
            @foreach($office_locations as $office_location)
              <option value="{{ $office_location->id }}" @if($branch_id == $office_location->id) selected @endif>{{ $office_location->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="card-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</section>

<!-- Manage Attendance Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Manage Attendance</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <label for="leave_type">Choose Type</label>
            <select class="form-control" name="leave_type" id="leave_type">
              <option selected value="present">Present</option>
              <option value="Full Leave">Full Leave</option>
              <option value="Half Leave">Half Leave</option>
              <option value="Short Leave">Short Leave</option>
              <option value="Paid Leave">Paid Leave</option>
            </select>
          </div>
          <div class="col-md-12 mt-3">
            <label for="datefrom">Start Date</label>
            <div class="input-group date" id="datefrompicker">
              <input type="text" id="datefrom" class="form-control" name="datefrom" autocomplete="off">
              <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
              </div>
            </div>
          </div>
          <div class="col-md-12 mt-3">
            <label for="dateto">End Date</label>
            <div class="input-group date" id="datetopicker">
              <input type="text" id="dateto" class="form-control" name="dateto" autocomplete="off">
              <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
              </div>
            </div>
          </div>
          <p id="hello" class="mb-0 mt-2"></p>
          <input type="hidden" id="currentStartTime">
          <input type="hidden" id="currentEndTime">
          <input type="hidden" id="currentStatus">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" id="update" data-dismiss="modal" class="btn btn-primary">Update</button>
        <button type="submit" id="del" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/plugins/fullcalendar-3.9.0/2.22.2-moment.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/fullcalendar-3.9.0/fullcalendar.min.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      eventLimit: true,
      editable: 1,
      eventClick: function (event) {
        if (event.title.search('Birthday') !== -1) {
          // window.location = "{{ route('employees') }}/" + event.resourceId + "/" + event.date;
        }
        if (event.title.search('present') !== -1) {
          window.location = "{{ route('attendance.create') }}/" + event.resourceId + "/" + event.date;
        }
        if (event.title.search('leave') != -1) {
          window.location = "{{ route('leaves') }}/show/" + event.resourceId;
        }
      },
      events: {!! $events !!}
    });

    $('#selectOffice').change(function () {
      var url = "{{ route('attendance') }}/" + $(this).val();
      if (url) {
        window.location = url;
      }
      return false;
    });
  });
</script>
@endsection
