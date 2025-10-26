@extends('layouts.app')

@section('title', 'Merek Produk')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
    <li class="breadcrumb-item active">Merek</li>
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
                            <i class="cil-bookmark mr-2 text-primary"></i>
                            Merek Produk
                        </h5>
                        <small class="text-muted">Kelola merek ban dan velg</small>
                    </div>
                    
                    <a href="{{ route('brands.create') }}" class="btn btn-primary">
                        <i class="cil-plus mr-2"></i> Tambah Merek
                    </a>
                </div>
            </div>

            {{-- Info Section --}}
            <div class="card-body border-bottom" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                <div class="alert alert-info mb-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="cil-info-circle mr-2 mt-1" style="font-size: 1.25rem;"></i>
                        <div>
                            <strong>Informasi:</strong> Merek digunakan untuk mengidentifikasi produsen produk. 
                            Pastikan nama merek yang diinput sudah sesuai dengan merek asli produk.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0" width="80">No.</th>
                                <th class="border-0">Nama Merek</th>
                                <th class="border-0 text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                            <tr>
                                <td class="align-middle">
                                    <span class="badge badge-light">{{ $loop->iteration }}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="brand-icon mr-2">
                                            <i class="cil-bookmark"></i>
                                        </div>
                                        <strong>{{ $brand->name }}</strong>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('brands.edit', $brand->id) }}" 
                                           class="btn btn-outline-warning"
                                           data-toggle="tooltip"
                                           title="Edit">
                                            <i class="cil-pencil"></i>
                                        </a>
                                        
                                        <button type="button"
                                                class="btn btn-outline-danger btn-delete"
                                                data-id="{{ $brand->id }}"
                                                data-name="{{ $brand->name }}"
                                                data-toggle="tooltip"
                                                title="Hapus">
                                            <i class="cil-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $brand->id }}" 
                                              action="{{ route('brands.destroy', $brand->id) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="mb-0 mt-3 font-weight-semibold">Belum ada merek</p>
                                        <small>Klik tombol "Tambah Merek" untuk mulai menambah data</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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

    /* ========== Table Styling ========== */
    .table thead th {
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #4f5d73;
        padding: 14px 12px;
    }

    .table tbody td {
        padding: 14px 12px;
        font-size: 0.875rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03) !important;
    }

    /* ========== Brand Icon ========== */
    .brand-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        box-shadow: 0 2px 6px rgba(72, 52, 223, 0.2);
        flex-shrink: 0;
    }

    /* ========== Badge Styling ========== */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 600;
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
        .table thead th,
        .table tbody td {
            padding: 10px 8px;
            font-size: 0.8125rem;
        }
        
        .brand-icon {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@push('page_scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Delete confirmation
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Merek?',
            html: `Merek <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-muted">Pastikan tidak ada produk yang menggunakan merek ini!</small>`,
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
                
                $('#delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endpush
