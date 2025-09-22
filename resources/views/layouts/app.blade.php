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

    @include('includes.main-css')
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

    {{-- JS utama --}}
    @include('includes.main-js')
@livewireScripts


    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    @stack('page_scripts')
    @stack('scripts')
</body>
</html>
