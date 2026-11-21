<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="{{ url('/dashboard') }}" class="brand-link">
    <div class="d-flex align-items-center">
      <div class="bg-primary text-white rounded-lg d-flex align-items-center justify-content-center mr-2 shadow-sm" style="width: 36px; height: 36px;">
        <i class="fas fa-boxes" style="font-size: 1.1rem;"></i>
      </div>
      <div>
        <span class="brand-text font-weight-bold text-white d-block" style="font-size: 1.1rem; line-height: 1.2;">PEAMS</span>
        <small class="text-white-50 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Pack &amp; Pour Asset System</small>
      </div>
    </div>
  </a>

  <div class="sidebar px-2">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center px-2">
      <div class="image">
        <img src="{{ asset('assets/images/default.png') }}" class="img-circle elevation-1" style="width: 34px; height: 34px; object-fit: cover;" alt="User Image">
      </div>
      <div class="info pl-2">
        <a href="#" class="d-block font-weight-bold text-white small m-0">{{ Auth::user()->firstname ?? 'Administrator' }}</a>
        <small class="text-white-50"><i class="fas fa-circle text-success mr-1" style="font-size: 7px;"></i> Active Session</small>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-header">MAIN DASHBOARD</li>
        <li class="nav-item">
          <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-pie"></i><p>Operations Dashboard</p>
          </a>
        </li>

        <li class="nav-header">INVENTORY &amp; ORDERS</li>
        <li class="nav-item">
          <a href="{{ route('peams.inventory.index') }}" class="nav-link {{ request()->is('peams/inventory*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-box-open"></i><p>Inventory Catalog</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('peams.orders.index') }}" class="nav-link {{ request()->is('peams/orders*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-invoice"></i><p>Orders &amp; Check-out</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('peams.transfers.index') }}" class="nav-link {{ request()->is('peams/transfers*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-exchange-alt"></i><p>Hub Transfers</p>
          </a>
        </li>

        <li class="nav-header">ASSETS &amp; PROCUREMENT</li>
        <li class="nav-item">
          <a href="{{ route('peams.purchase-orders.index') }}" class="nav-link {{ request()->is('peams/purchase-orders*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-signature"></i><p>Purchase Orders</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('peams.loans.index') }}" class="nav-link {{ request()->is('peams/loans*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tools"></i><p>Equipment Loans</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('peams.maintenance.index') }}" class="nav-link {{ request()->is('peams/maintenance*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-wrench"></i><p>Maintenance &amp; Repairs</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('peams.disposals.index') }}" class="nav-link {{ request()->is('peams/disposals*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dumpster"></i><p>Asset Disposals</p>
          </a>
        </li>

        <li class="nav-header">ORGANIZATION &amp; HR</li>
        <li class="nav-item">
          <a href="{{ url('/employees') }}" class="nav-link {{ request()->is('employees*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-id-badge"></i><p>Employees</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('branch.index') }}" class="nav-link {{ request()->is('branch*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-code-branch"></i><p>Branches / Hubs</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/departments') }}" class="nav-link {{ request()->is('departments*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-building"></i><p>Departments</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/designations') }}" class="nav-link {{ request()->is('designations*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-sitemap"></i><p>Designations</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('attendance') }}" class="nav-link {{ request()->is('attendance*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-clock"></i><p>Attendance Logs</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('employeeleaves') }}" class="nav-link {{ request()->is('employee_leaves*') || request()->is('leave*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-calendar-alt"></i><p>Leave Applications</p>
          </a>
        </li>

        <li class="nav-header">SYSTEM ADMINISTRATION</li>
        <li class="nav-item">
          <a href="{{ route('roles_permissions') }}" class="nav-link {{ request()->is('roles*') || request()->is('role/*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-shield"></i><p>Roles &amp; RBAC</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('peams.audit.index') }}" class="nav-link {{ request()->is('peams/audit*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-shield-alt"></i><p>Access Audit</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
