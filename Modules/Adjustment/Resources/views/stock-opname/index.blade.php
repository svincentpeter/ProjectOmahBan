@extends('layouts.app')

@section('title', 'Stok Opname')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Stok Opname</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    {{-- HEADER SECTION --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">
                            <i class="bi bi-clipboard-check"></i> Daftar Stok Opname
                        </h4>
                        @can('create_stock_opname')
                        <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Buat Stok Opname Baru
                        </a>
                        @endcan
                    </div>

                    {{-- STATISTICS CARDS --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h5 class="text-muted mb-1">Total Opname</h5>
                                    <h2 class="mb-0 text-dark">{{ $stats['total'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white border-0">
                                <div class="card-body text-center">
                                    <h5 class="mb-1">Draft</h5>
                                    <h2 class="mb-0">{{ $stats['draft'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white border-0">
                                <div class="card-body text-center">
                                    <h5 class="mb-1">Sedang Berjalan</h5>
                                    <h2 class="mb-0">{{ $stats['in_progress'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white border-0">
                                <div class="card-body text-center">
                                    <h5 class="mb-1">Selesai</h5>
                                    <h2 class="mb-0">{{ $stats['completed'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FILTER SECTION --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Filter Status:</label>
                            <select id="filter-status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="draft">Draft</option>
                                <option value="in_progress">Sedang Berjalan</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Tanggal Dari:</label>
                            <input type="date" id="filter-date-from" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Tanggal Sampai:</label>
                            <input type="date" id="filter-date-to" class="form-control">
                        </div>
                    </div>

                    {{-- DATATABLE --}}
                    <div class="table-responsive">
                        <table id="stock-opname-table" class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Reference</th>
                                    <th>Tanggal Opname</th>
                                    <th>Petugas (PIC)</th>
                                    <th>Total Item</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Diisi oleh DataTables AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    var table = $('#stock-opname-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('stock-opnames.datatable') }}",
            data: function(d) {
                // Kirim filter ke server
                d.status = $('#filter-status').val();
                d.date_from = $('#filter-date-from').val();
                d.date_to = $('#filter-date-to').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            },
            { 
                data: 'reference', 
                name: 'reference',
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { 
                data: 'opname_date', 
                name: 'opname_date',
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY');
                }
            },
            { 
                data: 'pic_name', 
                name: 'pic.name' 
            },
            { 
                data: 'total_items', 
                name: 'total_items',
                className: 'text-center'
            },
            { 
                data: 'completion', 
                name: 'completion',
                orderable: false,
                render: function(data, type, row) {
                    var percentage = parseFloat(data);
                    var colorClass = percentage < 50 ? 'bg-danger' : 
                                    percentage < 100 ? 'bg-warning' : 'bg-success';
                    
                    return `
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar ${colorClass}" 
                                 role="progressbar" 
                                 style="width: ${percentage}%"
                                 aria-valuenow="${percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                ${percentage}%
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'status_badge', 
                name: 'status',
                orderable: false,
                className: 'text-center'
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[2, 'desc']], // Sort by opname_date descending
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-excel"></i> Export Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6] // Exclude progress & action columns
                }
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-pdf"></i> Export PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6]
                }
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer"></i> Print',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6]
                }
            }
        ]
    });

    // Event listener untuk filter
    $('#filter-status, #filter-date-from, #filter-date-to').on('change', function() {
        table.ajax.reload();
    });

    // Refresh otomatis setiap 30 detik (untuk update progress)
    setInterval(function() {
        table.ajax.reload(null, false); // false = tidak reset pagination
    }, 30000);
});
</script>
@endpush
