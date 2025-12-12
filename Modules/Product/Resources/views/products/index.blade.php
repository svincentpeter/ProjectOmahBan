@extends('layouts.app-flowbite')

@section('title', 'Daftar Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Daftar Produk', 'url' => route('products.index'), 'icon' => 'bi bi-box-seam-fill']
    ]])
@endsection



@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Total Produk --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-200 transform transition-all hover:scale-[1.02]">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-box-seam text-2xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Produk</p>
                    <p class="text-3xl font-bold">{{ \Modules\Product\Entities\Product::active()->count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Stok Rendah --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl p-6 text-white shadow-lg shadow-orange-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="window.location.href='{{ route('products.index', ['quick_filter' => 'low-stock']) }}'">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-exclamation-triangle text-2xl"></i>
                </div>
                <div>
                    <p class="text-amber-100 text-sm font-medium mb-1">Stok Rendah</p>
                    <p class="text-3xl font-bold">{{ \Modules\Product\Entities\Product::active()->whereColumn('product_quantity', '<=', 'product_stock_alert')->where('product_quantity', '>', 0)->count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Total Kategori --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-teal-200 transform transition-all hover:scale-[1.02]">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-folder text-2xl"></i>
                </div>
                <div>
                    <p class="text-emerald-100 text-sm font-medium mb-1">Total Kategori</p>
                    <p class="text-3xl font-bold">{{ \Modules\Product\Entities\Category::count() }}</p>
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
                        <i class="bi bi-box-seam text-blue-600"></i>
                        Daftar Produk
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola produk dan inventory toko Anda</p>
                </div>
                
                @can('create_products')
                <a href="{{ route('products.create') }}" 
                   class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Produk
                </a>
                @endcan
            </div>

            @php
                $categories = \Modules\Product\Entities\Category::orderBy('category_name')->pluck('category_name', 'id')->toArray();
            @endphp

            {{-- Global Filter Component --}}
            @include('layouts.filter-card', [
                'action' => route('products.index'),
                'title' => 'Filter Data',
                'icon' => 'bi bi-funnel',
                'quickFilters' => [
                    [
                        'label' => 'Semua Produk',
                        'url' => route('products.index', ['quick_filter' => 'all']),
                        'param' => 'quick_filter',
                        'value' => 'all',
                        'icon' => 'bi bi-grid'
                    ],
                    [
                        'label' => 'Stok Rendah',
                        'url' => route('products.index', ['quick_filter' => 'low-stock']),
                        'param' => 'quick_filter',
                        'value' => 'low-stock',
                        'icon' => 'bi bi-exclamation-triangle'
                    ],
                    [
                        'label' => 'Stok Habis',
                        'url' => route('products.index', ['quick_filter' => 'out-of-stock']),
                        'param' => 'quick_filter',
                        'value' => 'out-of-stock',
                        'icon' => 'bi bi-slash-circle'
                    ]
                ],
                'filters' => [
                    [
                        'name' => 'category_id',
                        'label' => 'Kategori',
                        'type' => 'select',
                        'icon' => 'bi bi-folder',
                        'options' => $categories
                    ]
                ]
            ])
        </div>

        {{-- Table Wrapper --}}
        <div class="p-6 overflow-x-auto">
            {!! $dataTable->table(['class' => 'w-full text-sm text-left text-slate-500 dark:text-gray-400', 'id' => 'products-table']) !!}
        </div>
    </div>
@endsection

@push('page_styles')
<style>
    @include('includes.datatables-flowbite-css')
</style>
@endpush

@push('page_scripts')
@include('includes.datatables-flowbite-js')
{!! $dataTable->scripts() !!}

<script>
$(document).ready(function() {
    // Delete confirmation
    $(document).on('click', '.delete-product', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Produk?',
            html: `Produk <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-zinc-500">Data yang dihapus tidak dapat dikembalikan!</small>`,
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: '<i class="bi bi-x me-1"></i> Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
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
