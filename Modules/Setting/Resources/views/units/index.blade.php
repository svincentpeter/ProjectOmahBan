@extends('layouts.app')

@section('title', 'Satuan Produk')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Satuan</li>
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
                            <i class="cil-calculator mr-2 text-primary"></i>
                            Satuan Produk
                        </h5>
                        <small class="text-muted">Kelola satuan dan konversi unit produk</small>
                    </div>
                    
                    @can('create_units')
                    <a href="{{ route('units.create') }}" class="btn btn-primary">
                        <i class="cil-plus mr-2"></i> Tambah Satuan
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
                            <strong>Informasi:</strong> Satuan digunakan untuk mengatur unit pengukuran produk. 
                            Operator dan nilai operasi digunakan untuk konversi antar satuan.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="data-table">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0" width="80">No.</th>
                                <th class="border-0">Nama Satuan</th>
                                <th class="border-0 text-center" width="120">Singkatan</th>
                                <th class="border-0 text-center" width="100">Operator</th>
                                <th class="border-0 text-center" width="120">Nilai Operasi</th>
                                <th class="border-0 text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $unit)
                            <tr>
                                <td class="align-middle">
                                    <span class="badge badge-light">{{ $loop->iteration }}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="unit-icon mr-2">
                                            <i class="cil-calculator"></i>
                                        </div>
                                        <strong>{{ $unit->name }}</strong>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-primary">{{ $unit->short_name }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="operator-badge">{{ $unit->operator }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="font-weight-semibold">{{ $unit->operation_value }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('edit_units')
                                        <a href="{{ route('units.edit', $unit) }}" 
                                           class="btn btn-outline-warning"
                                           data-toggle="tooltip"
                                           title="Edit">
                                            <i class="cil-pencil"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete_units')
                                        <button type="button"
                                                class="btn btn-outline-danger btn-delete"
                                                data-id="{{ $unit->id }}"
                                                data-name="{{ $unit->name }}"
                                                data-toggle="tooltip"
                                                title="Hapus">
                                            <i class="cil-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $unit->id }}" 
                                              action="{{ route('units.destroy', $unit) }}" 
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
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="cil-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="mb-0 mt-3 font-weight-semibold">Belum ada satuan</p>
                                        <small>Klik tombol "Tambah Satuan" untuk mulai menambah data</small>
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

    /* ========== Unit Icon ========== */
    .unit-icon {
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

    /* ========== Operator Badge ========== */
    .operator-badge {
        display: inline-block;
        width: 32px;
        height: 32px;
        line-height: 32px;
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        font-weight: 700;
        font-size: 1rem;
        color: #495057;
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
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        padding: 1rem;
    }

    div.dt-buttons {
        display: inline-flex;
        gap: 0.5rem;
    }

    .dt-button {
        background: white !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 6px !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
        color: #495057 !important;
        transition: all 0.2s ease !important;
    }

    .dt-button:hover {
        background: #f8f9fa !important;
        border-color: #4834DF !important;
        color: #4834DF !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(72, 52, 223, 0.15) !important;
    }

    /* ========== Responsive ========== */
    @media (max-width: 768px) {
        .table thead th,
        .table tbody td {
            padding: 10px 8px;
            font-size: 0.8125rem;
        }
        
        .unit-icon {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
        }

        .operator-badge {
            width: 28px;
            height: 28px;
            line-height: 28px;
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@push('page_scripts')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize DataTable
    var table = $('#data-table').DataTable({
        dom: "<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>>tr<'row'<'col-md-5'i><'col-md-7 mt-2'p>>",
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
        },
        buttons: [
            {
                extend: 'excel',
                text: '<i class="cil-cloud-download mr-1"></i> Excel',
                className: 'btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'csv',
                text: '<i class="cil-cloud-download mr-1"></i> CSV',
                className: 'btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'print',
                text: '<i class="cil-print mr-1"></i> Cetak',
                className: 'btn-sm',
                title: 'Satuan Produk',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },
                customize: function(win) {
                    $(win.document.body)
                        .css('font-size', '10pt')
                        .prepend(
                            '<div style="text-align:center; margin-bottom: 20px;">' +
                            '<h2 style="margin: 0;">Satuan Produk</h2>' +
                            '<p style="margin: 5px 0;">Dicetak pada: ' + new Date().toLocaleString('id-ID') + '</p>' +
                            '</div>'
                        );

                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '9pt');
                }
            }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true
    });

    // Delete confirmation with SweetAlert2
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Satuan?',
            html: `Satuan <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-muted">Pastikan tidak ada produk yang menggunakan satuan ini!</small>`,
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
