@extends('layouts.app')

@section('title', 'Monitoring Deviasi Harga Jasa')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Monitoring Deviasi</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">

            {{-- ===== Summary Cards (match style stats-card) ===== --}}
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="stats-card stats-card-danger">
                        <div class="stats-icon"><i class="cil-fire"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">CRITICAL ( &gt; 50% )</div>
                            <div class="stats-value">{{ $summary['critical_count'] }}</div>
                            <small class="text-muted">Deviasi besar, pending approval</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon"><i class="cil-warning"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">WARNING ( 30–50% )</div>
                            <div class="stats-value">{{ $summary['warning_count'] }}</div>
                            <small class="text-muted">Perlu ditinjau, pending approval</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="stats-card stats-card-info">
                        <div class="stats-icon"><i class="cil-cash"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Total Variance Value</div>
                            <div class="stats-value">{{ format_currency($summary['total_variance_value']) }}</div>
                            <small class="text-muted">Total selisih harga (pending)</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Main Card ===== --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-list mr-2 text-primary"></i>
                                Monitoring Deviasi Harga Jasa
                            </h5>
                            <small class="text-muted">Pantau deviasi harga input vs harga master, filter & export</small>
                        </div>

                        <div class="btn-group" role="group">
                            <a href="{{ route('sale.variance-monitoring.export') }}" class="btn btn-success">
                                <i class="cil-cloud-download mr-2"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Quick Filters + Filter Form (match style) --}}
                <div class="card-body py-4 border-bottom"
                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        <div class="d-flex align-items-center mb-3">
                            <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                            <h6 class="mb-0 font-weight-bold text-dark">Filter Cepat</h6>
                        </div>

                        {{-- Quick level pills --}}
                        <div class="quick-filters mb-3">
                            <button type="button" class="filter-pill active" data-filter="">
                                <i class="cil-apps"></i>
                                <span>Semua</span>
                            </button>
                            <button type="button" class="filter-pill" data-filter="critical">
                                <i class="cil-fire"></i>
                                <span>Critical</span>
                            </button>
                            <button type="button" class="filter-pill" data-filter="warning">
                                <i class="cil-warning"></i>
                                <span>Warning</span>
                            </button>
                            <button type="button" class="filter-pill" data-filter="minor">
                                <i class="cil-check-circle"></i>
                                <span>Minor</span>
                            </button>
                        </div>

                        {{-- Detailed filters --}}
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label mb-1">Kasir</label>
                                <select class="form-control form-control-sm" id="filterCashier">
                                    <option value="">-- Semua Kasir --</option>
                                    @foreach ($cashiers as $cashier)
                                        <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label mb-1">Level Deviasi</label>
                                <select class="form-control form-control-sm" id="filterLevel">
                                    <option value="">-- Semua Level --</option>
                                    <option value="critical">CRITICAL (&gt;50%)</option>
                                    <option value="warning">WARNING (30–50%)</option>
                                    <option value="minor">Minor (&lt;30%)</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label mb-1">Status Approval</label>
                                <select class="form-control form-control-sm" id="filterStatus">
                                    <option value="">-- Semua Status --</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label mb-1">Dari Tanggal</label>
                                <input type="date" class="form-control form-control-sm" id="filterDateFrom">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label mb-1">Sampai Tanggal</label>
                                <input type="date" class="form-control form-control-sm" id="filterDateTo">
                            </div>
                            <div class="col-md-2 d-flex align-items-end mb-3">
                                <button class="btn btn-outline-secondary btn-sm w-100" id="btnResetFilter">
                                    🔄 Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top 10 Deviasi --}}
                @if ($top_deviations->isNotEmpty())
                    <div class="card-body pt-4 pb-0">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">
                                    <i class="cil-arrow-top mr-2 text-danger"></i>
                                    Top 10 Deviasi Terbesar
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Kasir</th>
                                            <th>Jasa</th>
                                            <th>Harga Master</th>
                                            <th>Harga Input</th>
                                            <th>Deviasi</th>
                                            <th>Level</th>
                                            <th>Alasan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($top_deviations as $key => $log)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                                <td>{{ $log->cashier->name ?? '-' }}</td>
                                                <td>{{ $log->item_name }}</td>
                                                <td>{{ format_currency($log->master_price) }}</td>
                                                <td><strong>{{ format_currency($log->input_price) }}</strong></td>
                                                <td>
                                                    <span
                                                        class="fw-bold text-{{ $log->variance_percent > 0 ? 'danger' : 'success' }}">
                                                        {{ $log->variance_percent > 0 ? '+' : '' }}{{ $log->variance_percent }}%
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($log->variance_level === 'critical')
                                                        <span class="badge bg-danger">🚨 CRITICAL</span>
                                                    @elseif($log->variance_level === 'warning')
                                                        <span class="badge bg-warning text-dark">⚠️ WARNING</span>
                                                    @else
                                                        <span class="badge bg-info">ℹ️ Minor</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Illuminate\Support\Str::limit($log->reason_provided ?? '', 30) ?: '-' }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- DataTable --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="datatable-wrapper">
                            <table class="table table-hover mb-0" id="variance-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kasir</th>
                                        <th>Jasa</th>
                                        <th>Harga Master</th>
                                        <th>Harga Input</th>
                                        <th>Deviasi</th>
                                        <th>Alasan</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
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
        /* ====== shared from service-masters ====== */
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

        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px
        }

        .filter-pill {
            display: inline-flex;
            align-items: center;
            padding: 12px 24px;
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            color: #4f5d73;
            font-size: .875rem;
            font-weight: 500;
            transition: .3s;
            cursor: pointer;
            white-space: nowrap
        }

        .filter-pill i {
            margin-right: 8px;
            font-size: 1rem
        }

        .filter-pill:hover {
            border-color: #4834DF;
            color: #4834DF;
            background: #f8f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(72, 52, 223, .15)
        }

        .filter-pill.active {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            border-color: #4834DF;
            color: #fff;
            box-shadow: 0 4px 15px rgba(72, 52, 223, .3)
        }

        .datatable-wrapper {
            padding: 1rem
        }

        #variance-table thead th {
            font-size: .8125rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 14px 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef
        }

        #variance-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: .875rem
        }

        #variance-table tbody tr {
            transition: .2s
        }

        #variance-table tbody tr:hover {
            background-color: rgba(72, 52, 223, .03) !important
        }

        @media (max-width:992px) {
            .quick-filters {
                flex-direction: column
            }

            .filter-pill {
                width: 100%;
                justify-content: center
            }
        }

        @media (max-width:768px) {
            .stats-card {
                flex-direction: column;
                text-align: center
            }

            .datatable-wrapper {
                padding: .5rem
            }
        }

        /* aksi icons (jika ada) */
        .action-buttons .btn-icon {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .06)
        }

        .action-buttons .btn-icon i {
            font-size: .95rem;
            line-height: 1
        }

        .action-buttons>* {
            margin-right: .375rem
        }

        .action-buttons>*:last-child {
            margin-right: 0
        }

        #variance-table th:last-child,
        #variance-table td:last-child {
            white-space: nowrap;
            width: 1%;
            text-align: center
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(function() {
            // ===== Quick filter (level) — mirip service-masters =====
            window._varQuick = ''; // '', 'critical', 'warning', 'minor'
            $('.filter-pill').on('click', function() {
                $('.filter-pill').removeClass('active');
                $(this).addClass('active');
                window._varQuick = $(this).data('filter') || '';
                if (window._varianceDT) window._varianceDT.ajax.reload();
            });

            // ===== Tooltips (kalau ada title / data-toggle) =====
            function initTooltips() {
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body'
                });
            }
            initTooltips();

            // ===== DataTable =====
            const tableEl = $('#variance-table');
            window._varianceDT = tableEl.DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                searchDelay: 400,
                ajax: {
                    url: "{{ route('sale.variance-monitoring.data') }}",
                    data: function(d) {
                        d.cashier_id = $('#filterCashier').val();
                        d.level = $('#filterLevel').val();
                        d.approval_status = $('#filterStatus').val();
                        d.date_from = $('#filterDateFrom').val();
                        d.date_to = $('#filterDateTo').val();
                        d.level_quick = window._varQuick || '';
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            const t = new Date(data);
                            return t.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    },
                    {
                        data: 'kasir_name'
                    },
                    {
                        data: 'service_name'
                    },
                    {
                        data: 'master_price_fmt'
                    },
                    {
                        data: 'input_price_fmt'
                    },
                    {
                        data: 'variance_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'reason_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'level_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'approval_status_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });

            // Re-init tooltips after draw
            tableEl.on('draw.dt', initTooltips);

            // ===== Detailed filters =====
            $('#filterCashier, #filterLevel, #filterStatus, #filterDateFrom, #filterDateTo').on('change',
        function() {
                window._varianceDT.ajax.reload();
            });

            $('#btnResetFilter').on('click', function() {
                $('#filterCashier, #filterLevel, #filterStatus, #filterDateFrom, #filterDateTo').val('');
                window._varQuick = '';
                $('.filter-pill').removeClass('active').first().addClass('active');
                window._varianceDT.ajax.reload();
            });
        });
    </script>
@endpush
