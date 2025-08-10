<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') || {{ config('app.name') }}</title>
    <meta content="Vincent Peter" name="author">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    @include('includes.main-css')
    {{-- Livewire Styles (pastikan ada di includes.main-css, kalau tidak, uncomment baris ini) --}}
    {{-- @livewireStyles --}}
</head>

<body class="c-app">
    @include('layouts.sidebar')

    <div class="c-wrapper">
        <header class="c-header c-header-light c-header-fixed">
            @include('layouts.header')
            <div class="c-subheader justify-content-between px-3">
                @yield('breadcrumb')
            </div>
        </header>

        <div class="c-body">
            <main class="c-main">
                @yield('content')
            </main>
        </div>

        @include('layouts.footer')
    </div>

    {{-- JS utama aplikasi --}}
    @include('includes.main-js')

    {{-- Livewire Scripts (pastikan salah satu ini ter-load sekali saja di layout) --}}
    {{-- Jika includes.main-js belum memuat Livewire, uncomment baris ini --}}
    {{-- @livewireScripts --}}

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Notifikasi swal --}}
    @if (session()->has('swal-success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('swal-success')),
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    {{-- ====== Penting untuk AutoNumeric & script lain yang di-push dari komponen ====== --}}
    @stack('page_scripts')
    @stack('scripts')
</body>
</html>
