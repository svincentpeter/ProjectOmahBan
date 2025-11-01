<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', config('app.name'))</title>
    <meta content="Vincent Peter" name="author">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    {{-- MAIN CSS (CoreUI + Bootstrap) --}}
    @include('includes.main-css')

    {{-- Select2 CSS - HARUS sebelum JS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    {{-- SweetAlert2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    {{-- DataTables CSS (jika pakai) --}}
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    {{-- Filepond CSS --}}
    @include('includes.filepond-css')

    {{-- Bootstrap Icons (untuk form icons) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    @livewireStyles
    @stack('page_styles')
    @yield('third_party_stylesheets')
</head>

<body class="c-app">
    {{-- Sidebar kiri --}}
    @include('layouts.sidebar')

    <div class="c-wrapper">
        {{-- Header (INI YANG BENAR, bukan layouts.navbar) --}}
        <header class="c-header c-header-light c-header-fixed">
            @include('layouts.header')

            <div class="c-subheader justify-content-between px-3">
                @yield('breadcrumb')
            </div>
        </header>

        <div class="c-body">
            <main class="c-main px-3 px-xl-4">
                @yield('content')
            </main>
        </div>

        {{-- Footer --}}
        @include('layouts.footer')
    </div>

    {{-- 1. jQuery (HARUS PERTAMA) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    {{-- 2. Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- 3. DataTables --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

    {{-- 4. SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    {{-- 5. Main JS (CoreUI init) --}}
    @include('includes.main-js')

    {{-- 6. Filepond JS --}}
    @include('includes.filepond-js')

    {{-- 7. Livewire --}}
    @livewireScripts

    {{-- 8. BARU: Select2 JS DIPINDAH KE SINI (SETELAH SEMUA YANG MUNGKIN MEMUAT jQuery) --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- 9. Toast/Notification Handler --}}
    <script>
        // Global SweetAlert handler untuk session flash
        @if (session()->has('swal-success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('swal-success')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-right'
            });
        @endif

        @if (session()->has('swal-error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: @json(session('swal-error')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-right'
            });
        @endif

        @if (session()->has('swal-warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: @json(session('swal-warning')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-right'
            });
        @endif

        @if (session()->has('swal-info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: @json(session('swal-info')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-right'
            });
        @endif

        // Handle validation errors
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: @json($error),
                    timer: 3000,
                    position: 'top-right'
                });
            @endforeach
        @endif
    </script>

    {{-- 10. Page Scripts (dari blade pages, ini eksekusi terakhir) --}}
    @stack('page_scripts')
    @stack('scripts')
</body>

</html>
