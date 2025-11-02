@extends('layouts.app')

@section('title', 'Penyesuaian Stok')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Penyesuaian Stok</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- ========================== STATISTICS CARDS ========================== --}}

            <div class="row mb-4">
                {{-- Total Penyesuaian --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-box border-left-purple">
                        <div class="stat-icon bg-purple">
                            <i class="cil-layers"></i>
                        </div>
                        <div class="stat-text">
                            <span class="stat-label">Total Penyesuaian</span>
                            <h4 class="stat-value">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                {{-- Pending --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-box border-left-warning">
                        <div class="stat-icon bg-warning">
                            <i class="cil-clock"></i>
                        </div>
                        <div class="stat-text">
                            <span class="stat-label">Pending</span>
                            <h4 class="stat-value">{{ $stats['pending'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                {{-- Approved --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-box border-left-success">
                        <div class="stat-icon bg-success">
                            <i class="cil-check-circle"></i>
                        </div>
                        <div class="stat-text">
                            <span class="stat-label">Approved</span>
                            <h4 class="stat-value">{{ $stats['approved'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                {{-- Rejected --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-box border-left-danger">
                        <div class="stat-icon bg-danger">
                            <i class="cil-x-circle"></i>
                        </div>
                        <div class="stat-text">
                            <span class="stat-label">Rejected</span>
                            <h4 class="stat-value">{{ $stats['rejected'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ========================== MAIN CARD ========================== --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-sync mr-2 text-primary"></i>
                                Daftar Penyesuaian Stok
                            </h5>
                            <small class="text-muted">Kelola penyesuaian stok untuk koreksi inventory</small>
                        </div>

                        <a href="{{ route('adjustments.create') }}" class="btn btn-primary">
                            <i class="cil-plus mr-2"></i> Buat Penyesuaian
                        </a>
                    </div>
                </div>

                {{-- ========================== FILTER PANEL ========================== --}}
                <div class="card-body py-4 border-bottom"
                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        <div class="d-flex align-items-center mb-3">
                            <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                            <h6 class="mb-0 font-weight-bold text-dark">Filter</h6>
                        </div>

                        <div class="row">
                            <div class="col-lg-2 col-md-4 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2">
                                    <i class="cil-flag-alt-2 mr-1 text-muted"></i> Status
                                </label>
                                <select id="filter-status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2">
                                    <i class="cil-filter mr-1 text-muted"></i> Tipe
                                </label>
                                <select id="filter-type" class="form-control">
                                    <option value="">Semua Tipe</option>
                                    <option value="add">Penambahan</option>
                                    <option value="sub">Pengurangan</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2">
                                    <i class="cil-user mr-1 text-muted"></i> Requester
                                </label>
                                <select id="filter-requester" class="form-control">
                                    <option value="">Semua Pengaju</option>
                                    @foreach (\App\Models\User::orderBy('name')->get(['id', 'name']) as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-6 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2">
                                    <i class="cil-calendar mr-1 text-muted"></i> Dari
                                </label>
                                <input type="date" id="filter-date-from" class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label small font-weight-semibold text-dark mb-2">
                                    <i class="cil-calendar mr-1 text-muted"></i> Sampai
                                </label>
                                <div class="d-flex gap-2">
                                    <input type="date" id="filter-date-to" class="form-control mr-2">
                                    <button id="btn-filter" class="btn btn-primary mr-2">
                                        <i class="cil-filter mr-1"></i> Terapkan
                                    </button>
                                    <button id="btn-reset" class="btn btn-outline-secondary">
                                        <i class="cil-reload mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap mt-2">
                            <small class="text-muted">Hasil di bawah akan memuat sesuai filter. Setiap approval tetap
                                dilakukan satu per satu.</small>
                            <div class="mb-2">
                                <a id="btn-export" href="javascript:void(0)" class="btn btn-outline-success">
                                    <i class="cil-cloud-download mr-1"></i> Export Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================== DATATABLE ========================== --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="datatable-wrapper">
                            {!! $dataTable->table(['class' => 'table table-hover mb-0', 'id' => 'adjustments-table']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08) !important;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            transition: .3s ease;
            border-left: 4px solid;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .12);
        }

        .stats-card-purple {
            border-left-color: #4834df;
        }

        .stats-card-warning {
            border-left-color: #f59e0b;
        }

        .stats-card-success {
            border-left-color: #2eb85c;
        }

        .stats-card-danger {
            border-left-color: #e55353;
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
            color: white;
        }

        .stats-card-purple .stats-icon {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        }

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }

        .stats-card-success .stats-icon {
            background: linear-gradient(135deg, #2eb85c 0%, #51d88a 100%);
        }

        .stats-card-danger .stats-icon {
            background: linear-gradient(135deg, #e55353 0%, #f27474 100%);
        }

        .stats-label {
            font-size: .75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
        }

        .datatable-wrapper {
            padding: 1rem;
        }

        #adjustments-table thead th {
            font-size: .8125rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 14px 12px;
            background: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        #adjustments-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: .875rem;
        }

        #adjustments-table tbody tr {
            transition: .2s ease;
        }

        #adjustments-table tbody tr:hover {
            background-color: rgba(72, 52, 223, .03) !important;
        }

        .stat-box {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            height: 100%;
            transition: all 0.25s ease;
        }

        .stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
            color: #fff;
            font-size: 1.5rem;
        }

        /* Warna masing-masing */
        .bg-purple {
            background: #5b5ce6;
        }

        .bg-warning {
            background: #f59e0b;
        }

        .bg-success {
            background: #22c55e;
        }

        .bg-danger {
            background: #ef4444;
        }

        /* Garis kiri lembut */
        .border-left-purple {
            border-left: 4px solid #5b5ce6;
        }

        .border-left-warning {
            border-left: 4px solid #f59e0b;
        }

        .border-left-success {
            border-left: 4px solid #22c55e;
        }

        .border-left-danger {
            border-left: 4px solid #ef4444;
        }

        .stat-text .stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6b7280;
            letter-spacing: 0.4px;
        }

        .stat-text .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin: 2px 0 0;
        }
    </style>
@endpush

@push('page_scripts')
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function() {
            const table = window.LaravelDataTables["adjustments-table"];

            // ===== Filtering =====
            $('#btn-filter').on('click', function() {
                table.ajax.reload();
            });
            $('#btn-reset').on('click', function() {
                $('#filter-status,#filter-type,#filter-requester').val('');
                $('#filter-date-from,#filter-date-to').val('');
                table.ajax.reload();
            });

            // Inject filter params to server
            $('#adjustments-table').on('preXhr.dt', function(e, settings, data) {
                data.status = $('#filter-status').val();
                data.type = $('#filter-type').val();
                data.requester_id = $('#filter-requester').val();
                data.date_from = $('#filter-date-from').val();
                data.date_to = $('#filter-date-to').val();
            });

            // ===== Export Excel (ikut filter aktif) =====
            $('#btn-export').on('click', function() {
                const params = new URLSearchParams({
                    status: $('#filter-status').val() || '',
                    type: $('#filter-type').val() || '',
                    requester_id: $('#filter-requester').val() || '',
                    start_date: $('#filter-date-from').val() || '', // <— ganti nama
                    end_date: $('#filter-date-to').val() || '' // <— ganti nama
                });
                window.location.href = "{{ route('adjustments.export') }}?" + params.toString();
            });

        });
    </script>
@endpush
