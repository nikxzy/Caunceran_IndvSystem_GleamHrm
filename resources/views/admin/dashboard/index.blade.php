@extends('layouts.master')

@section('content')
<style>
    .peams-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 22px 26px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.15);
    }
    .peams-card {
        border: none !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
    }
    .peams-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(0, 0, 0, 0.08) !important;
    }
    .peams-info-box {
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        padding: 18px;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .peams-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-right: 16px;
    }
    .badge-hub {
        background-color: #e0f2fe;
        color: #0369a1;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.82rem;
    }
    .table-modern td, .table-modern th {
        vertical-align: middle !important;
        border-top: 1px solid #f1f5f9;
    }
</style>

<!-- Header Banner -->
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="peams-banner d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h3 class="m-0 font-weight-bold">Mindoro Pack & Pour Warehousing Solutions</h3>
        <p class="mb-0 text-white-50 mt-1" style="font-size: 0.95rem;">PEAMS — Packaging & Equipment Asset Management System</p>
      </div>
      <div class="mt-2 mt-sm-0">
        <span class="badge-hub">
          <i class="fas fa-warehouse mr-1"></i> Operations: Bongabong & Roxas Hubs
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Widgets Section Start -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="peams-info-box peams-card">
          @if(Auth::user()->isAllowed('DashboardController:index'))
            <div class="peams-icon-box bg-primary text-white">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <span class="text-uppercase text-muted font-weight-bold small">Total Employees</span>
              <h3 class="mb-0 font-weight-bold text-dark mt-1">{{count($totalemployees)}}</h3>
            </div>
          @else
            <div class="peams-icon-box bg-success text-white">
              <i class="fas fa-check-square"></i>
            </div>
            <div>
              <span class="text-uppercase text-muted font-weight-bold small">Approved Leaves</span>
              <h3 class="mb-0 font-weight-bold text-dark mt-1">{{$approvedLeaves}}</h3>
            </div>
          @endif
        </div>
      </div>

      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="peams-info-box peams-card">
          @if(Auth::user()->isAllowed('DashboardController:index'))
            <div class="peams-icon-box bg-success text-white">
              <i class="fas fa-database"></i>
            </div>
            <div>
              <span class="text-uppercase text-muted font-weight-bold small">Payroll Processed</span>
              <h3 class="mb-0 font-weight-bold text-dark mt-1">0</h3>
            </div>
          @else
            <div class="peams-icon-box bg-danger text-white">
              <i class="fas fa-window-close"></i>
            </div>
            <div>
              <span class="text-uppercase text-muted font-weight-bold small">Rejected Leaves</span>
              <h3 class="mb-0 font-weight-bold text-dark mt-1">{{$rejectedLeaves}}</h3>
            </div>
          @endif
        </div>
      </div>

      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="peams-info-box peams-card">
          @if(Auth::user()->isAllowed('DashboardController:index'))
            <div class="peams-icon-box bg-warning text-white">
              <i class="fas fa-user-plus"></i>
            </div>
            <div>
              <span class="text-uppercase text-muted font-weight-bold small">Applicants</span>
              <h3 class="mb-0 font-weight-bold text-dark mt-1">{{$applicants}}</h3>
            </div>
          @else
            <div class="peams-icon-box bg-warning text-white">
              <i class="fas fa-minus-square"></i>
            </div>
            <div>
              <span class="text-uppercase text-muted font-weight-bold small">Pending Leaves</span>
              <h3 class="mb-0 font-weight-bold text-dark mt-1">{{$pendingLeaves}}</h3>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Widgets Section End -->

<!-- Main content Start -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div @if(Auth::user()->isAllowed('DashboardController:index')) class="col-lg-6" @else class="col-lg-12" @endif>
        
        <!-- Attendance Chart Card -->
        <div class="card peams-card mb-4">
          <div class="card-header border-0 bg-transparent pt-3 px-3">
            <h5 class="card-title font-weight-bold text-dark">
              <i class="fas fa-user-clock text-primary mr-2"></i> Attendance Analytics
            </h5>
          </div>
          <div class="card-body">
            <div class="position-relative mb-2">
              <canvas id="attendance" @if(Auth::user()->isAllowed('DashboardController:index')) style="max-height: 380px;" @else style="max-height: 450px;" @endif></canvas>
            </div>
          </div>
        </div>

        @if(Auth::user()->isAllowed('DashboardController:index'))
          <!-- Recently Added Employee Card -->
          <div class="card peams-card mb-4">
            <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center pt-3 px-3">
              <h5 class="card-title font-weight-bold text-dark m-0">
                <i class="fas fa-user-check text-success mr-2"></i> Recently Added Employees
              </h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
            </div>
            <div class="card-body px-3 pb-3 pt-0">
              <div class="table-responsive">
                <table class="table table-modern no-border mb-0">
                  <tbody>
                  @foreach($employee as $employees)
                    <tr>
                      <td style="width:50px">
                        <img src="{{asset($employees->picture)}}" onerror="this.src='{{asset('assets/images/default.png')}}';" width="45" height="45" class="rounded-circle shadow-sm" style="object-fit:cover;" alt="picture">
                      </td>
                      <td>
                        <span class="font-weight-bold text-dark d-block">{{$employees->firstname}} {{$employees->lastname}}</span>
                        <small class="text-muted">{{$employees->designation ?? 'Warehouse Personnel'}}</small>
                      </td>
                      <td align="right">
                        <span class="badge badge-light text-secondary border px-2 py-1 small">
                          {{ $diff = Carbon\Carbon::parse($employees->joining_date)->subMonth()->diffForHumans()}}
                        </span>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @endif
      </div>

      @if(Auth::user()->isAllowed('DashboardController:index'))
        <div class="col-lg-6">
          
          <!-- Designation Distribution Card -->
          <div class="card peams-card mb-4">
            <div class="card-header border-0 bg-transparent pt-3 px-3">
              <h5 class="card-title font-weight-bold text-dark">
                <i class="fas fa-sitemap text-info mr-2"></i> Employees per Designation
              </h5>
            </div>
            <div class="card-body">
              <div class="position-relative mb-2">
                <canvas id="employees" height="230"></canvas>
              </div>
            </div>
          </div>

          <!-- Gender Ratio Card -->
          <div class="card peams-card mb-4">
            <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center pt-3 px-3">
              <h5 class="card-title font-weight-bold text-dark m-0">
                <i class="fas fa-venus-mars text-warning mr-2"></i> Gender Ratio
              </h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="position-relative" style="max-height: 250px;">
                <canvas id="gender"></canvas>
              </div>
            </div>
          </div>
          
        </div>
      @endif
    </div>
  </div>
</div>
<!-- Main content End -->

@if(Auth::user()->isAllowed('DashboardController:index'))
  <script>
    var ctx = document.getElementById('attendance').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'polarArea',
      data: {
        labels: {!! $chartMonths !!},
        datasets: [{
          label: {!! $chartMonths !!},
          data: {!! $averageAttendance !!},
          backgroundColor: [
            'rgba(255, 99, 132, 0.65)',
            'rgba(54, 162, 235, 0.65)',
            'rgba(255, 206, 86, 0.65)',
            'rgba(153, 102, 255, 0.65)',
            'rgba(75, 192, 192, 0.65)',
            'rgba(255, 159, 64, 0.65)',
            'rgba(255, 215, 0, 0.65)',
            'rgba(128, 128, 0, 0.65)',
            'rgba(154, 205, 50, 0.65)',
            'rgba(0, 128, 128, 0.65)',
            'rgba(112, 128, 144, 0.65)',
            'rgba(139, 69, 19, 0.65)'
          ],
          borderColor: [
            '#ff6384', '#36a2eb', '#ffce56', '#9966ff', '#4bc0c0', '#ff9f40',
            '#ffd700', '#808000', '#9acd32', '#008080', '#708090', '#8b4513'
          ],
          borderWidth: 1.5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    var ctx = document.getElementById('employees').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {!! $DesignationName !!},
        datasets: [{
          label: 'Total Employees',
          data: {!! $designationSeries !!},
          backgroundColor: 'rgba(37, 99, 235, 0.7)',
          borderColor: '#2563eb',
          borderRadius: 6,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    var ctx = document.getElementById('gender').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Male', 'Female'],
        datasets: [{
          data: [{!! $male !!}, {!! $female !!}],
          backgroundColor: [
            'rgba(54, 162, 235, 0.75)',
            'rgba(255, 99, 132, 0.75)'
          ],
          borderColor: [
            '#36a2eb',
            '#ff6384'
          ],
          borderWidth: 1.5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  </script>
@else
  <script>
    var ctx = document.getElementById('attendance').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'polarArea',
      data: {
        labels: {!! $chartMonths !!},
        datasets: [{
          label: {!! $chartMonths !!},
          data: {!! $averageAttendance !!},
          backgroundColor: [
            'rgba(255, 99, 132, 0.65)',
            'rgba(54, 162, 235, 0.65)',
            'rgba(255, 206, 86, 0.65)',
            'rgba(153, 102, 255, 0.65)',
            'rgba(75, 192, 192, 0.65)',
            'rgba(255, 159, 64, 0.65)',
            'rgba(255, 215, 0, 0.65)',
            'rgba(128, 128, 0, 0.65)',
            'rgba(154, 205, 50, 0.65)',
            'rgba(0, 128, 128, 0.65)',
            'rgba(112, 128, 144, 0.65)',
            'rgba(139, 69, 19, 0.65)'
          ],
          borderColor: [
            '#ff6384', '#36a2eb', '#ffce56', '#9966ff', '#4bc0c0', '#ff9f40',
            '#ffd700', '#808000', '#9acd32', '#008080', '#708090', '#8b4513'
          ],
          borderWidth: 1.5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>
@endif
@stop