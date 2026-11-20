@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Hero Welcome Banner --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary bg-opacity-20 text-primary-light border border-primary border-opacity-20 px-3 py-1 rounded-pill fs-7 fw-semibold">
                        <i class="fas fa-warehouse me-1"></i> Operations: Bongabong & Roxas Hubs
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-white">Mindoro Pack & Pour Warehousing Solutions</h3>
                <p class="text-slate-300 mb-0 opacity-75">PEAMS — Packaging & Equipment Asset Management System</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-emerald-500 bg-opacity-10 text-emerald-400 border border-emerald-500 border-opacity-20 px-3 py-2 rounded-3 fs-7 fw-medium">
                    <i class="fas fa-circle text-emerald-400 fs-8 me-2"></i>System Operational
                </span>
            </div>
        </div>
    </div>

    {{-- Quick Stat Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Total Employees</span>
                        <h2 class="fw-bold text-dark mb-0 mt-1">
                            {{ $totalemployees ?? $employeecount ?? (isset($employees) ? count($employees) : (isset($employee) ? count($employee) : 0)) }}
                        </h2>
                        <span class="text-success fs-8 fw-semibold"><i class="fas fa-arrow-up me-1"></i>Active Workforce</span>
                    </div>
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-users fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Payroll Processed</span>
                        <h2 class="fw-bold text-dark mb-0 mt-1">₱{{ number_format($payrollcount ?? 0) }}</h2>
                        <span class="text-muted fs-8">Current Cycle</span>
                    </div>
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-wallet fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Applicants</span>
                        <h2 class="fw-bold text-dark mb-0 mt-1">{{ $applicantcount ?? 0 }}</h2>
                        <span class="text-warning fs-8 fw-semibold"><i class="fas fa-clock me-1"></i>Pending Review</span>
                    </div>
                    <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-user-plus fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analytics Grid 1: Attendance & Designation --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-slate-800 mb-0">
                        <i class="fas fa-user-check text-primary me-2"></i>Attendance Analytics
                    </h5>
                    <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fs-8">Weekly Log</span>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 280px; position: relative;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-slate-800 mb-0">
                        <i class="fas fa-sitemap text-primary me-2"></i>Employees per Designation
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 280px; position: relative;">
                        <canvas id="designationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analytics Grid 2: Recently Added Employees & Gender Ratio --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-slate-800 mb-0">
                        <i class="fas fa-user-plus text-success me-2"></i>Recently Added Employees
                    </h5>
                    <a href="{{ url('/employees') }}" class="btn btn-sm btn-light border rounded-pill px-3 fs-8 fw-semibold text-primary">View All</a>
                </div>
                <div class="card-body px-4 pt-0 pb-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <tbody class="border-top-0">
                                @php
                                    $employeeList = $employees ?? $employee ?? $recent_employees ?? [];
                                @endphp
                                @forelse(is_iterable($employeeList) ? collect($employeeList)->take(4) : [] as $emp)
                                <tr>
                                    <td class="ps-0" style="width: 50px;">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px;">
                                            {{ strtoupper(substr($emp->first_name ?? $emp->name ?? 'E', 0, 1)) }}
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="fw-bold mb-0 text-dark fs-7">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? $emp->name ?? 'Employee' }}</h6>
                                        <span class="text-muted fs-8">{{ $emp->official_email ?? $emp->email ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-slate-100 text-slate-700 border px-2.5 py-1 rounded-pill fs-8">
                                            {{ $emp->designation->name ?? $emp->role ?? 'Staff' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-0">
                                        <span class="text-muted fs-8"><i class="far fa-clock me-1"></i>Active</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted fs-7">No recently added employees found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-slate-800 mb-0">
                        <i class="fas fa-venus-mars text-warning me-2"></i>Gender Ratio
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 250px; position: relative;">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modern Footer --}}
    <footer class="mt-5 py-3 border-top text-center text-muted fs-7">
        Copyright © 2026 <span class="fw-semibold text-slate-700">Mindoro Pack & Pour Warehousing Solutions</span>. All rights reserved.
    </footer>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Attendance Chart
    const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctxAttendance, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            datasets: [{
                label: 'Present Employees',
                data: [18, 22, 25, 24, 21, 15],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Designation Chart
    const ctxDesignation = document.getElementById('designationChart').getContext('2d');
    new Chart(ctxDesignation, {
        type: 'bar',
        data: {
            labels: ['Warehouse', 'Logistics', 'HR/Admin', 'Asset Ops'],
            datasets: [{
                data: [8, 5, 3, 4],
                backgroundColor: ['#2563eb', '#3b82f6', '#0ea5e9', '#64748b'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. Gender Ratio Chart
    const ctxGender = document.getElementById('genderChart').getContext('2d');
    new Chart(ctxGender, {
        type: 'doughnut',
        data: {
            labels: ['Male', 'Female'],
            datasets: [{
                data: [{{ $male ?? 12 }}, {{ $female ?? 8 }}],
                backgroundColor: ['#2563eb', '#ec4899'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            cutout: '70%'
        }
    });
});
</script>
@endsection