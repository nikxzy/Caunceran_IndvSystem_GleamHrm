@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="m-0 font-weight-bold text-dark">Employee Management</h3>
        <p class="text-muted small mb-0">PEAMS Personnel Records & Branch Directory</p>
      </div>
      <a href="{{ url('/employees/create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-user-plus mr-1"></i> Add New Employee
      </a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div class="card peams-card">
      <div class="card-header border-0 bg-transparent pt-3 px-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title font-weight-bold text-dark m-0">
          <i class="fas fa-users text-primary mr-2"></i> Personnel Directory
        </h5>
      </div>
      <div class="card-body px-3 pb-3 pt-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern id="employees-table">
            <thead>
              <tr>
                <th style="width: 50px;">Avatar</th>
                <th>Name & Email</th>
                <th>Designation</th>
                <th>Employment Status</th>
                <th>Joining Date</th>
                <th text-align="right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($employees as $emp)
                <tr>
                  <td>
                    <img src="{{ asset($emp->picture) }}" onerror="this.src='{{ asset('assets/images/default.png') }}';" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;" alt="avatar">
                  </td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ $emp->firstname }} {{ $emp->lastname }}</span>
                    <small class="text-muted">{{ $emp->official_email }}</small>
                  </td>
                  <td>
                    <span class="badge badge-light border text-dark font-weight-normal px-2 py-1">
                      {{ $emp->designation ?? 'N/A' }}
                    </span>
                  </td>
                  <td>
                    @if(strtolower($emp->employment_status) == 'permanent')
                      <span class="badge badge-success px-2 py-1">Permanent</span>
                    @else
                      <span class="badge badge-warning text-dark px-2 py-1">{{ ucfirst($emp->employment_status ?? 'Probation') }}</span>
                    @endif
                  </td>
                  <td>
                    <small class="text-secondary font-weight-bold">{{ \Carbon\Carbon::parse($emp->joining_date)->format('M d, Y') }}</small>
                  </td>
                  <td align="right">
                    <a href="{{ url('/employees/'.$emp->id.'/edit') }}" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ url('/employees/'.$emp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete employee record?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">No employees registered in system.</td>
                </tr>
              @forelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection