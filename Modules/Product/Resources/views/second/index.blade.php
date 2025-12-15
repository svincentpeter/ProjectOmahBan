@extends('layouts.app-flowbite')

@section('title', 'Daftar Produk Bekas')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Manajemen Produk', 'url' => '#'],
            ['text' => 'Produk Bekas', 'url' => route('products_second.index'), 'icon' => 'bi bi-recycle'],
        ],
    ])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Statistics Cards (Line Color, bukan full gradient) --}}
@php
    $totalSecond   = \Modules\Product\Entities\ProductSecond::count();
    $availableSecond = \Modules\Product\Entities\ProductSecond::where('status', 'available')->count();
    $soldSecond      = \Modules\Product\Entities\ProductSecond::where('status', 'sold')->count();
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- Total Produk --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                border-l-4 border-l-purple-600">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-purple-50 text-purple-700 ring-1 ring-purple-100">
                <i class="bi bi-recycle text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Total Produk</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalSecond }}</p>
                <p class="text-xs text-slate-500 mt-1">Produk bekas terdaftar</p>
            </div>
        </div>
    </div>

    {{-- Tersedia (clickable) --}}
    <button type="button"
        onclick="window.location.href='{{ route('products_second.index', ['status' => 'available']) }}'"
        class="text-left group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
               border-l-4 border-l-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                <i class="bi bi-check-circle text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Tersedia</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $availableSecond }}</p>
                <p class="text-xs text-slate-500 mt-1">Siap dijual</p>
            </div>
        </div>
    </button>

    {{-- Terjual (clickable) --}}
    <button type="button"
        onclick="window.location.href='{{ route('products_second.index', ['status' => 'sold']) }}'"
        class="text-left group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
               border-l-4 border-l-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-rose-50 text-rose-700 ring-1 ring-rose-100">
                <i class="bi bi-x-circle text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Terjual</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $soldSecond }}</p>
                <p class="text-xs text-slate-500 mt-1">Sudah keluar stok</p>
            </div>
        </div>
    </button>

</div>


    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">

        {{-- Card Header --}}
        <div class="px-6 pt-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-recycle text-purple-600"></i>
                        Daftar Produk Bekas
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola produk ban & velg bekas</p>
                </div>

                <a href="{{ route('products_second.create') }}"
                    class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Produk Bekas
                </a>
            </div>

            {{-- Filter Card --}}
            <div class="px-6 pt-6">
                @include('layouts.filter-card', [
                    'action' => route('products_second.index'),
                    'title' => 'Filter Produk Bekas',
                    'filters' => [
                        [
                            'name' => 'brand_id',
                            'label' => 'Merek',
                            'type' => 'select',
                            'options' => $brands,
                            'placeholder' => 'Semua Merek',
                            'icon' => 'bi bi-tag',
                        ],
                        [
                            'name' => 'status',
                            'label' => 'Status',
                            'type' => 'select',
                            'options' => [
                                'available' => 'Tersedia',
                                'sold' => 'Terjual',
                            ],
                            'placeholder' => 'Semua Status',
                            'icon' => 'bi bi-check-circle',
                        ],
                    ],
                ])
            </div>
        </div>

        {{-- DataTable --}}
        <div class="px-6 pb-6 overflow-x-auto">
            {!! $dataTable->table(['class' => 'w-full text-sm text-left', 'id' => 'product-second-table']) !!}
        </div>
    </div>
@endsection

@push('page_styles')
    @include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
    @include('includes.datatables-flowbite-js')
    {!! $dataTable->scripts() !!}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#product-second-table').on('preXhr.dt', function(e, settings, data) {
                data.brand_id = $('select[name="brand_id"]').val() || '';
                data.status = $('select[name="status"]').val() || '';
            });
        });
    </script>
@endpush
