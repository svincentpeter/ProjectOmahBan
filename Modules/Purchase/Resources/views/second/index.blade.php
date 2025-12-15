@extends('layouts.app-flowbite')

@section('title', 'Daftar Pembelian Stok Bekas')

@section('content')
    {{-- Breadcrumb --}}
    @section('breadcrumb')
        @include('layouts.breadcrumb-flowbite', [
            'items' => [
                ['text' => 'Pembelian Bekas', 'url' => route('purchases.second.index')],
                ['text' => 'Daftar Pembelian', 'url' => '#', 'icon' => 'bi bi-recycle'],
            ]
        ])
    @endsection
{{-- Stats Cards (Line Color, angka rapi untuk nominal) --}}
@php
    $totalPurchases = (int) ($summary['total_purchases'] ?? 0);
    $totalAmount    = (int) ($summary['total_amount'] ?? 0);
    $totalPaid      = (int) ($summary['total_paid'] ?? 0);
    $totalDue       = (int) ($summary['total_due'] ?? 0);

    $fmt = fn ($n) => number_format((int) $n, 0, ',', '.');
@endphp

<div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">

    {{-- Total Pembelian --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-purple-600 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-gray-400">Total Pembelian</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-gray-100 leading-tight tabular-nums tracking-tight">
                    {{ $totalPurchases }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-500 mt-1">Total transaksi pembelian</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-purple-50 text-purple-700 ring-1 ring-purple-100
                        dark:bg-purple-900/40 dark:text-purple-200 dark:ring-purple-900/60">
                <i class="bi bi-recycle text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Total Nilai --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-blue-600 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-gray-400">Total Nilai</p>

                <div class="flex items-baseline gap-2 whitespace-nowrap">
                    <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">Rp</span>
                    <span class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-gray-100 leading-tight tabular-nums tracking-tight">
                        {{ $fmt($totalAmount) }}
                    </span>
                </div>

                <p class="text-xs text-slate-500 dark:text-gray-500 mt-1">Nilai pembelian</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-blue-50 text-blue-700 ring-1 ring-blue-100
                        dark:bg-blue-900/40 dark:text-blue-200 dark:ring-blue-900/60">
                <i class="bi bi-credit-card text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Terbayar --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-emerald-600 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-gray-400">Terbayar</p>

                <div class="flex items-baseline gap-2 whitespace-nowrap">
                    <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">Rp</span>
                    <span class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-gray-100 leading-tight tabular-nums tracking-tight">
                        {{ $fmt($totalPaid) }}
                    </span>
                </div>

                <p class="text-xs text-slate-500 dark:text-gray-500 mt-1">Sudah dibayarkan</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100
                        dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-900/60">
                <i class="bi bi-check-circle text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Sisa Hutang --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
                border-l-4 border-l-rose-600 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-gray-400">Sisa Hutang</p>

                <div class="flex items-baseline gap-2 whitespace-nowrap">
                    <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">Rp</span>
                    <span class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-gray-100 leading-tight tabular-nums tracking-tight">
                        {{ $fmt($totalDue) }}
                    </span>
                </div>

                <p class="text-xs text-slate-500 dark:text-gray-500 mt-1">Belum dilunasi</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-rose-50 text-rose-700 ring-1 ring-rose-100
                        dark:bg-rose-900/40 dark:text-rose-200 dark:ring-rose-900/60">
                <i class="bi bi-exclamation-triangle text-xl"></i>
            </div>
        </div>
    </div>

</div>



    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 pt-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="bi bi-recycle mr-2 text-purple-600"></i>
                    Daftar Pembelian Produk Bekas
                </h3>
                <p class="text-xs text-gray-500 mt-1">Kelola pembelian ban & velg bekas dari customer</p>
            </div>
            @can('create_purchases')
                <a href="{{ route('purchases.second.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-purple-500/30 hover:scale-[1.02] transition-transform duration-200">
                    <i class="bi bi-plus-lg mr-2"></i> Tambah Pembelian
                </a>
            @endcan
        </div>

        {{-- Filter Card --}}
        <div class="px-6 pt-6">
            @include('layouts.filter-card', [
                'action' => route('purchases.second.index'),
                'title' => 'Filter Data Pembelian Bekas',
                'icon' => 'bi bi-funnel',
                'quickFilters' => [
                    ['label' => 'Semua', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'all']), 'param' => 'quick_filter', 'value' => 'all', 'icon' => 'bi bi-grid'],
                    ['label' => 'Hari Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'today']), 'param' => 'quick_filter', 'value' => 'today', 'icon' => 'bi bi-clock'],
                    ['label' => 'Kemarin', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'yesterday']), 'param' => 'quick_filter', 'value' => 'yesterday', 'icon' => 'bi bi-clock-history'],
                    ['label' => 'Minggu Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'this_week']), 'param' => 'quick_filter', 'value' => 'this_week', 'icon' => 'bi bi-calendar-week'],
                    ['label' => 'Bulan Ini', 'url' => request()->fullUrlWithQuery(['quick_filter' => 'this_month']), 'param' => 'quick_filter', 'value' => 'this_month', 'icon' => 'bi bi-calendar-month'],
                ],
                'filters' => [
                    ['name' => 'from', 'label' => 'Dari Tanggal', 'type' => 'date', 'value' => $from ?? null],
                    ['name' => 'to', 'label' => 'Sampai Tanggal', 'type' => 'date', 'value' => $to ?? null],
                    ['name' => 'customer', 'label' => 'Customer', 'type' => 'text', 'value' => request('customer'), 'placeholder' => 'Cari Customer...'],
                    ['name' => 'payment_status', 'label' => 'Status Bayar', 'type' => 'select', 'options' => ['Lunas' => 'Lunas', 'Belum Lunas' => 'Belum Lunas'], 'placeholder' => 'Semua Status'],
                ]
            ])
        </div>
        
        <div class="px-6 pb-6">
            {{ $dataTable->table() }}
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
            // Inject Filters into DataTable AJAX
            // Use jQuery selector to avoid race conditions
            $('#purchases-second-table').on('preXhr.dt', function ( e, settings, data ) {
                data.quick_filter = '{{ request('quick_filter') }}';
                data.from = $('input[name="from"]').val();
                data.to = $('input[name="to"]').val();
                data.customer = $('input[name="customer"]').val();
                data.payment_status = $('select[name="payment_status"]').val();
            });

            // Trigger redraw on filter change
            $('input[name="from"], input[name="to"], input[name="customer"], select[name="payment_status"]').on('change', function() {
                $('#purchases-second-table').DataTable().draw();
            });
        });
    </script>
@endpush
