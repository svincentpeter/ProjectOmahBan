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
                position: 'top-end'
            });
        @endif

        @if (session()->has('swal-error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: @json(session('swal-error')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end'
            });
        @endif

        @if (session()->has('swal-warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: @json(session('swal-warning')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end'
            });
        @endif

        @if (session()->has('swal-info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: @json(session('swal-info')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end'
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
                    position: 'top-end'
                });
            @endforeach
        @endif
    </script>

    {{-- 10. Page Scripts (dari blade pages, ini eksekusi terakhir) --}}
    @stack('page_scripts')

    <script>
        // ================================
        // NOTIFICATION BADGE AUTO-UPDATE
        // ================================
        const UNREAD_URL = "{{ route('notifications.unread-count') }}";
        const LATEST_URL = "{{ route('notifications.latest') }}";

        function getSeverityIcon(severity) {
            switch (String(severity || '').toLowerCase()) {
                case 'critical':
                    return '<i class="bi bi-exclamation-triangle-fill text-danger"></i>';
                case 'warning':
                    return '<i class="bi bi-exclamation-circle-fill text-warning"></i>';
                case 'info':
                    return '<i class="bi bi-info-circle-fill text-info"></i>';
                case 'success':
                    return '<i class="bi bi-check-circle-fill text-success"></i>';
                default:
                    return '<i class="bi bi-bell"></i>';
            }
        }

        async function updateNotificationBadge() {
            try {
                const res = await fetch(UNREAD_URL, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const data = res.ok ? await res.json() : {
                    count: 0
                };
                const badge = document.getElementById('notif-badge');
                if (!badge) return;

                const count = parseInt(data.count || 0, 10);
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : String(count);
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            } catch (err) {
                // diamkan saja; jangan ganggu UI
                console.warn('Unread badge error:', err);
            }
        }

        function renderNotifItems(container, items) {
            container.innerHTML = '';
            items.forEach(n => {
                const icon = getSeverityIcon(n.severity);
                const title = n.title || 'Notifikasi';
                const message = (n.message || '').length > 140 ? (n.message.substring(0, 140) + '…') : (n.message ||
                    '');
                const timeAgo = n.time_ago || '';

                const el = document.createElement('div');
                el.className = 'notif-item';
                el.innerHTML = `
      <a href="{{ url('notifications') }}/${n.id}" class="dropdown-item py-2">
        <div class="d-flex align-items-start">
          <div class="mr-2" style="line-height:1.1">${icon}</div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <span class="small font-weight-bold">${title}</span>
              <small class="text-muted ml-2">${timeAgo}</small>
            </div>
            <div class="text-muted small" style="white-space:normal">${message}</div>
          </div>
        </div>
      </a>
      <div class="dropdown-divider m-0"></div>
    `;
                container.appendChild(el);
            });
        }

        async function loadLatestNotifications() {
            const loading = document.getElementById('notif-loading');
            const empty = document.getElementById('notif-empty');
            const list = document.getElementById('notif-list');

            if (!loading || !empty || !list) return;

            loading.style.display = 'block';
            empty.style.display = 'none';
            list.innerHTML = '';

            let hideSpinner = () => {
                loading.style.display = 'none';
            };

            try {
                const res = await fetch(LATEST_URL, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                if (!res.ok) {
                    throw new Error('Server mengembalikan status ' + res.status);
                }

                // Tahan kemungkinan respon bukan JSON
                let data;
                try {
                    data = await res.json();
                } catch {
                    throw new Error('Respon bukan JSON yang valid');
                }

                const items = Array.isArray(data) ? data :
                    Array.isArray(data?.notifications) ? data.notifications : [];

                if (!items.length) {
                    empty.style.display = 'block';
                    return;
                }

                renderNotifItems(list, items);
            } catch (err) {
                console.error('Load latest notifications error:', err);
                empty.style.display = 'block';
                // optional: beri tahu user sekali
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal memuat notifikasi',
                    text: err.message || 'Terjadi kesalahan jaringan/server.',
                    timer: 2500,
                    showConfirmButton: false,
                    position: 'top-end'
                });
            } finally {
                hideSpinner();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Perbarui badge saat halaman dibuka + interval
            updateNotificationBadge();
            setInterval(updateNotificationBadge, 30000);

            // Muat isi dropdown saat dropdown benar-benar dibuka
            // (pakai event Bootstrap)
            let _notifLastLoadedAt = 0;

            const $wrap = $('#notifWrap');
            if ($wrap.length) {
                // load saat dropdown akan dibuka (lebih reliable)
                $wrap.on('show.bs.dropdown', function() {
                    const now = Date.now();
                    // reload maksimal tiap 20 detik
                    if (now - _notifLastLoadedAt > 20000) {
                        loadLatestNotifications().then(() => {
                            _notifLastLoadedAt = now;
                        });
                    }
                });
            }

        });
    </script>


    {{-- 12. Sedikit styling agar lebih “keliatan” --}}
    <style>
        .notif-item {
            transition: background .15s ease;
        }

        .notif-item:hover {
            background: #f8f9fa;
        }

        .notif-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ced4da;
        }
    </style>

    @stack('scripts')
</body>

</html>
