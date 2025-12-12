<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS') - {{ config('app.name') }}</title>
    
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    
    {{-- Tailwind CSS via CDN (User Requested) and Local CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/pos.css', 'resources/js/app.js'])
    
    {{-- Flowbite CSS: loaded via NPM/Vite (node_modules/flowbite) --}}
    
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    {{-- Tailwind Config --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ob: {
                            primary: '#4f46e5',
                            soft: '#eef2ff',
                        }
                    }
                }
            }
        }
    </script>
    
    {{-- Livewire Styles --}}
    @livewireStyles
    
    @stack('styles')
</head>

<body class="h-full bg-slate-100 font-sans antialiased">
<div class="min-h-screen flex flex-col">
    
    {{-- TOP BAR --}}
    <header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur">
        <div class="w-full px-3 md:px-6 xl:px-10 py-3 flex items-center justify-between gap-3">
            
            {{-- Logo & Info --}}
            <div class="flex items-center gap-3">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-ob-primary text-white text-lg font-bold shadow-sm">
                    OB
                </div>
                <div class="space-y-0.5">
                    <p class="text-sm font-semibold text-slate-900">
                        {{ config('app.name') }} • Point of Sale
                    </p>
                    <p class="text-xs text-slate-500">
                        Shift: {{ auth()->user()->name ?? 'Kasir' }} • Lokasi: Workshop
                    </p>
                </div>
            </div>
            
            {{-- Right Actions --}}
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600">
                <div class="hidden md:flex flex-col items-end">
                    <span class="font-medium text-slate-700">Tanggal</span>
                    <span id="pos-date" class="font-mono text-[11px]"></span>
                </div>
                <div class="hidden md:flex flex-col items-end">
                    <span class="font-medium text-slate-700">No. Transaksi</span>
                    <span class="font-mono text-[11px] text-slate-800">
                        POS-{{ date('Ymd-Hi') }}
                    </span>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-x-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:ring-2 focus:ring-ob-primary transition-colors">
                        <i class="bi bi-arrow-left"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                    
                    <button type="button"
                            data-modal-target="shortcut-modal"
                            data-modal-toggle="shortcut-modal"
                            class="inline-flex items-center gap-x-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:ring-2 focus:ring-ob-primary">
                        <i class="bi bi-keyboard"></i>
                        Shortcut
                    </button>
                    
                    <button type="button"
                            class="hidden sm:inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500">
                        Simpan Draft
                    </button>
                </div>
            </div>
            
        </div>
    </header>
    
    {{-- MAIN CONTENT --}}
    <main class="flex-1">
        <div class="w-full px-3 md:px-6 xl:px-10 py-4">
            @yield('content')
        </div>
    </main>
    
</div>

{{-- MODAL: SHORTCUT --}}
<div id="shortcut-modal" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full h-full">
    <div class="relative p-4 w-full max-w-sm">
        <div class="relative bg-white rounded-2xl shadow-lg border border-slate-200">
            <div class="flex items-center justify-between p-3 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">
                    <i class="bi bi-keyboard mr-2"></i>Shortcut Kasir
                </h3>
                <button type="button"
                        class="text-slate-400 hover:text-slate-600"
                        data-modal-hide="shortcut-modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-4 space-y-2 text-xs text-slate-700">
                <p class="text-[11px] text-slate-500 mb-3">
                    Gunakan shortcut keyboard untuk mempercepat input.
                </p>
                <ul class="space-y-1.5">
                    <li class="flex justify-between py-1">
                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded bg-slate-100 text-slate-700 font-mono font-semibold">F2</span>
                        <span class="font-medium">Fokus pencarian</span>
                    </li>
                    <li class="flex justify-between py-1">
                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded bg-slate-100 text-slate-700 font-mono font-semibold">F4</span>
                        <span class="font-medium">Input diskon</span>
                    </li>
                    <li class="flex justify-between py-1">
                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded bg-slate-100 text-slate-700 font-mono font-semibold">F8</span>
                        <span class="font-medium">Pilih customer</span>
                    </li>
                    <li class="flex justify-between py-1">
                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded bg-emerald-100 text-emerald-800 font-mono font-semibold">F9</span>
                        <span class="font-medium text-emerald-700">Selesaikan transaksi</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
{{-- Flowbite JS: loaded via NPM/Vite (resources/js/app.js) --}}
{{-- Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- Midtrans Snap (Sandbox) --}}
@if(config('midtrans.client_key'))
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif

@livewireScripts

{{-- Optimization Scripts --}}
<script src="{{ asset('js/pos-optimized.js') }}"></script>

<script>
    // Update DateTime
    function updateDateTime() {
        const el = document.getElementById('pos-date');
        if (el) {
            // moment.locale('id');
            el.textContent = moment().format('DD MMM YYYY, HH:mm');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        setInterval(updateDateTime, 30000);
    });
</script>

{{-- SweetAlert Handler --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('swal-success', (event) => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: event[0],
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        });
        
        Livewire.on('swal-error', (event) => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: event[0],
                timer: 3000,
                position: 'top-end',
                toast: true
            });
        });

        Livewire.on('swal-warning', (event) => {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: event[0],
                timer: 3000,
                position: 'top-end',
                toast: true
            });
        });
    });
</script>

@stack('page_scripts')
@stack('scripts')
</body>
</html>
