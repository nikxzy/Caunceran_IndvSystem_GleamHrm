@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="m-0 font-weight-bold text-dark">Employee Management</h3>
        <p class="text-muted small mb-0">PEAMS Personnel Records & Branch Directory</p>
      </div>
      <a href="{{ route('employee.create') }}" class="btn btn-primary shadow-sm">
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
          <table class="table table-hover table-modern" id="employees-table">
            <thead>
              <tr>
                <th style="width: 50px;">Avatar</th>
                <th>Name & Email</th>
                <th>Designation</th>
                <th>Hub / Branch</th>
                <th>Employment Status</th>
                <th>Joining Date</th>
                <th class="text-right">Actions</th>
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
                    <span class="d-block small font-weight-bold text-dark">{{ optional($emp->hub)->name ?? 'Unassigned hub' }}</span>
                    <small class="text-muted">{{ optional($emp->branch)->name ?? 'No branch' }}</small>
                  </td>
                  <td>
                    @if(strtolower($emp->employment_status ?? '') == 'permanent')
                      <span class="badge badge-success px-2 py-1">Permanent</span>
                    @else
                      <span class="badge badge-warning text-dark px-2 py-1">{{ ucfirst($emp->employment_status ?? 'Probation') }}</span>
                    @endif
                  </td>
                  <td>
                    <small class="text-secondary font-weight-bold">{{ \Carbon\Carbon::parse($emp->joining_date)->format('M d, Y') }}</small>
                  </td>
                  <td align="right">
                    <a href="{{ route('employee.edit', $emp->id) }}" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                      data-toggle="modal" data-target="#deleteEmployeeModal"
                      data-action="{{ route('employee.destroy', $emp->id) }}"
                      data-name="{{ $emp->firstname }} {{ $emp->lastname }}">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">No employees registered in system.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>  </div>
</div>

<div class="modal fade" id="deleteEmployeeModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="deleteEmployeeForm">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user-slash text-danger mr-2"></i> Delete Employee</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Remove <strong id="deleteEmployeeName"></strong> from the active PEAMS directory?</p>
        <div class="form-group"><label>Confirm your admin password *</label><input type="password" name="password" class="form-control" required></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Delete Employee</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  $('#deleteEmployeeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#deleteEmployeeForm').attr('action', button.data('action'));
    $('#deleteEmployeeName').text(button.data('name'));
  });
});
</script>
@endsection
