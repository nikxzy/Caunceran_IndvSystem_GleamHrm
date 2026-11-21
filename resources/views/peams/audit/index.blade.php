@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Access Audit Dashboard</h3>
        <p class="text-muted small mb-0">Live sessions, last-login timestamps, and privilege flags replacing manual spreadsheet tracking</p>
      </div>
      <a href="{{ route('roles_permissions') }}" class="btn btn-outline-primary"><i class="fas fa-user-shield mr-1"></i> Roles &amp; RBAC</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <div class="row mb-3">
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#d1fae5;color:#047857;"><i class="fas fa-signal"></i></div>
          <div><span class="peams-stat-label">Active Sessions</span><span class="peams-stat-value">{{ $stats['active_sessions'] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-users"></i></div>
          <div><span class="peams-stat-label">Total Users</span><span class="peams-stat-value">{{ $stats['total_users'] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-user-slash"></i></div>
          <div><span class="peams-stat-label">Inactive / Stale</span><span class="peams-stat-value">{{ $stats['inactive'] }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fas fa-flag"></i></div>
          <div><span class="peams-stat-label">Privilege Flags</span><span class="peams-stat-value">{{ $stats['flagged'] }}</span></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card peams-card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0"><i class="fas fa-circle text-success mr-2" style="font-size:9px;"></i> Active Sessions</h5>
            <span class="badge badge-soft-success">{{ $activeUsers->count() }} online</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-modern mb-0">
                <thead><tr><th>User</th><th>Hub</th><th>Role</th><th class="text-right">Last Login</th></tr></thead>
                <tbody>
                  @forelse($activeUsers as $user)
                    <tr>
                      <td><span class="font-weight-bold text-dark d-block">{{ $user->full_name }}</span><small class="text-muted">{{ $user->official_email }}</small></td>
                      <td class="small text-muted">{{ optional($user->hub)->name ?? optional($user->branch)->name ?? '—' }}</td>
                      <td class="small">{{ $user->roles->pluck('name')->implode(', ') ?: 'No role' }}</td>
                      <td class="text-right small">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '—' }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No sessions active in the last 30 minutes.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="card peams-card h-100">
          <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-history text-primary mr-2"></i> Last Login Activity</h5></div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-modern mb-0">
                <thead><tr><th>User</th><th>Account</th><th class="text-right">Last Login</th></tr></thead>
                <tbody>
                  @forelse($lastLogins as $user)
                    <tr>
                      <td class="font-weight-bold">{{ $user->full_name }}</td>
                      <td>
                        @if($user->is_active)
                          <span class="badge badge-soft-success">Active</span>
                        @else
                          <span class="badge badge-soft-danger">Disabled</span>
                        @endif
                      </td>
                      <td class="text-right small text-muted">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="3" class="text-center py-4 text-muted">No login history yet.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card peams-card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0"><i class="fas fa-user-slash text-warning mr-2"></i> Inactive / Stale Accounts</h5>
            <span class="badge badge-soft-warning">Review</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-modern mb-0">
                <thead><tr><th>User</th><th>Branch</th><th>Reason</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                  @forelse($inactiveAccounts as $user)
                    <tr>
                      <td><span class="font-weight-bold d-block">{{ $user->full_name }}</span><small class="text-muted">{{ $user->official_email }}</small></td>
                      <td class="small text-muted">{{ optional($user->branch)->name ?? '—' }}</td>
                      <td class="small">
                        @if(! $user->is_active)
                          <span class="text-danger">Account disabled</span>
                        @elseif(! $user->last_login_at)
                          <span class="text-warning">Never signed in</span>
                        @else
                          <span class="text-muted">No login in 90+ days</span>
                        @endif
                      </td>
                      <td class="text-right"><a href="{{ route('employee.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">Review</a></td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No stale or inactive accounts detected.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="card peams-card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0"><i class="fas fa-user-shield text-danger mr-2"></i> Over-Privileged Accounts</h5>
            <span class="badge badge-soft-danger">{{ $overPrivileged->count() }} flagged</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-modern mb-0">
                <thead><tr><th>User</th><th>Roles</th><th class="text-right">Direct Permissions</th><th class="text-right">Flag</th></tr></thead>
                <tbody>
                  @forelse($overPrivileged as $user)
                    <tr>
                      <td><span class="font-weight-bold d-block">{{ $user->full_name }}</span><small class="text-muted">{{ $user->designation }}</small></td>
                      <td class="small">{{ $user->roles->pluck('name')->implode(', ') ?: '—' }}</td>
                      <td class="text-right small">{{ $user->permissions->count() }}</td>
                      <td class="text-right">
                        @if($user->hasRole('admin'))
                          <span class="badge badge-soft-danger">Full admin</span>
                        @else
                          <span class="badge badge-soft-warning">Excess direct permissions</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No over-privileged accounts detected.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card peams-card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
        <h5 class="card-title m-0"><i class="fas fa-shield-alt text-primary mr-2"></i> Security Event Log</h5>
        <form method="GET" class="d-flex flex-wrap" style="gap:.5rem;">
          <select name="event" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All events</option>
            @foreach($eventCounts as $row)
              <option value="{{ $row->event }}" @if($event === $row->event) selected @endif>{{ ucwords(str_replace('_', ' ', $row->event)) }} ({{ $row->total }})</option>
            @endforeach
          </select>
          <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search description">
          <button class="btn btn-sm btn-primary" type="submit"><i class="fas fa-search"></i></button>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead><tr><th>When</th><th>Event</th><th>Actor</th><th>Subject</th><th>Description</th><th>IP Address</th></tr></thead>
            <tbody>
              @forelse($logs as $log)
                <tr>
                  <td class="small text-muted text-nowrap">{{ $log->created_at ? $log->created_at->format('M d, Y h:i A') : '—' }}</td>
                  <td><span class="badge badge-soft-{{ $log->event_badge }}">{{ ucwords(str_replace('_', ' ', $log->event)) }}</span></td>
                  <td class="small">{{ optional($log->actor)->full_name ?? 'System' }}</td>
                  <td class="small">{{ optional($log->employee)->full_name ?? '—' }}</td>
                  <td class="small text-muted">{{ $log->description ?? '—' }}</td>
                  <td class="small text-muted"><code>{{ $log->ip_address ?? '—' }}</code></td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No audit events recorded yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($logs->hasPages())<div class="card-footer bg-white border-0">{{ $logs->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
