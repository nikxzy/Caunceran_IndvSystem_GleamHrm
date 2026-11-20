@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Branch &amp; Hub Management</h3>
        <p class="text-muted small mb-0">Packaging &amp; Equipment Asset Management Facilities</p>
      </div>
      <a href="{{ route('branch.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus mr-1"></i> Add New Branch
      </a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="card peams-card">
      <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">
          <i class="fas fa-code-branch text-primary mr-2"></i> Active Warehousing Hubs
        </h5>
        <span class="badge badge-soft-primary">{{ $branches->count() }} Total</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern" id="branches-table">
            <thead>
              <tr>
                <th style="width: 70px;">ID</th>
                <th>Branch Details</th>
                <th>Operating Hours</th>
                <th>Weekend Days</th>
                <th class="text-center">Status</th>
                <th class="text-right" style="width: 130px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($branches as $branch)
                <tr>
                  <td><span class="font-weight-bold text-secondary">#{{ $branch->id }}</span></td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ $branch->name }}</span>
                    <small class="text-muted">
                      <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                      {{ $branch->address ?? 'No address provided' }}
                    </small>
                  </td>
                  <td>
                    <small class="text-dark font-weight-bold d-block">
                      {{ $branch->timing_start ? \Carbon\Carbon::parse($branch->timing_start)->format('h:i A') : '—' }}
                      <span class="text-muted mx-1">to</span>
                      {{ $branch->timing_off ? \Carbon\Carbon::parse($branch->timing_off)->format('h:i A') : '—' }}
                    </small>
                    <small class="text-muted">
                      <i class="fas fa-phone-alt mr-1"></i>{{ $branch->phone_number ?? 'N/A' }}
                    </small>
                  </td>
                  <td>
                    @php
                      $weekend = is_array(json_decode($branch->weekend)) ? json_decode($branch->weekend) : [];
                    @endphp
                    @if(count($weekend))
                      <span class="badge badge-soft-secondary">{{ implode(', ', $weekend) }}</span>
                    @else
                      <span class="text-muted small">None</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($branch->status == 1)
                      <span class="badge badge-soft-success">Active</span>
                    @else
                      <span class="badge badge-soft-danger">Inactive</span>
                    @endif
                  </td>
                  <td class="text-right text-nowrap">
                    <a href="{{ route('branch.edit', $branch->id) }}" class="btn btn-sm btn-outline-primary peams-action-btn mr-1" title="Edit branch">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('branch.destroy', $branch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete branch record?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger peams-action-btn" title="Delete branch">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="fas fa-code-branch d-block mb-2" style="font-size: 1.8rem; opacity: 0.4;"></i>
                    No branches found.
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
<script>
  $(function () {
    $('#branches-table').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "order": [[0, 'asc']]
    });
  });
</script>
@endsection
