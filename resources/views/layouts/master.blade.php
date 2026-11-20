<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if(isset($platform->logo))
      <link rel="icon" type="image/png" sizes="16x16" href="{{asset($platform->logo)}}">
    @else
      <link rel="icon" type="image/png" sizes="16x16" href="{{asset('assets/images/company_logo.png')}}">
    @endif
    <title>PEAMS | Mindoro Pack & Pour Warehousing Solutions</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons StyleSheet -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/material-icons/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Theme StyleSheet -->
    <link rel="stylesheet" href="{{ asset('assets/backend/dist/css/adminlte.min.css') }}">

    <!-- Overlay Scrollbar & DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <!-- GLOBAL PEAMS MODERN OVERRIDE STYLES -->
    <style>
      /* Global Typography & Background */
      body, .content-wrapper, .main-header, .main-footer {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        background-color: #f8fafc !important;
        color: #334155 !important;
      }

      /* Top Navbar Clean Styling */
      .main-header {
        border-bottom: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
      }
      .main-header .nav-link {
        color: #475569 !important;
      }

      /* Deep Slate Navy Sidebar */
      .main-sidebar {
        background-color: #0f172a !important;
        box-shadow: 4px 0 25px rgba(0, 0, 0, 0.1) !important;
      }
      .brand-link {
        background-color: #090d16 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        padding: 16px 20px !important;
      }
      .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link {
        color: #94a3b8 !important;
        border-radius: 8px !important;
        margin: 3px 10px !important;
        font-weight: 500;
        font-size: 0.91rem;
        transition: all 0.2s ease;
      }
      .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.06) !important;
        color: #ffffff !important;
        transform: translateX(3px);
      }
      .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35) !important;
      }

      /* Global Card & Container Modernization */
      .card {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04) !important;
        background: #ffffff !important;
        margin-bottom: 1.5rem !important;
      }
      .card-header {
        background-color: transparent !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 1rem 1.25rem !important;
      }
      .card-title {
        font-weight: 700 !important;
        color: #0f172a !important;
        font-size: 1.05rem !important;
      }

      /* Global Buttons Modernization */
      .btn {
        border-radius: 8px !important;
        font-weight: 500 !important;
        padding: 0.45rem 1rem !important;
        transition: all 0.2s ease !important;
      }
      .btn-primary {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25) !important;
      }
      .btn-primary:hover {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
        transform: translateY(-1px);
      }
      .btn-success {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
      }
      .btn-danger {
        background-color: #ef4444 !important;
        border-color: #ef4444 !important;
      }

      /* Global Tables Modernization across ALL Modules */
      .table {
        color: #334155 !important;
      }
      .table thead th {
        background-color: #f8fafc !important;
        border-bottom: 2px solid #e2e8f0 !important;
        color: #475569 !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 0.78rem !important;
        letter-spacing: 0.05em !important;
      }
      .table td {
        vertical-align: middle !important;
        border-top: 1px solid #f1f5f9 !important;
        font-size: 0.9rem !important;
      }
      .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(248, 250, 252, 0.5) !important;
      }

      /* Global Form Controls Modernization */
      .form-control, .custom-select {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.9rem !important;
      }
      .form-control:focus, .custom-select:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
      }

      /* PEAMS Shared Page Components */
      .peams-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 14px !important;
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.05) !important;
        overflow: hidden;
      }
      .peams-card .card-header {
        padding: 1.1rem 1.4rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
      }
      .peams-page-title {
        font-weight: 700 !important;
        color: #0f172a !important;
        letter-spacing: -0.01em;
      }
      .table-modern {
        margin-bottom: 0 !important;
      }
      .table-modern thead th {
        border-top: 0 !important;
        white-space: nowrap;
      }
      .table-modern tbody tr {
        transition: background-color 0.15s ease;
      }
      .table-modern tbody tr:hover {
        background-color: #f8fafc !important;
      }
      .table-modern td, .table-modern th {
        vertical-align: middle !important;
        border-top: 1px solid #f1f5f9 !important;
      }
      .badge-soft-primary { background-color: #dbeafe; color: #1d4ed8; }
      .badge-soft-success { background-color: #d1fae5; color: #047857; }
      .badge-soft-warning { background-color: #fef3c7; color: #b45309; }
      .badge-soft-danger  { background-color: #fee2e2; color: #b91c1c; }
      .badge-soft-secondary { background-color: #e2e8f0; color: #475569; }
      .badge-soft-primary, .badge-soft-success, .badge-soft-warning,
      .badge-soft-danger, .badge-soft-secondary {
        font-weight: 600;
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        display: inline-block;
        line-height: 1.2;
      }
      .peams-action-btn {
        width: 34px;
        height: 34px;
        padding: 0 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
      }

      /* Footer Styling */
      .main-footer {
        border-top: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        font-size: 0.85rem !important;
        color: #64748b !important;
        padding: 0.9rem 1.25rem !important;
      }

      /* Page Header / Breadcrumb */
      .content-header {
        padding: 1.15rem 0.5rem 0.35rem !important;
      }
      .content-header h1, .content-header h3 {
        letter-spacing: -0.02em;
      }
      .content-header .peams-page-title {
        font-size: 1.55rem;
      }
      .breadcrumb {
        background: transparent !important;
        margin-bottom: 0 !important;
        padding: 0 !important;
        font-size: 0.85rem;
      }
      .breadcrumb-item a {
        color: #2563eb !important;
        font-weight: 500;
      }
      .breadcrumb-item.active {
        color: #94a3b8 !important;
      }
      .breadcrumb-item + .breadcrumb-item::before {
        content: '›';
        color: #cbd5e1;
      }

      /* Sidebar Details */
      .main-sidebar .nav-header {
        color: #64748b !important;
        font-size: 0.66rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.12em !important;
        text-transform: uppercase !important;
        padding: 1.1rem 1.15rem 0.4rem !important;
      }
      .main-sidebar .nav-sidebar .nav-icon {
        font-size: 0.9rem;
        width: 1.65rem;
        text-align: center;
      }
      .main-sidebar .user-panel {
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
      }
      .main-sidebar .sidebar {
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.3) transparent;
      }
      .main-sidebar .sidebar::-webkit-scrollbar {
        width: 5px;
      }
      .main-sidebar .sidebar::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 999px;
      }

      /* Navbar Dropdown */
      .navbar .dropdown-menu {
        border-radius: 12px !important;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12) !important;
        padding: 0.4rem 0;
      }
      .navbar .dropdown-item {
        padding: 0.55rem 1.15rem;
        font-size: 0.88rem;
        font-weight: 500;
      }
      .navbar .dropdown-item:hover {
        background-color: #f8fafc;
      }

      /* Alerts */
      .alert {
        border: 0 !important;
        border-radius: 10px !important;
        font-size: 0.9rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
      }
      .alert-success { background-color: #ecfdf5 !important; color: #047857 !important; }
      .alert-danger { background-color: #fef2f2 !important; color: #b91c1c !important; }

      /* Modals */
      .modal-content {
        border: 0 !important;
        border-radius: 14px !important;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.2) !important;
      }
      .modal-header {
        border-bottom: 1px solid #f1f5f9 !important;
      }
      .modal-title {
        font-weight: 700 !important;
        color: #0f172a !important;
        font-size: 1.05rem !important;
      }
      .modal-footer {
        border-top: 1px solid #f1f5f9 !important;
      }

      /* Shared Summary Rows */
      .peams-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.7rem 0;
        border-bottom: 1px dashed #e2e8f0;
      }
      .peams-summary-row:last-child {
        border-bottom: 0;
      }

      /* Attendance Stat Cards */
      .peams-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.1rem 1.2rem;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
        height: 100%;
      }
      .peams-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
      }
      .peams-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-right: 0.9rem;
        flex: 0 0 46px;
      }
      .peams-stat-label {
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-size: 0.68rem;
        font-weight: 700;
        color: #94a3b8;
        display: block;
      }
      .peams-stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
      }
      .peams-legend {
        display: inline-flex;
        align-items: center;
        font-size: 0.78rem;
        color: #64748b;
        margin-right: 1rem;
      }
      .peams-legend .dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
        margin-right: 0.4rem;
      }

      /* FullCalendar Modern Styling */
      #calendar {
        font-family: 'Inter', system-ui, sans-serif;
      }
      .fc .fc-toolbar.fc-header-toolbar {
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.35rem;
      }
      .fc .fc-toolbar h2 {
        font-size: 1.3rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        letter-spacing: -0.01em;
      }
      .fc .fc-button {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        color: #475569 !important;
        border-radius: 8px !important;
        font-size: 0.82rem !important;
        font-weight: 500 !important;
        padding: 0.4rem 0.75rem !important;
        box-shadow: none !important;
        text-transform: capitalize;
      }
      .fc .fc-button:hover {
        background: #f1f5f9 !important;
        color: #1d4ed8 !important;
      }
      .fc .fc-button.fc-state-active, .fc .fc-button:active {
        background: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
      }
      .fc .fc-button .fc-icon {
        font-size: 0.9rem;
      }
      .fc-unthemed th, .fc-unthemed td, .fc-unthemed thead, .fc-unthemed tbody, .fc-unthemed .fc-divider, .fc-unthemed .fc-row, .fc-unthemed .fc-content, .fc-unthemed .fc-popover, .fc-unthemed .fc-list-view, .fc-unthemed .fc-list-heading td {
        border-color: #eef2f7 !important;
      }
      .fc-unthemed th.fc-day-header {
        background: #f8fafc;
        color: #64748b;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.7rem 0 !important;
      }
      .fc-unthemed td.fc-today {
        background: rgba(37, 99, 235, 0.045) !important;
      }
      .fc-unthemed .fc-today .fc-day-number {
        background: #2563eb;
        color: #ffffff !important;
        border-radius: 999px;
        display: inline-block;
        min-width: 24px;
        text-align: center;
        padding: 1px 5px;
        margin: 4px !important;
      }
      .fc .fc-day-number {
        font-size: 0.82rem;
        color: #475569;
        font-weight: 600;
        padding: 6px 8px !important;
      }
      .fc-event {
        border: 0 !important;
        border-radius: 6px !important;
        padding: 2px 4px !important;
        font-size: 0.76rem !important;
        font-weight: 600;
        margin: 1px 2px !important;
      }
      .fc-event .fc-content {
        white-space: normal !important;
      }
      .fc .fc-list-item-title, .fc .fc-time {
        font-weight: 500;
      }

      /* Page Fade-in */
      .content-wrapper > .content, .content-wrapper > .content-header {
        animation: peamsFadeIn 0.35s ease both;
      }
      @keyframes peamsFadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
      }

      @media (max-width: 767.98px) {
        .content-header .peams-page-title { font-size: 1.3rem; }
        .fc .fc-toolbar { justify-content: center; }
        .fc .fc-toolbar h2 { font-size: 1.1rem !important; }
      }
    </style>
    @stack('styles')

    <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/Chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jquery-validation/additional-methods.min.js') }}"></script>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      @include('layouts.partials.navbar')
      @include('layouts.partials.left-sidebar')

      <div class="content-wrapper">
        @yield('content')
      </div>

      @include('layouts.partials.right-sidebar')
      @include('layouts.partials.footer')
    </div>

    <!-- SCRIPTS -->
    <script src="{{ asset('assets/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/backend/dist/js/adminlte.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    @yield('scripts')
    @stack('scripts')
  </body>
</html>