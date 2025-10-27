@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
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
                            <i class="cil-people mr-2 text-primary"></i>
                            Manajemen Pengguna
                        </h5>
                        <small class="text-muted">Kelola akses dan izin pengguna sistem</small>
                    </div>
                    
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="cil-user-plus mr-2"></i> Tambah Pengguna
                    </a>
                </div>
            </div>

            {{-- Info Section --}}
            <div class="card-body border-bottom" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                <div class="alert alert-info mb-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="cil-shield-alt mr-2 mt-1" style="font-size: 1.25rem;"></i>
                        <div>
                            <strong>Keamanan Sistem:</strong> Kelola akses pengguna dengan bijak. 
                            Pastikan setiap pengguna memiliki role dan permissions yang sesuai dengan tanggung jawabnya.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-hover mb-0'], true) !!}
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
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6 !important;
    }

    .table tbody td {
        padding: 14px 12px;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(72, 52, 223, 0.03) !important;
    }

    /* ========== Badge Styling ========== */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 600;
        border-radius: 6px;
    }

    /* ========== Button Styling ========== */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8125rem;
        border-radius: 6px;
    }

    .btn-group .btn {
        transition: all 0.2s ease;
    }

    .btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* ========== DataTables Custom Styling ========== */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: 1rem;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        font-weight: 500;
        margin-bottom: 0;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
        margin-left: 0.5rem;
        transition: all 0.3s ease;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4834DF;
        box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        padding: 1rem;
    }

    /* ========== Pagination Styling ========== */
    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        border-radius: 6px;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        color: #495057;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background-color: #4834DF;
        border-color: #4834DF;
        color: white;
    }

    .page-item.active .page-link {
        background-color: #4834DF;
        border-color: #4834DF;
    }

    /* ========== User Avatar (if used in DataTable) ========== */
    .user-avatar {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 2px 6px rgba(72, 52, 223, 0.2);
        flex-shrink: 0;
    }

    /* ========== Role Badge Colors ========== */
    .badge-admin {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
    }

    .badge-manager {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
    }

    .badge-staff {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    .badge-cashier {
        background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
        color: white;
    }

    /* ========== Status Badge ========== */
    .badge-active {
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        color: white;
    }

    .badge-inactive {
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
        color: white;
    }

    /* ========== Responsive ========== */
    @media (max-width: 768px) {
        .table thead th,
        .table tbody td {
            padding: 10px 8px;
            font-size: 0.8125rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@push('page_scripts')
{!! $dataTable->scripts() !!}

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Custom DataTable styling after initialization
    $('.dataTable').addClass('table-hover');
    
    // Add custom icons to action buttons if needed
    $('.dataTable tbody').on('click', '.btn-delete', function() {
        const userName = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Pengguna?',
            html: `Pengguna <strong>"${userName}"</strong> akan dihapus dari sistem.<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan!</small>`,
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
                // Submit delete form
                $(this).closest('form').submit();
            }
        });
    });

    // Format role badges
    $('.dataTable tbody td').each(function() {
        const text = $(this).text().trim().toLowerCase();
        
        if ($(this).find('.badge').length > 0) {
            const badge = $(this).find('.badge');
            
            // Add appropriate badge class based on role
            if (text.includes('admin')) {
                badge.addClass('badge-admin');
            } else if (text.includes('manager')) {
                badge.addClass('badge-manager');
            } else if (text.includes('staff')) {
                badge.addClass('badge-staff');
            } else if (text.includes('cashier') || text.includes('kasir')) {
                badge.addClass('badge-cashier');
            }
            
            // Add status badge styling
            if (text.includes('active') || text.includes('aktif')) {
                badge.addClass('badge-active');
            } else if (text.includes('inactive') || text.includes('nonaktif')) {
                badge.addClass('badge-inactive');
            }
        }
    });
});
</script>
@endpush
