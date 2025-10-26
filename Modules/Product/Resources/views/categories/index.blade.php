@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
    <li class="breadcrumb-item active">Kategori</li>
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
                            <i class="cil-folder mr-2 text-primary"></i>
                            Kategori Produk
                        </h5>
                        <small class="text-muted">Kelola kategori untuk mengorganisir produk</small>
                    </div>
                    
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryCreateModal">
                        <i class="cil-plus mr-2"></i> Tambah Kategori
                    </button>
                </div>
            </div>

            {{-- Info Section --}}
            <div class="card-body border-bottom" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                <div class="alert alert-info mb-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="cil-info-circle mr-2 mt-1" style="font-size: 1.25rem;"></i>
                        <div>
                            <strong>Informasi:</strong> Kategori membantu Anda mengorganisir produk dengan lebih baik. 
                            Setiap produk dapat ditempatkan dalam satu kategori untuk memudahkan pencarian dan manajemen.
                        </div>
                    </div>
                </div>
            </div>

            {{-- DataTable --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="datatable-wrapper">
                        {!! $dataTable->table(['class' => 'table table-hover mb-0', 'id' => 'categories-table']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create Modal --}}
@include('product::includes.category-modal')
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
    #categories-table thead th {
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #4f5d73;
        padding: 14px 12px;
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #e9ecef;
    }

    #categories-table tbody td {
        padding: 14px 12px;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    #categories-table tbody tr {
        transition: all 0.2s ease;
    }

    #categories-table tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03) !important;
    }

    /* ========== Modal Styling ========== */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 1.25rem 1.5rem;
    }

    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.125rem;
    }

    .modal-header .close {
        color: white;
        opacity: 0.9;
        text-shadow: none;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        border-radius: 0 0 12px 12px;
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
    $(document).on('click', '.delete-category', function(e) {
        e.preventDefault();
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Kategori <strong>"${categoryName}"</strong> akan dihapus permanen.<br><small class="text-muted">Pastikan tidak ada produk yang masih menggunakan kategori ini!</small>`,
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
                Swal.fire({
                    title: 'Menghapus...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $('#destroy' + categoryId).submit();
            }
        });
    });

    // Reset modal on close
    $('#categoryCreateModal').on('hidden.bs.modal', function () {
        $('#categoryForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
