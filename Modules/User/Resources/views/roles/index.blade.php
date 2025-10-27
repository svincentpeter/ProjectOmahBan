@extends('layouts.app')

@section('title', 'Peran & Hak Akses')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Peran & Hak Akses</li>
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
                            <i class="cil-shield-alt mr-2 text-primary"></i>
                            Peran & Hak Akses
                        </h5>
                        <small class="text-muted">Kelola peran pengguna dan permission untuk akses sistem</small>
                    </div>
                    
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="cil-plus mr-2"></i> Tambah Peran
                    </a>
                </div>
            </div>

            {{-- Info Section --}}
            <div class="card-body border-bottom" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="alert alert-warning mb-md-0" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="cil-warning mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                <div>
                                    <strong>Perhatian:</strong> Peran mengontrol akses pengguna ke fitur sistem. 
                                    Pastikan memberikan permissions yang tepat untuk menjaga keamanan data.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <div class="role-info-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="role-icon mr-2">
                                        <i class="cil-badge"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Total Peran</small>
                                        <strong class="role-count">Lihat tabel</strong>
                                    </div>
                                </div>
                            </div>
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
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffc107;
        color: #856404;
        border-radius: 8px;
    }

    /* ========== Role Info Card ========== */
    .role-info-card {
        background: white;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .role-info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .role-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 2px 6px rgba(72, 52, 223, 0.2);
        flex-shrink: 0;
    }

    .role-count {
        font-size: 1rem;
        color: #4834DF;
        font-weight: 700;
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

    /* ========== Role Badge Colors ========== */
    .badge-role {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .badge-super-admin {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
    }

    .badge-admin {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
    }

    .badge-manager {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    .badge-permissions {
        background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
        color: white;
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

    /* ========== Permission List Styling ========== */
    .permission-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        max-width: 400px;
    }

    .permission-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #495057;
    }

    /* ========== Responsive ========== */
    @media (max-width: 768px) {
        .table thead th,
        .table tbody td {
            padding: 10px 8px;
            font-size: 0.8125rem;
        }

        .role-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }

        .role-info-card {
            margin-top: 1rem;
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
    
    // Format role badges based on name
    $('.dataTable tbody td').each(function() {
        const text = $(this).text().trim().toLowerCase();
        
        if ($(this).find('.badge').length > 0) {
            const badge = $(this).find('.badge');
            
            // Add appropriate badge class based on role name
            if (text.includes('super admin')) {
                badge.addClass('badge-super-admin');
            } else if (text.includes('admin')) {
                badge.addClass('badge-admin');
            } else if (text.includes('manager')) {
                badge.addClass('badge-manager');
            } else {
                badge.addClass('badge-role');
            }
        }
    });

    // Delete confirmation with SweetAlert2
    $('.dataTable tbody').on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const roleName = $(this).data('name');
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Hapus Peran?',
            html: `Peran <strong>"${roleName}"</strong> akan dihapus dari sistem.<br><br>` +
                  `<small class="text-danger"><i class="cil-warning mr-1"></i>` +
                  `Pengguna dengan peran ini akan kehilangan akses mereka!</small>`,
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
                
                form.submit();
            }
        });
    });

    // Add permission count badges
    $('.dataTable tbody tr').each(function() {
        const permissionCell = $(this).find('td').eq(-2); // Assuming permissions is second-to-last column
        const permissionText = permissionCell.text().trim();
        
        if (permissionText && permissionText !== '-') {
            const permissions = permissionText.split(',').map(p => p.trim());
            let html = '<div class="permission-list">';
            
            permissions.slice(0, 3).forEach(permission => {
                html += `<span class="permission-badge">${permission}</span>`;
            });
            
            if (permissions.length > 3) {
                html += `<span class="badge badge-permissions">+${permissions.length - 3} lainnya</span>`;
            }
            
            html += '</div>';
            permissionCell.html(html);
        }
    });
});
</script>
@endpush
