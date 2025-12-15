<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- 1. Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    {{-- 2. SweetAlert2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    {{-- 3. DataTables CSS --}}
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">

    {{-- 4. Filepond CSS --}}
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    {{-- 5. Dropzone CSS --}}
    <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet" type="text/css" />

    <!-- Styles & Scripts (Local Vite) -->
    @vite(['resources/css/flowbite.css', 'resources/js/app.js'])

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        body { background-color: #f8fafc; }
        
        /* Select2 Flowbite Integration Fix */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-color: #d1d5db !important;
            border-radius: 0.5rem !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-top: 5px;
            color: #111827;
        }
    </style>

    @livewireStyles
    @stack('page_styles')
    @yield('third_party_stylesheets')
</head>
<body class="bg-slate-50 text-zinc-950 antialiased font-sans">
    
    <!-- Header -->
    @include('layouts.header-flowbite')
  
    <!-- Sidebar -->
    @include('layouts.sidebar-flowbite')
  
    <!-- Main Content -->
    <div class="p-4 sm:ml-64 pt-24 min-h-screen flex flex-col"> 
        <div class="max-w-7xl mx-auto w-full flex-grow">
            <div class="mb-6"> @yield('breadcrumb') </div>
            @yield('content')
        </div>

        <!-- Footer -->
        @include('layouts.footer-flowbite')
    </div>
    
    {{-- ======================================== 
         JAVASCRIPT SECTION
    ======================================== --}}

    {{-- 1. jQuery (Required for DataTables/Select2) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    {{-- 2. Moment.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <script>moment.locale('id');</script>

    {{-- 3. DataTables --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    {{-- Removed Bootstrap 4 Adapters to allow Flowbite/Tailwind Customization --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    {{-- 4. SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    {{-- SweetAlert Flash from RealRashid Package (toast() helper) --}}
    @include('sweetalert::alert')

    {{-- 5. Filepond --}}
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    {{-- 6. Dropzone JS --}}
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        // CRITICAL: Disable auto-discover IMMEDIATELY after Dropzone loads
        // to prevent it from auto-attaching to elements with class "dropzone"
        // before our manual initialization scripts run
        if (typeof Dropzone !== 'undefined') {
            Dropzone.autoDiscover = false;
        }
    </script>

    {{-- 6. Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- 7. Global Configs --}}
    <script>
        // Register custom 'reload' button type for DataTables
        // This is required because 'reload' is not a built-in button type
        $.fn.dataTable.ext.buttons.reload = {
            text: '<i class="bi bi-arrow-repeat me-1"></i> Reload',
            action: function (e, dt, node, config) {
                dt.ajax.reload(null, false);
            }
        };
    </script>

    {{-- 8. SweetAlert Session Handler --}}
    <script>
        @if (session()->has('swal-success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: @json(session('swal-success')), timer: 3000, showConfirmButton: false, position: 'top-end', toast: true });
        @endif
        @if (session()->has('swal-error'))
            Swal.fire({ icon: 'error', title: 'Error!', text: @json(session('swal-error')), timer: 3000, showConfirmButton: false, position: 'top-end', toast: true });
        @endif
        @if (session()->has('swal-warning'))
            Swal.fire({ icon: 'warning', title: 'Perhatian!', text: @json(session('swal-warning')), timer: 3000, showConfirmButton: false, position: 'top-end', toast: true });
        @endif
        @if (session()->has('swal-info'))
            Swal.fire({ icon: 'info', title: 'Informasi', text: @json(session('swal-info')), timer: 3000, showConfirmButton: false, position: 'top-end', toast: true });
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: @json($error), timer: 3000, position: 'top-end', toast: true });
            @endforeach
        @endif
    </script>
    
    <!-- Flowbite JS (Via Vite) -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script> --}}

    @livewireScripts
    @yield('third_party_scripts')
    @stack('page_scripts')
    @stack('scripts')
</body>
</html>
