@extends('layouts.app-flowbite')

@section('title', 'Semua Penjualan')

@section('content')
    {{-- Breadcrumb --}}
@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penjualan', 'url' => route('sales.index')],
            ['text' => 'Daftar Penjualan', 'url' => '#', 'icon' => 'bi bi-cart-check'],
        ],
    ])
@endsection

{{-- Stats Cards --}}
<div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
    {{-- Total Penjualan --}}
    <div
        class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden group">
        <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-blue-400 to-indigo-600"></div>
        <div
            class="p-3 mr-4 text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/30">
            <i class="bi bi-cash-stack text-2xl"></i>
        </div>
        <div>
            <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Penjualan</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-200" id="sum-total">
                Rp 0
            </p>
        </div>
    </div>

    {{-- Total Profit --}}
    <div
        class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden group">
        <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-green-400 to-emerald-600"></div>
        <div
            class="p-3 mr-4 text-white bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg shadow-green-500/30">
            <i class="bi bi-graph-up-arrow text-2xl"></i>
        </div>
        <div>
            <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Profit</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-200" id="sum-profit">
                Rp 0
            </p>
        </div>
    </div>

    {{-- Total Transaksi --}}
    <div
        class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 transition hover:shadow-md relative overflow-hidden group">
        <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-cyan-400 to-blue-500"></div>
        <div
            class="p-3 mr-4 text-white bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl shadow-lg shadow-cyan-500/30">
            <i class="bi bi-cart3 text-2xl"></i>
        </div>
        <div>
            <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Transaksi</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-200" id="sum-count">
                0
            </p>
        </div>
    </div>
</div>

@php
    $months = [];
    $now = \Carbon\Carbon::now()->startOfMonth();
    for ($i = 0; $i < 24; $i++) {
        $c = $now->copy()->subMonths($i);
        $months[$c->format('Y-m')] = $c->locale('id')->translatedFormat('F Y');
    }
@endphp



{{-- Main Card --}}
<div
    class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="px-6 pt-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                <i class="bi bi-receipt-cutoff mr-2 text-blue-600"></i>
                Daftar Transaksi Penjualan
            </h3>
            <p class="text-xs text-gray-500 mt-1">Kelola data transaksi penjualan harian</p>
        </div>
        <a href="{{ route('sales.create') }}"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-blue-500/30 hover:scale-[1.02] transition-transform duration-200">
            <i class="bi bi-plus-lg mr-2"></i> Tambah Penjualan
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="px-6 pt-6">
        @include('layouts.filter-card', [
            'action' => route('sales.index'),
            'title' => 'Filter Data Penjualan',
            'icon' => 'bi bi-funnel',
            'quickFilters' => [
                [
                    'label' => 'Hari Ini',
                    'url' => request()->fullUrlWithQuery(['preset' => 'today']),
                    'param' => 'preset',
                    'value' => 'today',
                    'icon' => 'bi bi-clock',
                ],
                [
                    'label' => 'Minggu Ini',
                    'url' => request()->fullUrlWithQuery(['preset' => 'this_week']),
                    'param' => 'preset',
                    'value' => 'this_week',
                    'icon' => 'bi bi-calendar-week',
                ],
                [
                    'label' => 'Bulan Ini',
                    'url' => request()->fullUrlWithQuery(['preset' => 'this_month']),
                    'param' => 'preset',
                    'value' => 'this_month',
                    'icon' => 'bi bi-calendar-month',
                ],
            ],
            'filters' => [
                [
                    'name' => 'month',
                    'label' => 'Pilih Bulan',
                    'type' => 'select',
                    'options' => $months,
                    'placeholder' => 'Pilih Bulan',
                ],
                ['name' => 'from', 'label' => 'Dari Tanggal', 'type' => 'date', 'value' => request('from')],
                ['name' => 'to', 'label' => 'Sampai Tanggal', 'type' => 'date', 'value' => request('to')],
                [
                    'name' => 'has_adjustment',
                    'label' => 'Ada Diskon',
                    'type' => 'select',
                    'options' => ['1' => 'Ya, Ada Diskon'],
                    'placeholder' => 'Semua',
                ],
                [
                    'name' => 'has_manual',
                    'label' => 'Input Manual',
                    'type' => 'select',
                    'options' => ['1' => 'Ya, Input Manual'],
                    'placeholder' => 'Semua',
                ],
            ],
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
    $(function() {
        const SUMMARY_URL = "{{ route('sales.summary') }}";

        function formatRupiah(n) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(Number(n || 0));
        }

        // Pastikan instance datatable sudah ada
        const table = window.LaravelDataTables['sales-table'];

        function getFilterValues() {
            return {
                preset: "{{ request('preset') }}",
                month: $('select[name="month"]').val(), // FIX: select
                from: $('input[name="from"]').val(),
                to: $('input[name="to"]').val(),
                has_adjustment: $('select[name="has_adjustment"]').val(),
                has_manual: $('select[name="has_manual"]').val(),
            };
        }

        // Inject filter ke request DataTables
        table.on('preXhr.dt', function(e, settings, data) {
            Object.assign(data, getFilterValues());
        });

        // Update summary saat draw
        table.on('draw', function() {
            const params = table.ajax.params();

            const summaryParams = {
                preset: params.preset,
                month: params.month,
                from: params.from,
                to: params.to,
                has_adjustment: params.has_adjustment,
                has_manual: params.has_manual,
            };

            $.get(SUMMARY_URL, {
                    filter: summaryParams
                })
                .done(function(d) {
                    $('#sum-total').text(formatRupiah(d.total_penjualan || 0));
                    $('#sum-profit').text(formatRupiah(d.total_profit || 0));
                    $('#sum-count').text(d.total_transaksi || 0);
                })
                .fail(function() {
                    console.error('Failed to fetch summary');
                });

            // Re-style row detail buttons
            $('button.btn-expand')
                .addClass(
                    'text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 focus:outline-none transition-colors shadow-sm'
                    )
                .removeClass('btn btn-sm btn-primary');
        });

        // Row detail expand
        $('#sales-table tbody').on('click', '.btn-expand', function() {
            const tr = $(this).closest('tr');
            const row = table.row(tr);
            const url = $(this).data('url');
            const btn = $(this);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                btn.html('<i class="bi bi-chevron-down"></i>');
            } else {
                btn.prop('disabled', true).html('<i class="bi bi-hourglass-split animate-spin"></i>');

                $.get(url, function(html) {
                    row.child(html).show();
                    tr.addClass('shown');
                    btn.html('<i class="bi bi-chevron-up"></i>');
                }).always(function() {
                    btn.prop('disabled', false);
                });
            }
        });
    });
</script>
@endpush
