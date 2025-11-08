@extends('layouts.app')

@section('title', 'Semua Penjualan')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Semua Penjualan</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Main Card --}}
            <div class="card shadow-sm">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-cart mr-2 text-primary"></i>
                                Semua Penjualan
                            </h5>
                            <small class="text-muted">Monitor dan kelola transaksi penjualan</small>
                        </div>
                    </div>
                </div>

                {{-- Quick Filters Section --}}
                <div class="card-body py-4 border-bottom"
                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        {{-- Header --}}
                        <div class="d-flex align-items-center mb-3">
                            <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                            <h6 class="mb-0 font-weight-bold text-dark">Filter Cepat</h6>
                        </div>

                        {{-- Quick Filter Pills --}}
                        <div class="quick-filters mb-3">
                            <button type="button" class="filter-pill" data-preset="today">
                                <i class="cil-calendar"></i>
                                <span>Hari Ini</span>
                            </button>

                            <button type="button" class="filter-pill" data-preset="this_week">
                                <i class="cil-calendar"></i>
                                <span>Minggu Ini</span>
                            </button>

                            <button type="button" class="filter-pill" data-preset="this_month">
                                <i class="cil-calendar"></i>
                                <span>Bulan Ini</span>
                            </button>

                            <button type="button" class="filter-pill" data-preset="last_month">
                                <i class="cil-calendar"></i>
                                <span>Bulan Lalu</span>
                            </button>

                            <button type="button" class="filter-pill" data-preset="this_year">
                                <i class="cil-calendar"></i>
                                <span>Tahun Ini</span>
                            </button>

                            <button type="button" class="filter-pill filter-pill-custom" id="customFilterToggle">
                                <i class="cil-settings"></i>
                                <span>Custom</span>
                            </button>
                        </div>

                        {{-- Hidden preset value --}}
                        <input type="hidden" id="filter_preset">

                        {{-- Advanced Filter Form --}}
                        <div id="advancedFilter" class="advanced-filter" style="display: none;">
                            <div class="row">
                                {{-- Pilih Bulan --}}
                                <div class="col-lg-2 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-calendar mr-1 text-muted"></i> Pilih Bulan
                                    </label>
                                    <input type="month" id="filter_bulan" class="form-control form-control-lg"
                                        placeholder="YYYY-MM">
                                    <small class="form-text text-muted mt-1">Format: Tahun-Bulan</small>
                                </div>

                                {{-- Dari Tanggal --}}
                                <div class="col-lg-2 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-calendar mr-1 text-muted"></i> Dari Tanggal
                                    </label>
                                    <input type="date" id="filter_dari" class="form-control form-control-lg">
                                </div>

                                {{-- Sampai Tanggal --}}
                                <div class="col-lg-2 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="cil-calendar mr-1 text-muted"></i> Sampai Tanggal
                                    </label>
                                    <input type="date" id="filter_sampai" class="form-control form-control-lg">
                                </div>

                                {{-- FILTER: Hanya dengan Diskon/Perubahan Harga --}}
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="bi bi-tag-fill text-warning mr-1"></i> Filter Diskon
                                    </label>
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="filter_has_adjustment"
                                            value="1">
                                        <label class="custom-control-label font-weight-semibold"
                                            for="filter_has_adjustment">
                                            <i class="bi bi-tag-fill text-warning mr-1"></i>
                                            Hanya Transaksi dengan Diskon
                                        </label>
                                    </div>
                                    <small class="form-text text-muted mt-1">
                                        Tampilkan hanya transaksi yang memiliki perubahan harga/diskon
                                    </small>
                                </div>

                                {{-- FILTER BARU: Input Manual --}}
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2">
                                        <i class="bi bi-pencil-square text-info mr-1"></i> Filter Input Manual
                                    </label>
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="filter_has_manual"
                                            value="1">
                                        <label class="custom-control-label font-weight-semibold" for="filter_has_manual">
                                            <i class="bi bi-pencil-square text-info mr-1"></i>
                                            Hanya Transaksi dengan Input Manual
                                        </label>
                                    </div>
                                    <small class="form-text text-muted mt-1">
                                        Tampilkan hanya transaksi yang memiliki catatan input manual
                                    </small>
                                </div>

                                {{-- Apply + Reset Button (selaras dengan halaman Produk) --}}
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2 d-block">
                                        &nbsp;
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <button id="btn_filter_apply" class="btn btn-primary btn-lg">
                                            <i class="cil-filter mr-2"></i> Terapkan Filter
                                        </button>
                                        <button id="btn_filter_reset" class="btn btn-outline-secondary btn-lg">
                                            <i class="cil-reload mr-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Text --}}
                            <div class="alert alert-info mb-0" role="alert">
                                <i class="cil-info-circle mr-2"></i>
                                <small>
                                    <strong>Catatan:</strong> Jika mengisi rentang tanggal (Dari–Sampai),
                                    maka pilihan preset dan bulan akan diabaikan secara otomatis.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ====== SUMMARY CARDS (pakai stats-card biar sama dengan Produk) ====== --}}
                <div class="row mb-4 px-3" id="sales-summary">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="stats-card stats-card-purple">
                            <div class="stats-icon">
                                <i class="cil-cash"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">Total Penjualan</div>
                                <div id="sum-total" class="stats-value">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="stats-card stats-card-success">
                            <div class="stats-icon">
                                <i class="cil-chart-line"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">Total Profit</div>
                                <div id="sum-profit" class="stats-value">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="stats-card stats-card-info">
                            <div class="stats-icon">
                                <i class="cil-cart"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">Total Transaksi</div>
                                <div id="sum-count" class="stats-value">0</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DataTable Section --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="datatable-wrapper">
                            {{ $dataTable->table(['class' => 'table table-hover mb-0', 'id' => 'sales-table']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* ========== Animations ========== */
        .animated.fadeIn {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========== Card Shadow (selaras) ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Statistics Cards (copy style Produk) ========== */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .stats-card-purple {
            border-left-color: #4834DF;
        }

        .stats-card-success {
            border-left-color: #2eb85c;
        }

        .stats-card-warning {
            border-left-color: #f9b115;
        }

        .stats-card-info {
            border-left-color: #39f;
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
        }

        .stats-card-purple .stats-icon {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            color: white;
        }

        .stats-card-success .stats-icon {
            background: linear-gradient(135deg, #2eb85c 0%, #51d88a 100%);
            color: white;
        }

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #f9b115 0%, #ffc451 100%);
            color: white;
        }

        .stats-card-info .stats-icon {
            background: linear-gradient(135deg, #39f 0%, #5dadec 100%);
            color: white;
        }

        .stats-content {
            flex: 1;
        }

        .stats-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
        }

        /* ========== Quick Filter Pills (copy dari Produk) ========== */
        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-pill {
            display: inline-flex;
            align-items: center;
            padding: 12px 24px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            color: #4f5d73;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        .filter-pill i {
            margin-right: 8px;
            font-size: 1rem;
        }

        .filter-pill:hover {
            border-color: #4834DF;
            color: #4834DF;
            background: #f8f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.15);
        }

        .filter-pill.active {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            border-color: #4834DF;
            color: white;
            box-shadow: 0 4px 15px rgba(72, 52, 223, 0.3);
        }

        /* tambahan khusus tombol custom */
        .filter-pill-custom:hover {
            border-color: #a0a0a0;
            background: #f5f5f5;
            color: #333;
        }

        .filter-pill-custom.active {
            background: #4834DF !important;
            border-color: #4834DF !important;
            color: #fff !important;
        }

        /* ========== Advanced Filter ========== */
        .advanced-filter {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .advanced-filter .form-control-lg {
            height: calc(1.5em + 1rem + 2px);
            font-size: 0.9375rem;
        }

        .advanced-filter .btn-lg {
            height: calc(1.5em + 1rem + 2px);
        }

        .advanced-filter .alert-info {
            background-color: #e7f6fc;
            border-color: #8ad4ee;
            color: #115293;
        }

        /* Checkbox styling */
        .custom-control-label {
            cursor: pointer;
            user-select: none;
        }

        .custom-control-input:checked~.custom-control-label::before {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        /* ========== DataTable Wrapper ========== */
        .datatable-wrapper {
            padding: 1rem;
        }

        /* ========== DataTable Styling (selaras dengan Produk) ========== */
        #sales-table thead th {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 14px 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        #sales-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        #sales-table tbody tr {
            transition: all 0.2s ease;
        }

        #sales-table tbody tr:hover {
            background-color: rgba(72, 52, 223, 0.03) !important;
        }

        /* highlight row + child detail */
        #sales-table tbody tr.shown {
            background-color: rgba(72, 52, 223, 0.05) !important;
        }

        #sales-table tbody tr.shown+tr {
            background-color: #f8fafc;
            border-left: 4px solid #4834DF;
        }

        /* DataTable Controls */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 8px 12px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 4px 8px;
            margin: 0 4px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 6px 12px;
            margin-left: 8px;
        }

        /* Empty State */
        .dataTables_empty {
            padding: 40px 20px !important;
            text-align: center;
            color: #6c757d;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {
            .quick-filters {
                flex-direction: column;
            }

            .filter-pill {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .stats-card {
                flex-direction: column;
                text-align: center;
            }

            .datatable-wrapper {
                padding: 0.5rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    {{ $dataTable->scripts() }}

    <script>
        // ===== Config & Helpers =====
        const SUMMARY_URL = "{{ route('sales.summary') }}";

        function rupiah(n) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(Number(n || 0));
        }

        function collectFilters() {
            return {
                preset: document.getElementById('filter_preset')?.value || '',
                bulan: document.getElementById('filter_bulan')?.value || '',
                dari: document.getElementById('filter_dari')?.value || '',
                sampai: document.getElementById('filter_sampai')?.value || '',
                has_adjustment: document.getElementById('filter_has_adjustment')?.checked ? 1 : '',
                has_manual: document.getElementById('filter_has_manual')?.checked ? 1 : ''
            };
        }

        function clearAll() {
            document.getElementById('filter_preset').value = '';
            ['filter_bulan', 'filter_dari', 'filter_sampai'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            const adj = document.getElementById('filter_has_adjustment');
            if (adj) adj.checked = false;
            const man = document.getElementById('filter_has_manual');
            if (man) man.checked = false;
            document.querySelectorAll('.filter-pill').forEach(pill => pill.classList.remove('active'));
        }

        function updateSummary(params) {
            $.get(SUMMARY_URL, {
                    filter: params
                })
                .then(function(d) {
                    $('#sum-total').text(rupiah(d.total_penjualan || 0));
                    $('#sum-profit').text(rupiah(d.total_profit || 0));
                    $('#sum-count').text(d.total_transaksi || 0);
                })
                .fail(function(xhr) {
                    console.error('Gagal ambil summary', xhr.status, xhr.responseText);
                });
        }

        // ===== DataTable wiring =====
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('init.dt', function(e, settings) {
                if (settings.sTableId !== 'sales-table') return;

                const table = $('#sales-table').DataTable();

                // inject filter ke ajax
                $('#sales-table').on('preXhr.dt', function(evt, set, data) {
                    data.filter = collectFilters();
                });

                // Refresh summary setiap draw
                table.on('draw', function() {
                    updateSummary(collectFilters());
                });

                // panggil sekali untuk draw pertama
                updateSummary(collectFilters());

                // Quick filter pills (preset)
                document.querySelectorAll('.filter-pill[data-preset]').forEach(pill => {
                    pill.addEventListener('click', function() {
                        document.querySelectorAll('.filter-pill').forEach(p => p.classList
                            .remove('active'));
                        this.classList.add('active');

                        document.getElementById('filter_preset').value = this.dataset
                        .preset;
                        ['filter_bulan', 'filter_dari', 'filter_sampai'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.value = '';
                        });

                        table.ajax.reload();
                    });
                });

                // Manual filter change (hapus preset saat ubah manual)
                ['filter_bulan', 'filter_dari', 'filter_sampai'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('change', () => {
                            document.getElementById('filter_preset').value = '';
                            document.querySelectorAll('.filter-pill').forEach(p => p
                                .classList.remove('active'));
                        });
                    }
                });

                // Checkbox (kalau mau auto reload, uncomment)
                document.getElementById('filter_has_adjustment')?.addEventListener('change', () => {
                    // table.ajax.reload();
                });
                document.getElementById('filter_has_manual')?.addEventListener('change', () => {
                    // table.ajax.reload();
                });

                // Apply
                document.getElementById('btn_filter_apply')?.addEventListener('click', () => {
                    table.ajax.reload();
                });

                // Reset
                document.getElementById('btn_filter_reset')?.addEventListener('click', () => {
                    clearAll();
                    const advFilter = document.getElementById('advancedFilter');
                    if (advFilter && advFilter.style.display === 'block') {
                        advFilter.style.display = 'none';
                        document.getElementById('customFilterToggle')?.classList.remove('active');
                    }
                    table.ajax.reload();
                });

                // Toggle Advanced
                document.getElementById('customFilterToggle')?.addEventListener('click', function() {
                    const advancedFilter = document.getElementById('advancedFilter');
                    if (!advancedFilter) return;
                    const show = (advancedFilter.style.display === 'none' || !advancedFilter.style
                        .display);
                    advancedFilter.style.display = show ? 'block' : 'none';
                    this.classList.toggle('active', show);
                });

                // Child row detail toggle
                $('#sales-table tbody').on('click', '.btn-expand, .btn-row-detail', function() {
                    const $btn = $(this);
                    const tr = $btn.closest('tr');
                    const row = table.row(tr);
                    const url = $btn.data('url');

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                        $btn.html('<i class="bi bi-chevron-down"></i>');
                        return;
                    }

                    $btn.prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm"></span>');

                    $.get(url, function(html) {
                        row.child(html).show();
                        tr.addClass('shown');
                        $btn.html('<i class="bi bi-chevron-up"></i>');
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat Detail',
                            text: `Error ${jqXHR.status}: ${errorThrown}`,
                            confirmButtonColor: '#4834DF',
                            confirmButtonText: 'OK'
                        });
                        $btn.html('<i class="bi bi-chevron-down"></i>');
                    }).always(function() {
                        $btn.prop('disabled', false);
                    });
                });
            });
        });
    </script>
@endpush
