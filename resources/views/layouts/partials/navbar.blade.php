<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav align-items-center">
    <li class="nav-item">
      <a class="nav-link text-secondary" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block ml-2">
      <span class="badge badge-light border text-secondary px-3 py-1 font-weight-bold" style="border-radius: 20px;">
        <i class="fas fa-warehouse text-primary mr-1"></i> Bongabong Central Hub &amp; Roxas Branch
      </span>
    </li>
    <li class="nav-item d-none d-md-inline-block ml-2">
      <span class="text-muted small"><i class="fas fa-truck text-success mr-1"></i>{{ \App\Hub::active()->count() }} active hubs</span>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto align-items-center">
    <!-- User Profile Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
        <img src="{{ asset('assets/images/default.png') }}" class="img-circle mr-2" style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #e2e8f0;" alt="User Image">
        <span class="d-none d-md-inline font-weight-bold text-dark small">{{ Auth::user()->firstname ?? 'Admin' }}</span>
        <i class="fas fa-angle-down ml-2 text-muted small"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right rounded-lg shadow-sm border-0 mt-2">
        <div class="dropdown-header text-left bg-light font-weight-bold text-dark">
          PEAMS System User
        </div>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-user-circle mr-2 text-primary"></i> My Profile
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item text-danger">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </div>
    </li>
  </ul>
</nav>