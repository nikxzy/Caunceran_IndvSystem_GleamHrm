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

      /* Footer Styling */
      .main-footer {
        border-top: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        font-size: 0.85rem !important;
        color: #64748b !important;
      }
    </style>

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
  </body>
</html>