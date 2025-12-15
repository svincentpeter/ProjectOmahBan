@extends('layouts.app-flowbite')

@section('title', 'Daftar Supplier')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Supplier',
        'items' => [
            ['text' => 'Home', 'url' => route('home')],
            ['text' => 'Supplier', 'url' => '#']
        ]
    ])
@endsection

@section('content')
    {{-- Statistics Cards (Line Color, bukan fullfill) --}}
@php
    $totalSuppliers = \Modules\People\Entities\Supplier::count();

    $activeSuppliers = \Modules\People\Entities\Supplier::whereHas('purchases', function ($q) {
        $q->where('date', '>=', now()->subMonths(6));
    })->count();

    $totalCities = \Modules\People\Entities\Supplier::distinct('city')->count('city');

    $totalPurchases = \Modules\Purchase\Entities\Purchase::count();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Supplier --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-blue-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 mb-1">Total Supplier</p>
                <h3 class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalSuppliers }}</h3>
                <p class="text-xs text-slate-500 mt-1">Supplier terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                <i class="bi bi-people text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Supplier Aktif --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-emerald-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 mb-1">Supplier Aktif</p>
                <h3 class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $activeSuppliers }}</h3>
                <p class="text-xs text-slate-500 mt-1">Transaksi 6 bulan terakhir</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                <i class="bi bi-check-circle text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Jangkauan Kota --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-violet-600">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600 mb-1">Jangkauan Kota</p>
                <h3 class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalCities }}</h3>
                <p class="text-xs text-slate-500 mt-1">Sebaran kota supplier</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-violet-50 text-violet-700 ring-1 ring-violet-100">
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
                <h3 class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalPurchases }}</h3>
                <p class="text-xs text-slate-500 mt-1">Jumlah pembelian</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-amber-50 text-amber-700 ring-1 ring-amber-100">
                <i class="bi bi-cart-check text-xl"></i>
            </div>
        </div>
    </div>

</div>


    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">
            {{-- Card Header --}}
        <div class="px-6 pt-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h5 class="text-xl font-bold text-zinc-800 flex items-center gap-2">
                        <i class="bi bi-people-fill text-blue-600"></i>
                        Daftar Supplier
                    </h5>
                    <p class="text-sm text-zinc-500 mt-1">Kelola data mitra pemasok untuk inventaris toko</p>
                </div>
                <div>
                    @can('create_suppliers')
                        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm hover:shadow-md">
                            <i class="bi bi-plus-lg me-2"></i> Tambah Supplier
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="px-6 pt-6">
            {{-- Filter Section --}}
            @include('layouts.filter-card', [
                'action' => route('suppliers.index'),
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
                        'options' => collect(\Modules\People\Entities\Supplier::getUniqueCities())->mapWithKeys(fn($item) => [$item => $item])->toArray()
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'select',
                        'options' => [
                            'active' => 'Aktif (Ada Transaksi)',
                            'inactive' => 'Tidak Aktif'
                        ]
                    ]
                ]
            ])
        </div>

        {{-- DataTable --}}
        <div class="px-6 pb-6 overflow-x-auto">
            {{ $dataTable->table(['class' => 'w-full text-sm text-left text-zinc-500 dark:text-zinc-400', 'id' => 'suppliers-table'], true) }}
        </div>
    </div>
@endsection

@push('page_styles')
    @include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
    @include('includes.datatables-flowbite-js')
    {{ $dataTable->scripts() }}

    <script>
        $(document).ready(function() {

            // Use jQuery selector to avoid race condition with window.LaravelDataTables
            $('#suppliers-table').on('preXhr.dt', function ( e, settings, data ) {
                data.city = $('#city').val();
                data.status = $('#status').val();
            });
            
            // Handle Filter Changes
            $('#city, #status').on('change', function() {
                $('#suppliers-table').DataTable().draw();
            });

            // SweetAlert2 Delete Confirmation (Delegated Event)
            $(document).on('click', '.delete-supplier', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');
                const hasPurchases = $(this).data('has-purchases'); // boolean string 'true'/'false'
                const url = "{{ route('suppliers.destroy', ':id') }}".replace(':id', id);

                // Customize message based on purchase history
                let warningTitle = 'Hapus Supplier?';
                let warningText = `Anda akan menghapus data supplier <strong>"${name}"</strong>.`;
                let warningIcon = 'warning'; 

                if (hasPurchases === 'true') {
                    warningText += `<br><br><span class="text-orange-600 font-bold"><i class="bi bi-archive me-1"></i> Perhatian:</span> Supplier ini memiliki riwayat transaksi. Data akan diarsipkan (Soft Delete) demi integritas data.`;
                } else {
                    warningText += `<br><br><span class="text-red-600 font-bold"><i class="bi bi-exclamation-triangle me-1"></i> Peringatan:</span> Tindakan ini bersifat permanen dan tidak dapat dibatalkan!`;
                }

                Swal.fire({
                    title: warningTitle,
                    html: warningText,
                    icon: warningIcon,
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Create and submit form
                        const form = $('<form>', {
                            method: 'POST',
                            action: url
                        });
                        
                        form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                        form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));
                        
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
