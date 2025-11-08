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
                            <small class="text-muted">Notifikasi baru</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-purple">
                        <div class="stats-icon"><i class="cil-calendar"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Hari Ini</div>
                            <div class="stats-value">{{ $stats['today_count'] }}</div>
                            <small class="text-muted">Notifikasi masuk</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon"><i class="cil-task"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Belum Direview</div>
                            <div class="stats-value">{{ $stats['unreviewed_count'] }}</div>
                            <small class="text-muted">Menunggu review</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-danger">
                        <div class="stats-icon"><i class="cil-warning"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Critical</div>
                            <div class="stats-value">{{ $stats['critical_count'] }}</div>
                            <small class="text-muted">Perlu perhatian</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Kartu utama ===== --}}
            <div class="card shadow-sm">
                {{-- Header (tanpa bulk action) --}}
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-bell mr-2 text-primary"></i>
                                Notification Center
                            </h5>
                            <small class="text-muted">Kelola notifikasi sistem & operasional</small>
                        </div>
                    </div>
                </div>

                {{-- Filter Section --}}
                <div class="card-body py-4 border-bottom"
                    style="background:linear-gradient(to bottom,#f8f9fa 0%,#ffffff 100%);">
                    <div class="d-flex align-items-center mb-3">
                        <i class="cil-bolt text-primary mr-2" style="font-size:1.25rem;"></i>
                        <h6 class="mb-0 font-weight-bold text-dark">Filter</h6>
                    </div>

                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 font-weight-semibold">Status Baca</label>
                            <select class="custom-select custom-select-sm" id="filterRead">
                                <option value="">Semua</option>
                                <option value="0">Belum Dibaca</option>
                                <option value="1">Sudah Dibaca</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 font-weight-semibold">Status Review</label>
                            <select class="custom-select custom-select-sm" id="filterReviewed">
                                <option value="">Semua</option>
                                <option value="0">Belum Direview</option>
                                <option value="1">Sudah Direview</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 font-weight-semibold">Severity</label>
                            <select class="custom-select custom-select-sm" id="filterSeverity">
                                <option value="">Semua</option>
                                <option value="critical">🚨 Critical</option>
                                <option value="warning">⚠️ Warning</option>
                                <option value="info">ℹ️ Info</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 font-weight-semibold">Tipe</label>
                            <select class="custom-select custom-select-sm" id="filterType">
                                <option value="">Semua</option>
                                <option value="manual_input_alert">Input Manual</option>
                                <option value="price_adjustment">Edit Harga</option>
                                <option value="discount_alert">Diskon</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="mb-1 font-weight-semibold">Fontee Status</label>
                            <select class="custom-select custom-select-sm" id="filterFonteeStatus">
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
                            <table class="table table-hover table-striped mb-0" id="notificationsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60" class="text-center no-col">No</th>

                                        {{-- ✅ Hidden raw timestamp for sorting --}}
                                        <th style="display:none">TS</th>

                                        <th width="110">Waktu</th>
                                        <th width="80">Severity</th>
                                        <th width="120">Tipe</th>
                                        <th>Judul</th>
                                        <th width="110">Status Baca</th>
                                        <th width="120">Status Review</th>
                                        <th width="110">Fontee</th>
                                        <th width="160" class="text-center">Aksi</th>
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
        /* ===== Animation & elevation ===== */
        .animated.fadeIn {
            animation: fadeIn .3s ease-in
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08) !important
        }

        /* ===== Stat cards ===== */
        .stats-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            transition: .3s;
            border-left: 4px solid
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .12)
        }

        .stats-card-purple {
            border-left-color: #4834DF
        }

        .stats-card-success {
            border-left-color: #2eb85c
        }

        .stats-card-warning {
            border-left-color: #f9b115
        }

        .stats-card-info {
            border-left-color: #39f
        }

        .stats-card-danger {
            border-left-color: #e55353
        }

        .stats-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
            color: #fff
        }

        .stats-card-purple .stats-icon {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%)
        }

        .stats-card-success .stats-icon {
            background: linear-gradient(135deg, #2eb85c 0%, #51d88a 100%)
        }

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #f9b115 0%, #ffc451 100%)
        }

        .stats-card-info .stats-icon {
            background: linear-gradient(135deg, #39f 0%, #5dadec 100%)
        }

        .stats-card-danger .stats-icon {
            background: linear-gradient(135deg, #e55353 0%, #ff7b7b 100%)
        }

        .stats-content {
            flex: 1
        }

        .stats-label {
            font-size: .75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: .5px;
            margin-bottom: 4px
        }

        .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1
        }

        /* ===== Table ===== */
        #notificationsTable thead th {
            font-size: .8125rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 14px 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
            vertical-align: middle
        }

        #notificationsTable tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: .875rem
        }

        #notificationsTable tbody tr {
            transition: .2s
        }

        #notificationsTable tbody tr:hover {
            background-color: rgba(0, 0, 0, .02) !important
        }

        /* Baris unread diberi aksen kiri + hover lembut */
        #notificationsTable tbody tr.unread {
            background-color: rgba(96, 113, 182, 0.06);
            font-weight: 500;
            box-shadow: inset 3px 0 0 0 #7889ce33;
        }

        /* Kolom No konsisten */
        .no-col {
            width: 64px
        }

        /* ===== Badge-soft (aksesibel) ===== */
        .badge {
            border-radius: 6px;
            font-weight: 600;
            letter-spacing: .2px
        }

        .badge-soft-primary {
            background: rgba(0, 123, 255, .12);
            color: #0b5ed7
        }

        .badge-soft-info {
            background: rgba(23, 162, 184, .12);
            color: #0c7282
        }

        .badge-soft-success {
            background: rgba(40, 167, 69, .12);
            color: #1b7a37
        }

        .badge-soft-warning {
            background: rgba(255, 193, 7, .2);
            color: #7a5a00
        }

        .badge-soft-danger {
            background: rgba(220, 53, 69, .14);
            color: #a01824
        }

        .badge-soft-secondary {
            background: rgba(108, 117, 125, .14);
            color: #495057
        }

        .badge-soft-dark {
            background: rgba(52, 58, 64, .14);
            color: #343a40
        }

        .badge-soft-purple {
            background: rgba(40, 167, 69, 0.14);
            color: #1b7a37;
        }

        .badge-soft-teal {
            background: rgba(32, 201, 151, .14);
            color: #0f9d7a
        }

        /* Type badge kecil tetap kebaca */
        .notif-type-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: .75rem;
            font-weight: 700
        }

        @media (max-width:768px) {
            .datatable-wrapper {
                padding: .5rem
            }

            .stats-card {
                flex-direction: column;
                text-align: center
            }

            #notificationsTable {
                font-size: .75rem
            }

            #notificationsTable thead th {
                padding: 8px 6px
            }

            #notificationsTable tbody td {
                padding: 8px 6px
            }
        }

        .datatable-wrapper {
            padding: 1rem
        }

        .font-weight-semibold {
            font-weight: 600
        }

        /* === Notif Table Badge – lebih terang & tebal === */
        #notificationsTable .badge {
            font-weight: 700;
            /* lebih tebal */
            font-size: .82rem;
            /* sedikit lebih besar */
            padding: 4px 10px;
            /* lebih lega */
            border-radius: 999px;
            /* pill */
            letter-spacing: .25px;
            line-height: 1.1;
        }

        /* Palet soft yang lebih terang + ada border tipis */
        #notificationsTable .badge-soft-info {
            background: #E7F4FF;
            color: #0B5ED7;
            border: 1px solid #CDE7FF;
        }

        #notificationsTable .badge-soft-success {
            background: #EAF7EF;
            color: #1E7E34;
            border: 1px solid #D3F0DB;
        }

        #notificationsTable .badge-soft-warning {
            background: #FFF5D6;
            color: #805600;
            border: 1px solid #FFE9A8;
        }

        #notificationsTable .badge-soft-danger {
            background: #FDE8EA;
            color: #B4232C;
            border: 1px solid #FCCFD4;
        }

        #notificationsTable .badge-soft-secondary {
            background: #F1F3F5;
            color: #495057;
            border: 1px solid #E5E7EA;
        }

        #notificationsTable .badge-soft-dark {
            background: #EDF1F4;
            color: #343A40;
            border: 1px solid #DDE3E8;
        }

        #notificationsTable .badge-soft-purple {
            background: #EAF7EF;
            color: #1E7E34;
            border: 1px solid #D3F0DB;
        }

        #notificationsTable .badge-soft-teal {
            background: #E6FBF5;
            color: #0F766E;
            border: 1px solid #C7F5EA;
        }

        /* Ikon dalam badge sedikit lebih jelas */
        #notificationsTable .badge i {
            opacity: .9;
            margin-right: 4px;
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
                    // Index (No)
                    {
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },

                    // ✅ Hidden raw timestamp for sorting
                    {
                        data: 'created_at_ts',
                        visible: false,
                        searchable: false
                    },

                    // Time ago (human)
                    {
                        data: 'time_ago'
                    },

                    // Severity badge (badge-soft)
                    {
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
                            return map[row.severity] ||
                                '<span class="badge badge-soft-dark">-</span>';
                        }
                    },

                    // Type badge (badge-soft)
                    {
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

                    // Title
                    {
                        data: 'title'
                    },

                    // Read status
                    {
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

                    // Reviewed status
                    {
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

                    // Fontee status
                    {
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

                    // Action (single delete/detail)
                    {
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
                    } // ✅ hide created_at_ts
                ],
                order: [
                    [1, 'desc']
                ], // ✅ sort by hidden timestamp
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
                    bindDeleteButtons(); // single delete only
                },
                language: {
                    emptyTable: "Belum ada notifikasi."
                },
                pageLength: 10
            });

            // ==== Filter handlers ====
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

            // ==== Single delete ====
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

            // Tooltips
            function initTooltips() {
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body'
                });
            }

            // Initial bindings
            initTooltips();
            bindDeleteButtons();

            // Re-bind after any draw
            table.on('draw.dt', function() {
                initTooltips();
                bindDeleteButtons();
            });
        });

        // Helper alert (Swal fallback ke alert biasa)
        function showAlert(title, message, type) {
            if (typeof Swal !== 'undefined') {
                Swal.fire(title, message, type);
            } else {
                alert(`${title}: ${message}`);
            }
        }
    </script>
@endpush
