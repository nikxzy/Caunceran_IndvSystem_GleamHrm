@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Designations</h3>
        <p class="text-muted small mb-0">Job titles and positions recognized by the organization</p>
      </div>
      <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#create" title="Add Designation">
        <i class="fas fa-plus mr-1"></i> Add Designation
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
          <i class="fas fa-sitemap text-primary mr-2"></i> Designation Directory
        </h5>
        <span class="badge badge-soft-primary">{{ $designations->count() }} Total</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="designations" class="table table-hover table-modern">
            <thead>
              <tr>
                <th style="width: 70px;">#</th>
                <th>Designation Name</th>
                <th class="text-center">Status</th>
                <th class="text-right" style="width: 130px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($designations as $key => $designation)
                <tr>
                  <td><span class="font-weight-bold text-secondary">{{ $key + 1 }}</span></td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ $designation->designation_name }}</span>
                    <small class="text-muted">Designation ID #{{ $designation->id }}</small>
                  </td>
                  <td class="text-center">
                    @if($designation->status == 1)
                      <span class="badge badge-soft-success">Enabled</span>
                    @else
                      <span class="badge badge-soft-danger">Disabled</span>
                    @endif
                  </td>
                  <td class="text-right text-nowrap">
                    <a class="btn btn-sm btn-outline-primary peams-action-btn mr-1" data-toggle="modal" data-target="#edit{{ $designation->id }}" href="#" title="Edit Designation">
                      <i class="fas fa-pencil-alt"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-danger peams-action-btn" data-toggle="modal" data-target="#confirm-delete{{ $designation->id }}" href="#" title="Delete Designation">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-5 text-muted">
                    <i class="fas fa-sitemap d-block mb-2" style="font-size: 1.8rem; opacity: 0.4;"></i>
                    No designations found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    @foreach($designations as $designation)
      <div class="modal fade" id="confirm-delete{{ $designation->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form action="{{ route('designation.delete', $designation->id) }}" method="post">
              @csrf
              <div class="modal-header">
                <h4 class="modal-title">Delete Designation</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Are you sure you want to delete Designation
                <strong>{{ $designation->designation_name }}</strong>?
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

      <div class="modal fade" id="edit{{ $designation->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form id="editDesignationForm{{ $designation->id }}" action="{{ route('designation.update', ['id' => $designation->id]) }}" method="post">
              @csrf
              <div class="modal-header">
                <h4 class="modal-title">Update Designation</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label class="control-label">Designation Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" value="{{ old('name', $designation->designation_name) }}" placeholder="Enter Designation Name" class="form-control" id="designation_name{{ $designation->id }}" oninput="check('designation_name'+{{ $designation->id }});">
                  <span id="designation_name-error{{ $designation->id }}" class="error invalid-feedback">Designation name is required</span>
                </div>
                <div class="form-group">
                  <label class="control-label">Status</label>
                  <select name="status" class="form-control custom-select">
                    <option value="1" @if($designation->status == 1) selected @endif>Enable</option>
                    <option value="0" @if($designation->status == 0) selected @endif>Disable</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="validate({{ $designation->id }});" class="btn btn-primary">
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
          <form id="createDesignationForm" action="{{ route('designation.create') }}" method="post">
            @csrf
            <div class="modal-header">
              <h4 class="modal-title">Create Designation</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label class="control-label">Designation Name <span class="text-danger">*</span></label>
                <input type="text" name="designation_name" placeholder="Enter Designation Name" class="form-control">
              </div>
              <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control custom-select">
                  <option value="1">Enable</option>
                  <option value="0">Disable</option>
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
    $('#designations').DataTable({
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
    $('#createDesignationForm').validate({
      rules: {
        designation_name: { required: true }
      },
      messages: {
        designation_name: "Designation name is required"
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
    if ($("#designation_name" + id).val() == '') {
      $('#designation_name-error' + id).addClass('show');
      $('#designation_name' + id).addClass('is-invalid');
    } else {
      $('#editDesignationForm' + id).submit();
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
