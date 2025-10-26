@extends('layouts.app')

@section('title', 'Mata Uang')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Mata Uang</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        {{-- Alerts --}}
        @include('utils.alerts')

        {{-- Main Card --}}
        <div class="card shadow-sm">
            {{-- Card Header --}}
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <h5 class="mb-1 font-weight-bold">
                            <i class="cil-dollar mr-2 text-primary"></i>
                            Daftar Mata Uang
                        </h5>
                        <small class="text-muted">Kelola mata uang untuk transaksi multi-currency</small>
                    </div>
                    
                    @can('create_currencies')
                    <a href="{{ route('currencies.create') }}" class="btn btn-primary">
                        <i class="cil-plus mr-2"></i> Tambah Mata Uang
                    </a>
                    @endcan
                </div>
            </div>

            {{-- Info Section --}}
            <div class="card-body border-bottom" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                <div class="alert alert-info mb-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="cil-info-circle mr-2 mt-1" style="font-size: 1.25rem;"></i>
                        <div>
                            <strong>Informasi:</strong> Mata uang digunakan untuk transaksi dengan nilai tukar berbeda. 
                            Anda dapat menambahkan, mengedit, atau menghapus mata uang sesuai kebutuhan bisnis.
                        </div>
                    </div>
                </div>
            </div>

            {{-- DataTable --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="datatable-wrapper">
                        {!! $dataTable->table(['class' => 'table table-hover mb-0', 'id' => 'currencies-table']) !!}
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
    #currencies-table thead th {
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #4f5d73;
        padding: 14px 12px;
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #e9ecef;
    }

    #currencies-table tbody td {
        padding: 14px 12px;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    #currencies-table tbody tr {
        transition: all 0.2s ease;
    }

    #currencies-table tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03) !important;
    }

    /* ========== Currency Symbol Styling ========== */
    .currency-symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        border-radius: 8px;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 2px 6px rgba(72, 52, 223, 0.2);
    }

    /* ========== Badge Styling ========== */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 600;
    }

    /* ========== DataTable Controls ========== */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 6px 12px;
    }

    /* ========== Button Group ========== */
    .btn-group .btn {
        transition: all 0.2s ease;
    }

    .btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* ========== Responsive ========== */
    @media (max-width: 768px) {
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
    // Delete confirmation
    $(document).on('click', '.delete-currency', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Mata Uang?',
            html: `Mata uang <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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

    // Set default currency
    $(document).on('click', '.set-default-currency', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Set Sebagai Default?',
            html: `Mata uang <strong>"${name}"</strong> akan dijadikan default untuk semua transaksi.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4834DF',
            cancelButtonColor: '#768192',
            confirmButtonText: '<i class="cil-check-circle mr-1"></i> Ya, Set Default!',
            cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
@endpush
