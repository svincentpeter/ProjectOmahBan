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

    {{-- 1. MAIN CSS (Vite + CoreUI + FontAwesome) --}}
    @include('includes.main-css')

    {{-- 2. Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    {{-- 3. SweetAlert2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    {{-- 4. DataTables CSS --}}
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css" rel="stylesheet">

    {{-- 5. Filepond CSS --}}
    @include('includes.filepond-css')

    {{-- 6. Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    {{-- 7. Custom CSS untuk Stock Opname --}}
    <style>
        /* Progress Bar Enhancement */
        .progress {
            border-radius: 0.25rem;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .progress-bar {
            transition: width 0.6s ease;
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* Notification Dropdown */
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

        /* DataTables Buttons */
        .dataTables_wrapper .dt-buttons {
            margin-bottom: 1rem;
        }

        .dt-button {
            margin-right: 0.25rem;
        }

        /* Badge Enhancement */
        .badge {
            font-weight: 600;
            padding: 0.35em 0.65em;
        }

        /* Counting Page Cards */
        .product-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .product-card:hover {
            border-color: #007bff;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .product-card.counted {
            background-color: #f0fff4;
            border-color: #28a745;
        }

        .product-card.variance-shortage {
            background-color: #fff5f5;
            border-left: 4px solid #dc3545;
        }

        .product-card.variance-surplus {
            background-color: #f0fff4;
            border-left: 4px solid #28a745;
        }

        .product-card.variance-match {
            background-color: #f8f9fa;
            border-left: 4px solid #6c757d;
        }

        /* Input number untuk counting */
        .count-input {
            font-size: 1.25rem;
            font-weight: 700;
            text-align: center;
            border: 2px solid #dee2e6;
            border-radius: 0.5rem;
        }

        .count-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
    </style>

    {{-- 8. Livewire & Page Styles --}}
    @livewireStyles
    @stack('page_styles')
    @yield('third_party_stylesheets')
</head>

<body class="c-app">
    {{-- Sidebar --}}
    @include('layouts.sidebar')

    <div class="c-wrapper">
        {{-- Header --}}
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

    {{-- ======================================== 
         JAVASCRIPT SECTION
    ======================================== --}}

    {{-- 1. jQuery (HARUS PERTAMA) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    {{-- 2. Moment.js (untuk format tanggal) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <script>
        moment.locale('id');
    </script>

    {{-- 3. Bootstrap JS (dari CoreUI bundle, sudah termasuk Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- 4. DataTables --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

    {{-- 5. DataTables Buttons --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    {{-- 6. SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    {{-- 7. Main JS (CoreUI + Vite) --}}
    @include('includes.main-js')

    {{-- 8. Filepond --}}
    @include('includes.filepond-js')

    {{-- 9. Livewire --}}
    @livewireScripts

    {{-- 10. Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- 11. Global DataTables Config --}}
    <script>
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    </script>

    {{-- 12. SweetAlert Session Handler --}}
    <script>
        @if (session()->has('swal-success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('swal-success')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        @endif

        @if (session()->has('swal-error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: @json(session('swal-error')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        @endif

        @if (session()->has('swal-warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: @json(session('swal-warning')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        @endif

        @if (session()->has('swal-info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: @json(session('swal-info')),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: @json($error),
                    timer: 3000,
                    position: 'top-end',
                    toast: true
                });
            @endforeach
        @endif
    </script>

    {{-- 13. Third Party Scripts --}}
    @yield('third_party_scripts')

    {{-- 14. Page Scripts --}}
    @stack('page_scripts')

    {{-- 15. Notification System --}}
    <script>
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

                const data = res.ok ? await res.json() : { count: 0 };
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
                console.warn('Unread badge error:', err);
            }
        }

        function renderNotifItems(container, items) {
            container.innerHTML = '';
            items.forEach(n => {
                const icon = getSeverityIcon(n.severity);
                const title = n.title || 'Notifikasi';
                const message = (n.message || '').length > 140 ?
                    (n.message.substring(0, 140) + '…') : (n.message || '');
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

            try {
                const res = await fetch(LATEST_URL, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                if (!res.ok) throw new Error('Server error ' + res.status);

                let data;
                try {
                    data = await res.json();
                } catch {
                    throw new Error('Invalid JSON response');
                }

                const items = Array.isArray(data) ? data :
                    Array.isArray(data?.notifications) ? data.notifications : [];

                if (!items.length) {
                    empty.style.display = 'block';
                    return;
                }

                renderNotifItems(list, items);
            } catch (err) {
                console.error('Load notifications error:', err);
                empty.style.display = 'block';
            } finally {
                loading.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationBadge();
            setInterval(updateNotificationBadge, 30000);

            let _notifLastLoadedAt = 0;
            const $wrap = $('#notifWrap');

            if ($wrap.length) {
                $wrap.on('show.bs.dropdown', function() {
                    const now = Date.now();
                    if (now - _notifLastLoadedAt > 20000) {
                        loadLatestNotifications().then(() => {
                            _notifLastLoadedAt = now;
                        });
                    }
                });
            }
        });
    </script>

    {{-- 16. Stack Scripts --}}
    @stack('scripts')
</body>

</html>
