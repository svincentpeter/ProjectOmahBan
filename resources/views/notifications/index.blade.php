@extends('layouts.app')

@section('title', 'Notification Center')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Notifications</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">

            {{-- ===== Statistik (seragam card di halaman Jasa) ===== --}}
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-info">
                        <div class="stats-icon"><i class="cil-envelope-closed"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Belum Dibaca</div>
                            <div class="stats-value">{{ $stats['unread_count'] }}</div>
                            <small class="stats-subtext">Notifikasi baru</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-purple">
                        <div class="stats-icon"><i class="cil-calendar"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Hari Ini</div>
                            <div class="stats-value">{{ $stats['today_count'] }}</div>
                            <small class="stats-subtext">Notifikasi masuk</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon"><i class="cil-task"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Belum Direview</div>
                            <div class="stats-value">{{ $stats['unreviewed_count'] }}</div>
                            <small class="stats-subtext">Menunggu review</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-danger">
                        <div class="stats-icon"><i class="cil-warning"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Critical</div>
                            <div class="stats-value">{{ $stats['critical_count'] }}</div>
                            <small class="stats-subtext">Perlu perhatian</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Kartu utama ===== --}}
            <div class="card shadow-sm notif-card">
                {{-- Header --}}
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-1 font-weight-bold text-dark">
                                <i class="cil-bell mr-2 text-primary"></i>
                                Notification Center
                            </h5>
                            <small class="text-muted">Kelola notifikasi sistem & operasional</small>
                        </div>
                    </div>
                </div>

                {{-- Filter Section --}}
                <div class="card-body py-4 border-bottom filter-section">
                    <div class="d-flex align-items-center mb-3">
                        <i class="cil-bolt text-primary mr-2" style="font-size:1.3rem;"></i>
                        <h6 class="mb-0 font-weight-bold text-dark">Filter</h6>
                    </div>

                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 filter-label">Status Baca</label>
                            <select class="custom-select custom-select-sm filter-control" id="filterRead">
                                <option value="">Semua</option>
                                <option value="0">Belum Dibaca</option>
                                <option value="1">Sudah Dibaca</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 filter-label">Status Review</label>
                            <select class="custom-select custom-select-sm filter-control" id="filterReviewed">
                                <option value="">Semua</option>
                                <option value="0">Belum Direview</option>
                                <option value="1">Sudah Direview</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 filter-label">Severity</label>
                            <select class="custom-select custom-select-sm filter-control" id="filterSeverity">
                                <option value="">Semua</option>
                                <option value="critical">🚨 Critical</option>
                                <option value="warning">⚠️ Warning</option>
                                <option value="info">ℹ️ Info</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 filter-label">Tipe</label>
                            <select class="custom-select custom-select-sm filter-control" id="filterType">
                                <option value="">Semua</option>
                                <option value="manual_input_alert">Input Manual</option>
                                <option value="price_adjustment">Edit Harga</option>
                                <option value="discount_alert">Diskon</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 filter-label">Fontee Status</label>
                            <select class="custom-select custom-select-sm filter-control" id="filterFonteeStatus">
                                <option value="">Semua</option>
                                <option value="sent">✓ Sent</option>
                                <option value="read">✓✓ Read</option>
                                <option value="failed">✗ Failed</option>
                                <option value="pending">⏳ Pending</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end mb-3">
                            <button class="btn btn-outline-secondary btn-sm w-100" id="btnReset">
                                <i class="cil-reload mr-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="datatable-wrapper">
                            <table class="table table-hover mb-0" id="notificationsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60" class="text-center no-col">No</th>

                                        {{-- Hidden raw timestamp for sorting --}}
                                        <th style="display:none">TS</th>

                                        <th width="130">Waktu</th>
                                        <th width="90">Severity</th>
                                        <th width="140">Tipe</th>
                                        <th>Judul</th>
                                        <th width="130">Status Baca</th>
                                        <th width="140">Status Review</th>
                                        <th width="120">Fontee</th>
                                        <th width="150" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> {{-- /card --}}
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* ===== Global page look khusus halaman ini ===== */
        body {
            background: #f5f7fb;
        }

        .notif-card {
            border-radius: 14px;
        }

        .animated.fadeIn {
            animation: fadeIn .3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .shadow-sm {
            box-shadow: 0 2px 10px rgba(15, 23, 42, .08) !important;
        }

        /* ===== Stat cards (atas) ===== */
        .stats-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
            transition: .3s;
            border-left: 4px solid;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(15, 23, 42, .10);
        }

        .stats-card-purple {
            border-left-color: #6366f1;
        }

        .stats-card-success {
            border-left-color: #22c55e;
        }

        .stats-card-warning {
            border-left-color: #f59e0b;
        }

        .stats-card-info {
            border-left-color: #0ea5e9;
        }

        .stats-card-danger {
            border-left-color: #ef4444;
        }

        .stats-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
            color: #fff;
        }

        .stats-card-info .stats-icon {
            background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);
        }

        .stats-card-purple .stats-icon {
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        }

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }

        .stats-card-danger .stats-icon {
            background: linear-gradient(135deg, #ef4444 0%, #f97373 100%);
        }

        .stats-content {
            flex: 1;
        }

        .stats-label {
            font-size: .8rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6b7280;
            letter-spacing: .06em;
            margin-bottom: 4px;
        }

        .stats-value {
            font-size: 1.9rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .stats-subtext {
            font-size: .78rem;
            color: #9ca3af;
        }

        /* ===== Filter section ===== */
        .filter-section {
            background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 70%);
        }

        .filter-label {
            font-size: .82rem;
            font-weight: 600;
            color: #4b5563;
        }

        .filter-control {
            font-size: .85rem;
            border-radius: 8px;
        }

        /* ===== DataTables wrapper (Show entries + Search) ===== */
        .datatable-wrapper {
            padding: 1rem 1.25rem 1.25rem;
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-size: .86rem;
            color: #4b5563;
        }

        .dataTables_wrapper .dataTables_length select {
            font-size: .85rem;
            padding: .28rem .6rem;
            border-radius: 8px;
            margin: 0 .25rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            font-size: .85rem;
            border-radius: 999px;
            padding: .3rem .8rem;
            border: 1px solid #d1d5db;
        }

        .dataTables_wrapper .dataTables_info {
            font-size: .8rem;
            color: #6b7280;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: .8rem;
            padding: .25rem .6rem;
        }

        /* ===== Table ===== */
        #notificationsTable {
            font-size: .95rem; /* 👉 teks isi tabel dibesarkan */
            color: #111827;
        }

        #notificationsTable thead th {
            font-size: .88rem; /* 👉 header sedikit lebih besar */
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 600;
            color: #6b7280;
            padding: 14px 12px;
            background-color: #f9fafb !important;
            border-bottom: 2px solid #e5e7eb;
            vertical-align: middle;
            white-space: nowrap;
        }

        #notificationsTable tbody td {
            padding: 12px 12px;
            vertical-align: middle;
            font-size: .95rem;
            color: #000000;
        }

        #notificationsTable tbody tr {
            transition: background-color .15s ease;
        }

        #notificationsTable tbody tr:hover {
            background-color: #f3f4ff !important;
        }

        /* Baris unread: aksen lembut */
        #notificationsTable tbody tr.unread {
            background-color: #eef2ff;
            box-shadow: inset 3px 0 0 0 #a5b4fc; /* 👉 border kiri lebih soft */
            font-weight: 500;
        }

        .no-col {
            width: 64px;
        }

        /* ===== Badge di tabel notifikasi (override badge-info/warning/primary/success lama) ===== */

        #notificationsTable .badge {
            border-radius: 999px;
            font-weight: 600;
            letter-spacing: .2px;
            padding: 4px 10px;
            font-size: .86rem;      /* 👉 teks badge lebih besar */
            line-height: 1.1;
        }

        /* Info (biru muda, teks gelap) */
        #notificationsTable .badge-info,
        #notificationsTable .badge-soft-info {
            background: #e0f2ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        /* Primary / tipe "Manual input alert" (ungu pastel) */
        #notificationsTable .badge-primary,
        #notificationsTable .badge-soft-primary {
            background: #e0e7ff;
            color: #7168d3;
            border: 1px solid #c7d2fe;
        }

        /* Warning (kuning pastel, teks coklat) */
        #notificationsTable .badge-warning,
        #notificationsTable .badge-soft-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        /* Success (hijau pastel) */
        #notificationsTable .badge-success,
        #notificationsTable .badge-soft-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* Danger (merah pastel) */
        #notificationsTable .badge-danger,
        #notificationsTable .badge-soft-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Secondary / "-" */
        #notificationsTable .badge-secondary,
        #notificationsTable .badge-soft-secondary {
            background: #e5e7eb;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        /* Khusus tipe price adjustment → hijau kebiruan */
        #notificationsTable .badge-soft-teal {
            background: #ccfbf1;
            color: #0f766e;
            border: 1px solid #a5f3fc;
        }

        #notificationsTable .badge i {
            opacity: .9;
            margin-right: 4px;
        }

        /* ===== Responsif ===== */
        @media (max-width: 768px) {
            .stats-card {
                flex-direction: row;
                align-items: center;
            }

            #notificationsTable thead th,
            #notificationsTable tbody td {
                padding: 8px 6px;
            }

            .datatable-wrapper {
                padding: .75rem;
            }

            .dataTables_wrapper .dataTables_filter {
                text-align: left;
                margin-top: .5rem;
            }
        }

        /* Override badge bawaan Bootstrap di tabel notifikasi */
#notificationsTable .badge.bg-primary {
    background-color: #f7798e !important; /* ungu pastel */
    border: 1px solid #e2e3e6;
}

#notificationsTable .badge.bg-success {
    background-color: #dcfce7 !important; /* hijau pastel */
    border: 1px solid #bbf7d0;
}

#notificationsTable .badge.bg-warning {
    background-color: #fef3c7 !important; /* kuning pastel */
    border: 1px solid #fde68a;
}

#notificationsTable .badge.bg-info {
    background-color: #f7ccf4 !important; /* biru muda pastel */
    border: 1px solid #d3d3d3;
}

    </style>
@endpush


@push('page_scripts')
    <script>
        $(function() {
            const $tableEl = $('#notificationsTable');

            const table = $tableEl.DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                ajax: {
                    url: "{{ route('notifications.data') }}",
                    type: "GET",
                    data: function(d) {
                        d.is_read = $('#filterRead').val();
                        d.is_reviewed = $('#filterReviewed').val();
                        d.severity = $('#filterSeverity').val();
                        d.type = $('#filterType').val();
                        d.fontee_status = $('#filterFonteeStatus').val();
                    },
                    error: function(xhr) {
                        console.error('DataTables AJAX error:', xhr.status, xhr.statusText, xhr
                            .responseText || '(no responseText)');
                    }
                },
                columns: [
                    { // No
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    { // hidden ts
                        data: 'created_at_ts',
                        visible: false,
                        searchable: false
                    },
                    { // waktu
                        data: 'time_ago'
                    },
                    { // severity
                        data: 'severity_badge',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) return data;
                            const map = {
                                critical: '<span class="badge badge-soft-danger">Critical</span>',
                                warning: '<span class="badge badge-soft-warning">Warning</span>',
                                info: '<span class="badge badge-soft-info">Info</span>',
                            };
                            return map[row.severity] || '<span class="badge badge-soft-secondary">-</span>';
                        }
                    },
                    { // tipe
                        data: 'type_badge',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) return data;
                            const map = {
                                manual_input_alert: '<span class="badge badge-soft-purple">Input Manual</span>',
                                price_adjustment: '<span class="badge badge-soft-primary">Edit Harga</span>',
                                discount_alert: '<span class="badge badge-soft-teal">Diskon</span>',
                            };
                            return map[row.type] ||
                                '<span class="badge badge-soft-secondary">Lainnya</span>';
                        }
                    },
                    { // judul
                        data: 'title'
                    },
                    { // read status
                        data: 'read_status',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) return data;
                            return row.is_read ?
                                '<span class="badge badge-soft-success"><i class="cil-check-circle mr-1"></i>Dibaca</span>' :
                                '<span class="badge badge-soft-primary"><i class="cil-bell mr-1"></i>Baru</span>';
                        }
                    },
                    { // reviewed status
                        data: 'reviewed_status',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) return data;
                            return row.is_reviewed ?
                                '<span class="badge badge-soft-success"><i class="cil-task mr-1"></i>Direview</span>' :
                                '<span class="badge badge-soft-warning"><i class="cil-clock mr-1"></i>Pending</span>';
                        }
                    },
                    { // fontee status
                        data: 'fontee_status_badge',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) return data;
                            if (!row.fontee_message_id)
                                return '<span class="badge badge-soft-secondary">-</span>';
                            const map = {
                                sent: '<span class="badge badge-soft-info"><i class="cil-check mr-1"></i>Sent</span>',
                                read: '<span class="badge badge-soft-success"><i class="cil-check-circle mr-1"></i>Read</span>',
                                failed: '<span class="badge badge-soft-danger"><i class="cil-x mr-1"></i>Failed</span>',
                                pending: '<span class="badge badge-soft-warning"><i class="cil-clock mr-1"></i>Pending</span>',
                            };
                            return map[row.fontee_status] ||
                                '<span class="badge badge-soft-secondary">Unknown</span>';
                        }
                    },
                    { // action
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) return data;
                            const showUrl = "{{ url('/notifications') }}/" + row.id;
                            return `
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="${showUrl}" class="btn btn-outline-primary" title="Detail">
                                        <i class="cil-search"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger delete-notif"
                                            data-id="${row.id}" title="Hapus">
                                        <i class="cil-trash"></i>
                                    </button>
                                </div>`;
                        }
                    }
                ],
                columnDefs: [{
                    targets: 1,
                    visible: false,
                    searchable: false
                }],
                order: [
                    [1, 'desc']
                ],
                drawCallback: function() {
                    const api = this.api();
                    api.rows().every(function() {
                        const $row = $(this.node());
                        const data = this.data();
                        const isRead = (data && (data.is_read === true || data.is_read === 1 ||
                            data.is_read === '1'));
                        if (!isRead) $row.addClass('unread');
                        else $row.removeClass('unread');
                    });
                    initTooltips();
                    bindDeleteButtons();
                },
                language: {
                    emptyTable: "Belum ada notifikasi."
                },
                pageLength: 10
            });

            // Filter
            $('#filterRead, #filterReviewed, #filterSeverity, #filterType, #filterFonteeStatus').on('change',
                function() {
                    table.ajax.reload();
                });

            $('#btnReset').on('click', function() {
                $('#filterRead, #filterReviewed, #filterSeverity, #filterType, #filterFonteeStatus')
                    .val('')
                    .trigger('change');
                table.ajax.reload(null, true);
            });

            // Delete
            function bindDeleteButtons() {
                $('.delete-notif').off('click.__notif').on('click.__notif', function() {
                    const notifId = $(this).data('id');
                    confirmDeleteSingle(notifId);
                });
            }

            function confirmDeleteSingle(notifId) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus?',
                        text: 'Hapus notifikasi ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((res) => {
                        if (res.isConfirmed) deleteNotification(notifId);
                    });
                } else {
                    if (confirm('Hapus notifikasi ini?')) deleteNotification(notifId);
                }
            }

            function deleteNotification(notifId) {
                $.ajax({
                    url: "{{ route('notifications.destroy.api', ['notification' => '___ID___']) }}".replace(
                        '___ID___', notifId),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response && response.success) {
                            table.ajax.reload(null, false);
                            showAlert('Berhasil', response.message || 'Notifikasi dihapus', 'success');
                        } else {
                            showAlert('Gagal', (response && response.message) || 'Tidak bisa menghapus',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr.responseText || xhr.statusText);
                        showAlert('Error', 'Gagal menghapus notifikasi', 'error');
                    }
                });
            }

            function initTooltips() {
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body'
                });
            }

            // Initial
            initTooltips();
            bindDeleteButtons();

            table.on('draw.dt', function() {
                initTooltips();
                bindDeleteButtons();
            });
        });

        function showAlert(title, message, type) {
            if (typeof Swal !== 'undefined') {
                Swal.fire(title, message, type);
            } else {
                alert(`${title}: ${message}`);
            }
        }
    </script>
@endpush
