@extends('layouts.app-flowbite')

@section('title', 'Stok Opname')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite')
@endsection

@section('breadcrumb_items')
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <span class="text-sm font-bold text-zinc-900 dark:text-gray-400">Stok Opname</span>
        </div>
    </li>
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        {{-- Total Opname --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-500 to-slate-700 rounded-2xl p-6 text-white shadow-lg shadow-slate-200 transform transition-all hover:scale-[1.02]">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-clipboard-data text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-100 text-sm font-medium mb-1">Total</p>
                    <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Draft --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-zinc-400 to-zinc-600 rounded-2xl p-6 text-white shadow-lg shadow-zinc-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="$('#status').val('draft').trigger('change');">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-file-earmark-text text-2xl"></i>
                </div>
                <div>
                    <p class="text-zinc-100 text-sm font-medium mb-1">Draft</p>
                    <p class="text-3xl font-bold">{{ $stats['draft'] }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Sedang Berjalan --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-6 text-white shadow-lg shadow-orange-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="$('#status').val('in_progress').trigger('change');">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-arrow-repeat text-2xl"></i>
                </div>
                <div>
                    <p class="text-amber-100 text-sm font-medium mb-1">Berjalan</p>
                    <p class="text-3xl font-bold">{{ $stats['in_progress'] }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Selesai --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-teal-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="$('#status').val('completed').trigger('change');">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-check-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-emerald-100 text-sm font-medium mb-1">Selesai</p>
                    <p class="text-3xl font-bold">{{ $stats['completed'] }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Card Header --}}
        <div class="p-6 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-clipboard-check text-blue-600"></i>
                        Daftar Stok Opname
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola dan pantau proses stok opname</p>
                </div>
                
                @can('create_stock_opname')
                <a href="{{ route('stock-opnames.create') }}"
                   class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Buat Stok Opname Baru
                </a>
                @endcan
            </div>

            {{-- Filter Component --}}
            @include('layouts.filter-card', [
                'action' => route('stock-opnames.index'),
                'title' => 'Filter Data',
                'icon' => 'bi bi-funnel',
                'quickFilters' => [],
                'filters' => [
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'select',
                        'options' => [
                            'draft' => 'Draft',
                            'in_progress' => 'Sedang Berjalan',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan'
                        ]
                    ],
                    [
                        'name' => 'date_from',
                        'label' => 'Dari Tanggal',
                        'type' => 'date',
                    ],
                    [
                        'name' => 'date_to',
                        'label' => 'Sampai Tanggal',
                        'type' => 'date',
                    ]
                ]
            ])
        </div>

        {{-- DataTable --}}
        <div class="p-6 overflow-x-auto">
            <table id="stock-opname-table" class="w-full text-sm text-left">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Reference</th>
                        <th>Tanggal Opname</th>
                        <th>Petugas (PIC)</th>
                        <th>Total Item</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Diisi oleh DataTables AJAX --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('page_styles')
<style>
    @include('includes.datatables-flowbite-css')
</style>
@endpush

@push('page_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
@include('includes.datatables-flowbite-js')
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    var table = $('#stock-opname-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('stock-opnames.datatable') }}",
            data: function(d) {
                d.status = $('#status').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false,
                className: 'text-center font-bold text-zinc-500'
            },
            { 
                data: 'reference', 
                name: 'reference',
                render: function(data, type, row) {
                    return '<span class="font-bold text-blue-600">' + data + '</span>';
                }
            },
            { 
                data: 'opname_date', 
                name: 'opname_date',
                render: function(data) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-zinc-100 text-zinc-700"><i class="bi bi-calendar3 me-1"></i>' + moment(data).format('DD MMM YYYY') + '</span>';
                }
            },
            { 
                data: 'pic_name', 
                name: 'pic.name',
                render: function(data) {
                    return '<span class="text-zinc-700 font-medium"><i class="bi bi-person me-1"></i>' + (data || '-') + '</span>';
                }
            },
            { 
                data: 'total_items', 
                name: 'total_items',
                className: 'text-center',
                render: function(data) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700"><i class="bi bi-box-seam me-1"></i>' + data + ' Item</span>';
                }
            },
            { 
                data: 'completion', 
                name: 'completion',
                orderable: false,
                render: function(data, type, row) {
                    var percentage = parseFloat(data);
                    var colorClass = percentage < 50 ? 'bg-red-500' : 
                                    percentage < 100 ? 'bg-amber-500' : 'bg-emerald-500';
                    
                    return `
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-zinc-600">${percentage}%</span>
                            </div>
                            <div class="progress-tailwind">
                                <div class="progress-tailwind-bar ${colorClass}" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'status_badge', 
                name: 'status',
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    const statusMap = {
                        'draft': '<span class="badge-draft"><i class="bi bi-file-earmark me-1"></i>Draft</span>',
                        'in_progress': '<span class="badge-in-progress"><i class="bi bi-arrow-repeat me-1"></i>Berjalan</span>',
                        'completed': '<span class="badge-completed"><i class="bi bi-check-circle me-1"></i>Selesai</span>',
                        'cancelled': '<span class="badge-cancelled"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>'
                    };
                    return statusMap[row.status] || data;
                }
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[2, 'desc']]
    });

    // Event listener untuk filter (Bind to new IDs from filter-card)
    $('#btn-filter-apply').on('click', function() {
        table.ajax.reload();
    });

    $('#btn-filter-reset').on('click', function() {
        // filter-card handles value clearing, wait for it
        setTimeout(() => {
            table.ajax.reload();
        }, 100);
    });

    // Also refresh on change for select
    $('#status').on('change', function() {
        table.ajax.reload();
    });

    // Refresh otomatis setiap 30 detik (untuk update progress)
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});
</script>
@endpush
