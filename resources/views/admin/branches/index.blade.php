@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="m-0 font-weight-bold text-dark">Branch & Hub Management</h3>
        <p class="text-muted small mb-0">Mindoro Pack & Pour Warehousing Logistics Facilities</p>
      </div>
      <a href="{{ url('/branches/create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus mr-1"></i> Add New Branch
      </a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div class="card peams-card">
      <div class="card-header border-0 bg-transparent pt-3 px-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title font-weight-bold text-dark m-0">
          <i class="fas fa-code-branch text-primary mr-2"></i> Active Warehousing Hubs
        </h5>
      </div>
      <div class="card-body px-3 pb-3 pt-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern" id="branches-table">
            <thead>
              <tr>
                <th style="width: 60px;">ID</th>
                <th>Branch Name</th>
                <th>Weekend Days</th>
                <th>Created At</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($branches as $branch)
                <tr>
                  <td><span class="font-weight-bold text-secondary">#{{ $branch->id }}</span></td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ $branch->branch_name }}</span>
                    <small class="text-muted"><i class="fas fa-map-marker-alt text-danger mr-1"></i> Regional Operating Hub</small>
                  </td>
                  <td>
                    <span class="badge badge-light border text-primary font-weight-normal px-2 py-1">
                      {{ is_array(json_decode($branch->weekend)) ? implode(', ', json_decode($branch->weekend)) : ($branch->weekend ?? 'N/A') }}
                    </span>
                  </td>
                  <td>
                    <small class="text-secondary font-weight-bold">{{ \Carbon\Carbon::parse($branch->created_at)->format('M d, Y') }}</small>
                  </td>
                  <td align="right">
                    <a href="{{ url('/branches/'.$branch->id.'/edit') }}" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ url('/branches/'.$branch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete branch record?');">
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
                  <td colspan="5" class="text-center py-4 text-muted">No branches found.</td>
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