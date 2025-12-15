@extends('layouts.app-flowbite')

@section('title', 'Daftar Customer')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Customer',
        'items' => [
            ['text' => 'Home', 'url' => route('home')],
            ['text' => 'Customer', 'url' => '#']
        ]
    ])
@endsection

@section('content')
    {{-- Statistics Cards (Line Color, bukan fullfill) --}}
@php
    $totalCustomers = \Modules\People\Entities\Customer::count();

    try {
        $activeCustomers = \Modules\People\Entities\Customer::whereHas('sales', function($q) {
            $q->where('date', '>=', now()->subMonths(6));
        })->count();
    } catch (\Throwable $e) {
        \Log::error('Error counting active customers: ' . $e->getMessage());
        $activeCustomers = 0;
    }

    $totalCities = \Modules\People\Entities\Customer::distinct('city')->count('city');

    try {
        $totalSales = \Modules\Sale\Entities\Sale::count();
    } catch (\Throwable $e) {
        $totalSales = 0;
    }
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Customer --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-indigo-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 mb-1">Total Customer</p>
                <h3 class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalCustomers }}</h3>
                <p class="text-xs text-slate-500 mt-1">Seluruh customer terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100">
                <i class="bi bi-people text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Customer Aktif --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-emerald-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 mb-1">Customer Aktif</p>
                <h3 class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $activeCustomers }}</h3>
                <p class="text-xs text-emerald-600 mt-1 flex items-center">
                    <i class="bi bi-activity me-1"></i> In last 6 months
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                <i class="bi bi-person-check text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Total Kota --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-blue-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 mb-1">Total Kota</p>
                <h3 class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalCities }}</h3>
                <p class="text-xs text-slate-500 mt-1">Sebaran kota customer</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                <i class="bi bi-geo-alt text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Total Transaksi --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-amber-500">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 mb-1">Total Transaksi</p>
                <h3 class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalSales }}</h3>
                <p class="text-xs text-slate-500 mt-1">Jumlah transaksi penjualan</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-amber-50 text-amber-700 ring-1 ring-amber-100">
                <i class="bi bi-cart text-xl"></i>
            </div>
        </div>
    </div>

</div>


    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-zinc-200 dark:border-gray-700">
        {{-- Header & Toolbar --}}
        {{-- Header & Toolbar --}}
        <div class="px-6 pt-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h5 class="text-lg font-bold text-zinc-800 dark:text-white flex items-center">
                    <i class="bi bi-people-fill text-indigo-600 me-2"></i>
                    Daftar Customer
                </h5>
                <p class="text-sm text-zinc-500 dark:text-gray-400 mt-1">Kelola data customer untuk transaksi penjualan</p>
            </div>
            <div>
                @can('create_customers')
                <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 transition-all shadow-sm hover:shadow-md">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Customer
                </a>
                @endcan
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="px-6 pt-6">
            @include('layouts.filter-card', [
                'action' => route('customers.index'),
                'title' => 'Filter Data',
                'icon' => 'bi bi-funnel',
                'quickFilters' => [
                     [
                        'label' => 'Semua', 
                        'value' => '', 
                        'param' => 'status', 
                        'url' => request()->fullUrlWithQuery(['status' => '']),
                        'icon' => 'bi bi-grid'
                     ],
                     [
                        'label' => 'Aktif', 
                        'value' => 'active', 
                        'param' => 'status', 
                        'url' => request()->fullUrlWithQuery(['status' => 'active']),
                        'icon' => 'bi bi-check-circle'
                     ],
                     [
                        'label' => 'Tidak Aktif', 
                        'value' => 'inactive', 
                        'param' => 'status', 
                        'url' => request()->fullUrlWithQuery(['status' => 'inactive']),
                        'icon' => 'bi bi-x-circle'
                     ],
                ],
                'filters' => [
                    [
                        'name' => 'city',
                        'label' => 'Kota',
                        'type' => 'select',
                        'options' => \Modules\People\Entities\Customer::distinct('city')->pluck('city')->mapWithKeys(fn($item) => [$item => $item])->toArray()
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'select',
                        'options' => [
                            'active' => 'Aktif (Ada Order)',
                            'inactive' => 'Tidak Aktif'
                        ]
                    ]
                ]
            ])
        </div>

        {{-- DataTable --}}
        <div class="px-6 pb-6">
            <div class="overflow-x-auto">
                {{ $dataTable->table(['class' => 'w-full text-sm text-left text-zinc-500 dark:text-zinc-400', 'id' => 'customers-table'], true) }}
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
@include('includes.datatables-flowbite-css')
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f9fafb;
        }

        .dataTables_wrapper .dataTables_info {
            padding: 1rem 1.5rem;
            color: #71717a;
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('page_scripts')
    @include('includes.datatables-flowbite-js')
    {{ $dataTable->scripts() }}

    <script>
        $(document).ready(function() {
            // Use jQuery selector to avoid race condition with window.LaravelDataTables
            $('#customers-table').on('preXhr.dt', function ( e, settings, data ) {
                data.city = $('#city').val();
                data.status = $('#status').val();
            });
            
            // Handle Filter Changes
            $('#city, #status').on('change', function() {
                $('#customers-table').DataTable().draw();
            });

            // Delete confirmation
            $(document).on('click', '.delete-customer', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');
                const hasSales = $(this).data('has-sales') === true; // Note: data attr is parsed carefully
                const url = '{{ route('customers.destroy', ':id') }}'.replace(':id', id);

                let warningText = hasSales 
                    ? `Customer <strong class="text-zinc-800">"${name}"</strong> memiliki riwayat penjualan dan akan di-arsipkan (soft delete).<br><span class="text-xs text-zinc-500">Data masih bisa dikembalikan!</span>`
                    : `Customer <strong class="text-zinc-800">"${name}"</strong> akan dihapus permanen.<br><span class="text-xs text-red-500">Data tidak dapat dikembalikan!</span>`;

                Swal.fire({
                    title: 'Hapus Customer?',
                    html: warningText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#E5E7EB',
                    confirmButtonText: '<i class="bi bi-trash me-2"></i> Ya, Hapus!',
                    cancelButtonText: '<span class="text-zinc-700">Batal</span>',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'font-bold rounded-xl px-5 py-2.5',
                        cancelButton: 'font-medium rounded-xl px-5 py-2.5',
                        popup: 'rounded-2xl font-sans'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

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
