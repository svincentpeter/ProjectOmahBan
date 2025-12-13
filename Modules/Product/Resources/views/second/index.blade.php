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

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Total Produk --}}
        <div
            class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-purple-200 transform transition-all hover:scale-[1.02]">
            <div class="flex items-center gap-4 relative z-10">
                <div
                    class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-recycle text-2xl"></i>
                </div>
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Total Produk</p>
                    <p class="text-3xl font-bold">{{ \Modules\Product\Entities\ProductSecond::count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Tersedia --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-teal-200 transform transition-all hover:scale-[1.02] cursor-pointer"
            onclick="window.location.href = @json(route('products_second.index', ['status' => 'available']))">
            <div class="flex items-center gap-4 relative z-10">
                <div
                    class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-check-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-emerald-100 text-sm font-medium mb-1">Tersedia</p>
                    <p class="text-3xl font-bold">
                        {{ \Modules\Product\Entities\ProductSecond::where('status', 'available')->count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Terjual --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-6 text-white shadow-lg shadow-rose-200 transform transition-all hover:scale-[1.02] cursor-pointer"
            onclick="window.location.href = @json(route('products_second.index', ['status' => 'sold']))">
            <div class="flex items-center gap-4 relative z-10">
                <div
                    class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-x-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-rose-100 text-sm font-medium mb-1">Terjual</p>
                    <p class="text-3xl font-bold">
                        {{ \Modules\Product\Entities\ProductSecond::where('status', 'sold')->count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>
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
