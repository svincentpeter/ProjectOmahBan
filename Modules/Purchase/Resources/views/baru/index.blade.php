@extends('layouts.app-flowbite')

@section('title', 'Daftar Pembelian Stok')

@push('page_styles')
    @include('includes.datatables-flowbite-css')
@endpush

@section('content')
    {{-- Breadcrumb --}}
    @section('breadcrumb')
        @include('layouts.breadcrumb-flowbite', [
            'items' => [
                ['text' => 'Pembelian Stok', 'url' => route('purchases.index')],
                ['text' => 'Daftar Pembelian', 'url' => '#', 'icon' => 'bi bi-box-seam'],
            ]
        ])
    @endsection

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total Pembelian --}}
        <div class="relative overflow-hidden p-5 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            <div class="relative z-10 flex items-center">
                <div class="p-3 mr-4 bg-white/20 backdrop-blur-sm rounded-xl">
                    <i class="bi bi-box-seam text-3xl text-white"></i>
                </div>
                <div>
                    <p class="mb-1 text-sm font-medium text-purple-100">Total Pembelian</p>
                    <p class="text-2xl font-bold text-white">
                        {{ $total_purchases }}
                    </p>
                </div>
            </div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Total Nilai --}}
        <div class="relative overflow-hidden p-5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            <div class="relative z-10 flex items-center">
                <div class="p-3 mr-4 bg-white/20 backdrop-blur-sm rounded-xl">
                    <i class="bi bi-credit-card text-3xl text-white"></i>
                </div>
                <div>
                    <p class="mb-1 text-sm font-medium text-blue-100">Total Nilai</p>
                    <p class="text-2xl font-bold text-white">
                        {{ rupiah($total_amount) }}
                    </p>
                </div>
            </div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Terbayar --}}
        <div class="relative overflow-hidden p-5 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            <div class="relative z-10 flex items-center">
                <div class="p-3 mr-4 bg-white/20 backdrop-blur-sm rounded-xl">
                    <i class="bi bi-check-circle text-3xl text-white"></i>
                </div>
                <div>
                    <p class="mb-1 text-sm font-medium text-emerald-100">Terbayar</p>
                    <p class="text-2xl font-bold text-white">
                        {{ rupiah($total_paid) }}
                    </p>
                </div>
            </div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Sisa Hutang --}}
        <div class="relative overflow-hidden p-5 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            <div class="relative z-10 flex items-center">
                <div class="p-3 mr-4 bg-white/20 backdrop-blur-sm rounded-xl">
                    <i class="bi bi-exclamation-triangle text-3xl text-white"></i>
                </div>
                <div>
                    <p class="mb-1 text-sm font-medium text-orange-100">Sisa Hutang</p>
                    <p class="text-2xl font-bold text-white">
                        {{ rupiah($total_due) }}
                    </p>
                </div>
            </div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        </div>
    </div>



    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 pt-6 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="bi bi-box-seam mr-2 text-purple-600"></i>
                    Daftar Pembelian
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data pembelian stok baru</p>
            </div>
            @can('create_purchases')
                <a href="{{ route('purchases.create') }}"
                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-200">
                    <i class="bi bi-plus-lg mr-2"></i> Tambah Pembelian
                </a>
            @endcan
        </div>

        {{-- Filter Card --}}
        <div class="px-6 pt-6">
            @include('layouts.filter-card', [
                'action' => route('purchases.index'),
                'title' => 'Filter Data Pembelian',
                'icon' => 'bi bi-funnel',
                'quickFilters' => [
                    ['label' => 'Semua', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'all']), 'param' => 'quick_filter', 'value' => 'all', 'icon' => 'bi bi-grid'],
                    ['label' => 'Hari Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'today']), 'param' => 'quick_filter', 'value' => 'today', 'icon' => 'bi bi-clock'],
                    ['label' => 'Kemarin', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'yesterday']), 'param' => 'quick_filter', 'value' => 'yesterday', 'icon' => 'bi bi-clock-history'],
                    ['label' => 'Minggu Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'this_week']), 'param' => 'quick_filter', 'value' => 'this_week', 'icon' => 'bi bi-calendar-week'],
                    ['label' => 'Bulan Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'this_month']), 'param' => 'quick_filter', 'value' => 'this_month', 'icon' => 'bi bi-calendar-month'],
                ],
                'filters' => [
                    ['name' => 'from', 'label' => 'Dari Tanggal', 'type' => 'date', 'value' => $from],
                    ['name' => 'to', 'label' => 'Sampai Tanggal', 'type' => 'date', 'value' => $to],
                    ['name' => 'supplier_id', 'label' => 'Supplier', 'type' => 'select', 'options' => $suppliers->pluck('supplier_name', 'id')->toArray(), 'placeholder' => 'Pilih Supplier'],
                    ['name' => 'payment_status', 'label' => 'Status Bayar', 'type' => 'select', 'options' => ['Lunas' => 'Lunas', 'Belum Lunas' => 'Belum Lunas'], 'placeholder' => 'Semua Status'],
                ]
            ])
        </div>

        {{-- Table Content --}}
        <div class="px-6 pb-6">
            {{ $dataTable->table() }}
        </div>
    </div>
@endsection

@push('page_scripts')
    @include('includes.datatables-flowbite-js')
    {{ $dataTable->scripts() }}
    
    <script>
        $(document).ready(function() {
            // Inject Filters into DataTable AJAX
            // Use jQuery selector to avoid race conditions
            $('#purchases-table').on('preXhr.dt', function ( e, settings, data ) {
                data.quick_filter = '{{ request('quick_filter') }}';
                data.from = $('input[name="from"]').val();
                data.to = $('input[name="to"]').val();
                data.supplier_id = $('select[name="supplier_id"]').val();
                data.payment_status = $('select[name="payment_status"]').val();
            });

            // Trigger redraw on filter change
            $('input[name="from"], input[name="to"], select[name="supplier_id"], select[name="payment_status"]').on('change', function() {
                $('#purchases-table').DataTable().draw();
            });
        });
    </script>
@endpush
