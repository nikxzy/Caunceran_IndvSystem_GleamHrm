@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Departments</h3>
        <p class="text-muted small mb-0">Organizational units available across PEAMS</p>
      </div>
      <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#create" title="Add Department">
        <i class="fas fa-plus mr-1"></i> Add Department
      </button>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="card peams-card">
      <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">
          <i class="fas fa-building text-primary mr-2"></i> Department Directory
        </h5>
        <span class="badge badge-soft-primary">{{ $departments->count() }} Total</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="departments" class="table table-hover table-modern">
            <thead>
              <tr>
                <th style="width: 70px;">#</th>
                <th>Department Name</th>
                <th class="text-center">Status</th>
                <th class="text-right" style="width: 130px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($departments as $key => $department)
                <tr>
                  <td><span class="font-weight-bold text-secondary">{{ $key + 1 }}</span></td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ $department->department_name }}</span>
                    <small class="text-muted">Department ID #{{ $department->id }}</small>
                  </td>
                  <td class="text-center">
                    @if($department->status == 'Active')
                      <span class="badge badge-soft-success">Active</span>
                    @else
                      <span class="badge badge-soft-danger">Inactive</span>
                    @endif
                  </td>
                  <td class="text-right text-nowrap">
                    <a class="btn btn-sm btn-outline-primary peams-action-btn mr-1" data-toggle="modal" data-target="#edit{{ $department->id }}" href="#" title="Edit Department">
                      <i class="fas fa-pencil-alt"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-danger peams-action-btn" data-toggle="modal" data-target="#confirm-delete{{ $department->id }}" href="#" title="Delete Department">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-5 text-muted">
                    <i class="fas fa-building d-block mb-2" style="font-size: 1.8rem; opacity: 0.4;"></i>
                    No departments found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    @foreach($departments as $department)
      <div class="modal fade" id="confirm-delete{{ $department->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form action="{{ route('department.delete', $department->id) }}" method="post">
              @csrf
              <div class="modal-header">
                <h4 class="modal-title">Delete Department</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Are you sure you want to delete Department
                <strong>{{ $department->department_name }}</strong>?
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash-alt mr-1"></i> Delete
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="edit{{ $department->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form id="editDepartmentForm{{ $department->id }}" action="{{ route('department.update', ['id' => $department->id]) }}" method="post">
              @csrf
              <div class="modal-header">
                <h4 class="modal-title">Update Department</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label class="control-label">Name <span class="text-danger">*</span></label>
                  <input type="text" name="department_name" value="{{ old('department_name', $department->department_name) }}" placeholder="Enter Department Name" class="form-control" id="department_name{{ $department->id }}" oninput="check('department_name'+{{ $department->id }});">
                  <span id="department_name-error{{ $department->id }}" class="error invalid-feedback">Department name is required</span>
                </div>
                <div class="form-group">
                  <label class="control-label">Status</label>
                  <select name="status" class="form-control custom-select">
                    <option value="Active" @if($department->status == 'Active') selected @endif>Active</option>
                    <option value="InActive" @if($department->status == 'InActive') selected @endif>InActive</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="validate({{ $department->id }});" class="btn btn-primary">
                  <i class="fas fa-check-circle mr-1"></i> Update
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endforeach

    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form id="createDepartmentForm" action="{{ route('department.create') }}" method="post">
            @csrf
            <div class="modal-header">
              <h4 class="modal-title">Create Department</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label class="control-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="department_name" placeholder="Enter Department Name" class="form-control">
              </div>
              <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control custom-select">
                  <option value="Active">Active</option>
                  <option value="InActive">InActive</option>
                </select>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle mr-1"></i> Create
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function () {
    $('#departments').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "columnDefs": [{ "orderable": false, "targets": 3 }]
    });
  });

  $(function () {
    $('#createDepartmentForm').validate({
      rules: {
        department_name: { required: true }
      },
      messages: {
        department_name: "Department name is required"
      },
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element) {
        $(element).removeClass('is-invalid');
      }
    });
  });

  function validate(id) {
    if ($("#department_name" + id).val() == '') {
      $('#department_name-error' + id).addClass('show');
      $('#department_name' + id).addClass('is-invalid');
    } else {
      $('#editDepartmentForm' + id).submit();
    }
  }

  function check(id) {
    if ($('#' + id).val() != '') {
      $('#' + id).removeClass('show');
      $('#' + id).removeClass('is-invalid');
    } else {
      $('#' + id).addClass('show');
      $('#' + id).addClass('is-invalid');
    }
  }
</script>
@endsection
