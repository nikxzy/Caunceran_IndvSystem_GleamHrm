@extends('layouts.master')

@push('styles')
<link href="{{ asset('assets/backend/plugins/fullcalendar-3.9.0/fullcalendar.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/backend/plugins/fullcalendar-3.9.0/fullcalendar.print.css') }}" rel="stylesheet" media="print">
<style>
  .peams-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
    color: #ffffff;
    border-radius: 16px;
    padding: 1.4rem 1.6rem;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
  }
  .peams-hero h3 {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin: 0;
  }
  .peams-hero p {
    color: rgba(255, 255, 255, 0.65);
    margin: 0.35rem 0 0;
    font-size: 0.88rem;
  }
  .peams-hero .hero-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  .peams-hero .hero-select {
    min-width: 210px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.35);
    color: #0f172a;
    font-weight: 500;
  }
  .peams-hero .btn-light-soft {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }
  .peams-hero .btn-light-soft:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
  }
  .peams-hero .btn-white {
    background: #ffffff;
    border-color: #ffffff;
    color: #1d4ed8;
    font-weight: 600;
  }
  .peams-hero .btn-white:hover {
    background: #eff6ff;
    border-color: #eff6ff;
    color: #1d4ed8;
  }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="peams-hero d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h3><i class="fas fa-clock mr-2" style="opacity: 0.8;"></i>Attendance Logs</h3>
        <p>Daily attendance, leaves and delays across all offices</p>
      </div>
      <div class="hero-actions mt-3 mt-lg-0">
        <select class="form-control hero-select" id="selectOffice">
          <option value="0" @if($branch_id == 0) selected @endif>All Offices</option>
          @foreach($office_locations as $office_location)
            <option value="{{ $office_location->id }}" @if($branch_id == $office_location->id) selected @endif>{{ $office_location->name }}</option>
          @endforeach
        </select>
        <a href="{{ route('attendance.create') }}" class="btn btn-white">
          <i class="fas fa-plus mr-1"></i> Add Attendance
        </a>
        <a href="{{ route('leaves') }}" class="btn btn-light-soft">
          <i class="fas fa-calendar-plus mr-1"></i> Add Leave
        </a>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="peams-stat-card">
          <span class="peams-stat-icon" style="background:#dbeafe; color:#1d4ed8;"><i class="fas fa-users"></i></span>
          <div>
            <span class="peams-stat-label">Employees</span>
            <span class="peams-stat-value">{{ number_format($stats['employees']) }}</span>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="peams-stat-card">
          <span class="peams-stat-icon" style="background:#d1fae5; color:#047857;"><i class="fas fa-clipboard-check"></i></span>
          <div>
            <span class="peams-stat-label">Attendance Logs</span>
            <span class="peams-stat-value">{{ number_format($stats['records']) }}</span>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="peams-stat-card">
          <span class="peams-stat-icon" style="background:#fef3c7; color:#b45309;"><i class="fas fa-hourglass-half"></i></span>
          <div>
            <span class="peams-stat-label">Delayed Check-ins</span>
            <span class="peams-stat-value">{{ number_format($stats['delays']) }}</span>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="peams-stat-card">
          <span class="peams-stat-icon" style="background:#ede9fe; color:#6d28d9;"><i class="fas fa-calendar-alt"></i></span>
          <div>
            <span class="peams-stat-label">Leave Records</span>
            <span class="peams-stat-value">{{ number_format($stats['leaves']) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar -->
    <div class="card peams-card mb-4">
      <div class="card-header border-0 bg-transparent d-flex flex-wrap justify-content-between align-items-center">
        <h5 class="card-title m-0">
          <i class="fas fa-calendar-alt text-primary mr-2"></i> Attendance Calendar
        </h5>
        <div class="mt-2 mt-sm-0">
          <span class="peams-legend"><span class="dot" style="background:#00a560;"></span> Present</span>
          <span class="peams-legend"><span class="dot" style="background:#43474a;"></span> Delayed</span>
          <span class="peams-legend"><span class="dot" style="background:#ef4444;"></span> Leave</span>
          <span class="peams-legend"><span class="dot" style="background:#ec4899;"></span> Birthday</span>
        </div>
      </div>
      <div class="card-body pt-2">
        <div id="calendar"></div>
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
      defaultView: 'month',
      eventLimit: true,
      height: 'auto',
      eventClick: function (event) {
        if (event.title.search('Birthday') !== -1) {
          return;
        }
        if (event.title.search('present') !== -1) {
          window.location = "{{ route('attendance.create') }}/" + event.resourceId + "/" + event.date.format('YYYY-MM-DD');
        }
        if (event.title.search('leave') !== -1) {
          window.location = "{{ url('leave/show') }}/" + event.resourceId;
        }
      },
      events: {!! $events !!}
    });

    $('#selectOffice').change(function () {
      window.location = "{{ route('attendance') }}/" + $(this).val();
    });
  });
</script>
@endsection
