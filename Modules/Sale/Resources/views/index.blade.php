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
                <div class="card-body py-4" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        {{-- Header with Reset Button --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                                <h6 class="mb-0 font-weight-bold text-dark">Filter Cepat</h6>
                            </div>
                            <button id="btn_filter_reset" class="btn btn-outline-secondary btn-sm">
                                <i class="cil-reload mr-1"></i> Reset
                            </button>
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

                                {{-- ✅ FILTER BARU: Hanya dengan Diskon --}}
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

                                {{-- Apply Button --}}
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label small font-weight-semibold text-dark mb-2 d-block">
                                        &nbsp;
                                    </label>
                                    <button id="btn_filter_apply" class="btn btn-primary btn-lg w-100">
                                        <i class="cil-filter mr-2"></i> Terapkan Filter
                                    </button>
                                </div>
                            </div>

                            {{-- Info Text --}}
                            <div class="alert alert-info mb-0" role="alert">
                                <i class="cil-info-circle mr-2"></i>
                                <small>
                                    <strong>Catatan:</strong> Jika mengisi rentang tanggal (Dari-Sampai),
                                    maka pilihan preset dan bulan akan diabaikan secara otomatis.
                                </small>
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

        /* ========== Card Styling ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Quick Filter Pills ========== */
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

        .filter-pill.active:hover {
            background: linear-gradient(135deg, #3d2bb8 0%, #5a5fc9 100%);
            transform: translateY(-2px);
        }

        .filter-pill-custom:hover {
            border-color: #a0a0a0;
            background: #f5f5f5;
            color: #333;
        }

        .filter-pill-custom.active {
            background: #4834DF !important;
            border-color: #4834DF !important;
            color: white !important;
        }

        /* ========== Advanced Filter ========== */
        .advanced-filter {
            margin-top: 20px;
            padding: 20px;
            background: white;
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

        /* ✅ STYLING CHECKBOX FILTER DISKON */
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

        /* ========== DataTable Styling ========== */
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

        /* Child row styling */
        #sales-table tbody tr.shown {
            background-color: rgba(72, 52, 223, 0.05) !important;
        }

        #sales-table tbody tr.shown+tr {
            background-color: #f8fafc;
            border-left: 4px solid #4834DF;
        }

        /* ========== DataTable Controls ========== */
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

        /* ========== Empty State ========== */
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
            .datatable-wrapper {
                padding: 0.5rem;
            }
        }

        
    </style>
@endpush

@push('page_scripts')
    {{ $dataTable->scripts() }}

    <script>
        function collectFilters() {
            return {
                preset: document.getElementById('filter_preset')?.value || '',
                bulan: document.getElementById('filter_bulan')?.value || '',
                dari: document.getElementById('filter_dari')?.value || '',
                sampai: document.getElementById('filter_sampai')?.value || '',
                has_adjustment: document.getElementById('filter_has_adjustment')?.checked ? '1' : ''
            };
        }

        $('#sales-table').on('preXhr.dt', function(e, settings, data) {
            console.log('DataTable AJAX Request Data:', data);
        });

        function clearAll() {
            document.getElementById('filter_preset').value = '';
            ['filter_bulan', 'filter_dari', 'filter_sampai'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            // ✅ RESET CHECKBOX DISKON
            document.getElementById('filter_has_adjustment').checked = false;

            document.querySelectorAll('.filter-pill').forEach(pill => pill.classList.remove('active'));
        }

        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('init.dt', function(e, settings) {
                if (settings.sTableId !== 'sales-table') return;

                const table = $('#sales-table').DataTable();

                // Inject filter data into AJAX requests
                $('#sales-table').on('preXhr.dt', function(evt, set, data) {
                    Object.assign(data, collectFilters());
                });

                // Quick filter pills handler
                document.querySelectorAll('.filter-pill[data-preset]').forEach(pill => {
                    pill.addEventListener('click', function() {
                        // Clear all active states
                        document.querySelectorAll('.filter-pill').forEach(p => p.classList
                            .remove('active'));

                        // Set this pill as active
                        this.classList.add('active');

                        // Set preset value
                        document.getElementById('filter_preset').value = this.dataset
                            .preset;

                        // Clear manual filters
                        document.getElementById('filter_bulan').value = '';
                        document.getElementById('filter_dari').value = '';
                        document.getElementById('filter_sampai').value = '';

                        // Reload table
                        table.ajax.reload();
                    });
                });

                // Manual filter change handler
                ['filter_bulan', 'filter_dari', 'filter_sampai'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('change', () => {
                            // Clear preset when manual filter changes
                            document.getElementById('filter_preset').value = '';
                            document.querySelectorAll('.filter-pill').forEach(p => p
                                .classList.remove('active'));
                        });
                    }
                });

                // ✅ HANDLER CHECKBOX DISKON - Auto reload saat diubah
                document.getElementById('filter_has_adjustment').addEventListener('change', function() {
                    // Optional: langsung reload tanpa klik "Terapkan Filter"
                    // Uncomment baris ini jika ingin auto-reload:
                    // table.ajax.reload();
                });

                // Apply button
                document.getElementById('btn_filter_apply').addEventListener('click', () => {
                    table.ajax.reload();
                });

                // Reset button
                document.getElementById('btn_filter_reset').addEventListener('click', () => {
                    clearAll();
                    // Hide advanced filter
                    const advFilter = document.getElementById('advancedFilter');
                    if (advFilter.style.display === 'block') {
                        advFilter.style.display = 'none';
                        document.getElementById('customFilterToggle').classList.remove('active');
                    }
                    table.ajax.reload();
                });

                // Custom filter toggle
                document.getElementById('customFilterToggle').addEventListener('click', function() {
                    const advancedFilter = document.getElementById('advancedFilter');
                    if (advancedFilter.style.display === 'none' || !advancedFilter.style.display) {
                        advancedFilter.style.display = 'block';
                        this.classList.add('active');
                    } else {
                        advancedFilter.style.display = 'none';
                        this.classList.remove('active');
                    }
                });

                // Child row detail toggle
                $('#sales-table tbody').on('click', '.btn-expand, .btn-row-detail', function() {
                    const $btn = $(this);
                    const tr = $btn.closest('tr');
                    const row = table.row(tr);
                    const url = $btn.data('url');

                    // If row already shown, hide it
                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                        $btn.html('<i class="bi bi-chevron-down"></i>');
                        return;
                    }

                    // Show loading state
                    $btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm"></span>');

                    // Fetch detail data
                    $.get(url, function(html) {
                        row.child(html).show();
                        tr.addClass('shown');
                        $btn.html('<i class="bi bi-chevron-up"></i>');
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        // Show error with SweetAlert
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
