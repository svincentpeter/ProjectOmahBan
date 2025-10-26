@extends('layouts.app')

@section('title', 'Kategori Pengeluaran')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Kategori Pengeluaran</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        {{-- Success/Error Alert --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="cil-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="cil-warning mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        @endif

        {{-- Main Card --}}
        <div class="card shadow-sm">
            {{-- Card Header --}}
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <h5 class="mb-1 font-weight-bold">
                            <i class="cil-folder-open mr-2 text-primary"></i>
                            Kategori Pengeluaran
                        </h5>
                        <small class="text-muted">Kelola kategori untuk pencatatan pengeluaran</small>
                    </div>
                    
                    @can('create_expense_categories')
                    <a href="{{ route('expense-categories.create') }}" 
                       class="btn btn-primary">
                        <i class="cil-plus mr-2"></i> Tambah Kategori
                    </a>
                    @endcan
                </div>
            </div>

            {{-- Stats Bar --}}
            <div class="card-body py-3 border-bottom" style="background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);">
                <div class="row align-items-center text-white">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="cil-layers" style="font-size: 2.5rem; opacity: 0.9;"></i>
                            </div>
                            <div>
                                <small class="d-block" style="opacity: 0.8;">Total Kategori Terdaftar</small>
                                <h3 class="mb-0 font-weight-bold">
                                    {{ $categories->total() }} Kategori
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <small class="d-block" style="opacity: 0.8;">Status</small>
                        <h5 class="mb-0 font-weight-semibold">
                            <i class="cil-check-circle mr-1"></i> Aktif
                        </h5>
                    </div>
                </div>
            </div>
            
            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th width="60" class="text-center border-0">No</th>
                                <th class="border-0">Nama Kategori</th>
                                <th class="border-0">Deskripsi</th>
                                <th width="150" class="text-center border-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $c)
                            <tr>
                                <td class="text-center">
                                    <span class="text-muted font-weight-semibold">
                                        {{ $categories->firstItem() + $index }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="cil-tag text-white" style="font-size: 0.875rem;"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $c->category_name }}</div>
                                            <small class="text-muted">ID: {{ $c->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ Str::limit($c->category_description ?? 'Tidak ada deskripsi', 60) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('edit_expense_categories')
                                        <a href="{{ route('expense-categories.edit', $c->id) }}" 
                                           class="btn btn-outline-warning"
                                           data-toggle="tooltip"
                                           title="Edit Kategori">
                                            <i class="cil-pencil"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete_expense_categories')
                                        <button type="button"
                                                class="btn btn-outline-danger btn-delete"
                                                data-id="{{ $c->id }}"
                                                data-name="{{ $c->category_name }}"
                                                data-toggle="tooltip"
                                                title="Hapus Kategori">
                                            <i class="cil-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $c->id }}" 
                                              action="{{ route('expense-categories.destroy', $c->id) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="mb-0 mt-3 font-weight-semibold">Belum ada kategori pengeluaran</p>
                                        <small>Klik tombol "Tambah Kategori" untuk membuat kategori baru</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($categories->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $categories->firstItem() }} - {{ $categories->lastItem() }} 
                        dari {{ $categories->total() }} kategori
                    </small>
                    {{ $categories->links() }}
                </div>
            </div>
            @endif
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
    
    /* ========== Table Enhancements ========== */
    .table thead th {
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #4f5d73;
        padding: 12px 16px;
    }

    .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03);
        transform: scale(1.002);
    }
    
    /* ========== Category Icon ========== */
    .table tbody td > div > div[style*="gradient"] {
        box-shadow: 0 2px 6px rgba(72, 52, 223, 0.2);
        transition: all 0.3s ease;
    }

    .table tbody tr:hover td > div > div[style*="gradient"] {
        box-shadow: 0 4px 12px rgba(72, 52, 223, 0.3);
        transform: scale(1.05);
    }
    
    /* ========== Button Group ========== */
    .btn-group .btn {
        margin: 0 2px;
        border-radius: 4px !important;
        transition: all 0.2s ease;
    }

    .btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-outline-warning:hover {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }

    .btn-outline-danger:hover {
        background-color: #e55353;
        border-color: #e55353;
        color: #fff;
    }

    /* ========== Tooltip Fix ========== */
    .tooltip {
        font-size: 0.75rem;
    }
</style>
@endpush

@push('page_scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 3 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);

    // Initialize Bootstrap tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Delete confirmation with SweetAlert2
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Kategori <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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
                
                // Submit form
                $('#delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endpush
