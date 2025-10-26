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
        {{-- Statistics Cards --}}
        <div class="row mb-4">
            {{-- Total Penyesuaian --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card stats-card-purple">
                    <div class="stats-icon">
                        <i class="cil-file-text"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total Penyesuaian</div>
                        <div class="stats-value">
                            {{ \Modules\Adjustment\Entities\Adjustment::count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Penambahan --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card stats-card-success">
                    <div class="stats-icon">
                        <i class="cil-arrow-circle-top"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Penambahan</div>
                        <div class="stats-value">
                            {{ \Modules\Adjustment\Entities\AdjustedProduct::where('type', 'add')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pengurangan --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card stats-card-danger">
                    <div class="stats-icon">
                        <i class="cil-arrow-circle-bottom"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Pengurangan</div>
                        <div class="stats-value">
                            {{ \Modules\Adjustment\Entities\AdjustedProduct::where('type', 'sub')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bulan Ini --}}
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card stats-card-info">
                    <div class="stats-icon">
                        <i class="cil-calendar-check"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Bulan Ini</div>
                        <div class="stats-value">
                            {{ \Modules\Adjustment\Entities\Adjustment::whereMonth('date', date('m'))->whereYear('date', date('Y'))->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="card shadow-sm">
            {{-- Card Header --}}
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

            {{-- Info Alert --}}
            <div class="card-body border-bottom" style="background-color: #f8f9fa;">
                <div class="alert alert-info mb-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="cil-info-circle mr-2 mt-1" style="font-size: 1.25rem;"></i>
                        <div>
                            <strong>Informasi Penting:</strong> Penyesuaian stok digunakan untuk menambah atau mengurangi stok produk secara manual. 
                            Gunakan fitur ini untuk koreksi stok, barang rusak, atau penambahan dari supplier lain.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Filters --}}
            <div class="card-body py-4 border-bottom" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                <div class="filter-container">
                    <div class="d-flex align-items-center mb-3">
                        <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                        <h6 class="mb-0 font-weight-bold text-dark">Filter Data</h6>
                    </div>

                    <div class="row">
                        {{-- Filter Tipe --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label small font-weight-semibold text-dark mb-2">
                                <i class="cil-filter mr-1 text-muted"></i> Tipe Penyesuaian
                            </label>
                            <select id="filter-type" class="form-control">
                                <option value="">Semua Tipe</option>
                                <option value="add">Penambahan</option>
                                <option value="sub">Pengurangan</option>
                            </select>
                        </div>

                        {{-- Dari Tanggal --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label small font-weight-semibold text-dark mb-2">
                                <i class="cil-calendar mr-1 text-muted"></i> Dari Tanggal
                            </label>
                            <input type="date" id="filter-date-from" class="form-control">
                        </div>

                        {{-- Sampai Tanggal --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label small font-weight-semibold text-dark mb-2">
                                <i class="cil-calendar mr-1 text-muted"></i> Sampai Tanggal
                            </label>
                            <input type="date" id="filter-date-to" class="form-control">
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label small font-weight-semibold text-dark mb-2 d-block">
                                &nbsp;
                            </label>
                            <div class="btn-group w-100" role="group">
                                <button id="btn-filter" class="btn btn-primary">
                                    <i class="cil-filter mr-1"></i> Filter
                                </button>
                                <button id="btn-reset" class="btn btn-outline-secondary">
                                    <i class="cil-reload mr-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DataTable --}}
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

    /* ========== Card Shadow ========== */
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }

    /* ========== Statistics Cards ========== */
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

    .stats-card-danger {
        border-left-color: #e55353;
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

    .stats-card-danger .stats-icon {
        background: linear-gradient(135deg, #e55353 0%, #f27474 100%);
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

    /* ========== Alert Styling ========== */
    .alert-info {
        background-color: #e7f6fc;
        border-color: #8ad4ee;
        color: #115293;
        border-radius: 8px;
    }

    /* ========== DataTable Wrapper ========== */
    .datatable-wrapper {
        padding: 1rem;
    }

    /* ========== DataTable Styling ========== */
    #adjustments-table thead th {
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #4f5d73;
        padding: 14px 12px;
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #e9ecef;
    }

    #adjustments-table tbody td {
        padding: 14px 12px;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    #adjustments-table tbody tr {
        transition: all 0.2s ease;
    }

    #adjustments-table tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03) !important;
    }

    /* ========== DataTable Controls ========== */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 6px 12px;
    }

    /* ========== Responsive ========== */
    @media (max-width: 768px) {
        .stats-card {
            flex-direction: column;
            text-align: center;
        }

        .stats-icon {
            margin: 0 auto;
        }

        .datatable-wrapper {
            padding: 0.5rem;
        }
    }
</style>
@endpush

@push('page_scripts')
{!! $dataTable->scripts() !!}

<script>
$(document).ready(function() {
    // Filter functionality
    $('#btn-filter').click(function() {
        const type = $('#filter-type').val();
        const dateFrom = $('#filter-date-from').val();
        const dateTo = $('#filter-date-to').val();
        
        console.log('Filtering:', {type, dateFrom, dateTo});
        
        // Reload datatable with filters
        // Implement this in your DataTable class
        if (typeof window.LaravelDataTables !== 'undefined' && window.LaravelDataTables["adjustments-table"]) {
            window.LaravelDataTables["adjustments-table"].ajax.reload();
        }
    });
    
    // Reset filters
    $('#btn-reset').click(function() {
        $('#filter-type').val('');
        $('#filter-date-from').val('');
        $('#filter-date-to').val('');
        
        // Reload datatable
        if (typeof window.LaravelDataTables !== 'undefined' && window.LaravelDataTables["adjustments-table"]) {
            window.LaravelDataTables["adjustments-table"].ajax.reload();
        }
    });
    
    // Delete confirmation
    $(document).on('click', '.delete-adjustment', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        
        Swal.fire({
            title: 'Hapus Penyesuaian?',
            html: '<small class="text-muted">Stok produk akan dikembalikan ke kondisi sebelum penyesuaian.</small><br><strong>Yakin ingin menghapus?</strong>',
            icon: 'warning',
            iconColor: '#e55353',
            showCancelButton: true,
            confirmButtonColor: '#e55353',
            cancelButtonColor: '#768192',
            confirmButtonText: '<i class="cil-trash mr-1"></i> Ya, Hapus!',
            cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Create and submit form
                const form = $('<form>', {
                    'method': 'POST',
                    'action': url
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': '{{ csrf_token() }}'
                }));
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));
                
                $('body').append(form);
                form.submit();
            }
        });
    });
});
</script>
@endpush
