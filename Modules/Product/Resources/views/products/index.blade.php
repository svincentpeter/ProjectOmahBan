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

   {{-- Statistics Cards (Line Color, bukan full gradient) --}}
@php
    $totalProduk = \Modules\Product\Entities\Product::active()->count();
    $stokRendah  = \Modules\Product\Entities\Product::active()
        ->whereColumn('product_quantity', '<=', 'product_stock_alert')
        ->where('product_quantity', '>', 0)
        ->count();
    $totalKategori = \Modules\Product\Entities\Category::count();
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- Total Produk --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                border-l-4 border-l-blue-600">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                <i class="bi bi-box-seam text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Total Produk</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalProduk }}</p>
                <p class="text-xs text-slate-500 mt-1">Produk aktif terdaftar</p>
            </div>
        </div>
    </div>

    {{-- Stok Rendah (clickable) --}}
    <button type="button"
        onclick="window.location.href='{{ route("products.index", ["quick_filter" => "low-stock"]) }}'"
        class="text-left group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
               border-l-4 border-l-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-amber-50 text-amber-700 ring-1 ring-amber-100">
                <i class="bi bi-exclamation-triangle text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Stok Rendah</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $stokRendah }}</p>
                <p class="text-xs text-slate-500 mt-1">Butuh restock segera</p>
            </div>
        </div>
    </button>

    {{-- Total Kategori --}}
    <div class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                border-l-4 border-l-emerald-600">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                <i class="bi bi-folder text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-600">Total Kategori</p>
                <p class="text-3xl font-extrabold text-slate-900 leading-tight">{{ $totalKategori }}</p>
                <p class="text-xs text-slate-500 mt-1">Kategori tersedia</p>
            </div>
        </div>
    </div>

</div>


    {{-- Main Card --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Card Header --}}
        <div class="px-6 pt-6">
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
        </div>

        {{-- Filter Card --}}
        <div class="px-6 pt-6">
            @include('layouts.filter-card', [
                'action' => route('products.index'),
                'title' => 'Filter Produk',
                'filters' => [
                    ['name' => 'brand_id', 'label' => 'Merek', 'type' => 'select', 'options' => $brands, 'placeholder' => 'Semua Merek', 'icon' => 'bi bi-tag'],
                    ['name' => 'quick_filter', 'label' => 'Status Stok', 'type' => 'select', 'options' => [
                        'low-stock' => 'Stok Menipis',
                        'out-of-stock' => 'Habis'
                    ], 'placeholder' => 'Semua Status', 'icon' => 'bi bi-box-seam']
                ]
            ])
        </div>

        {{-- Table Wrapper --}}
        <div class="px-6 pb-6 overflow-x-auto">
            {!! $dataTable->table(['class' => 'w-full text-sm text-left text-gray-900 dark:text-gray-400', 'id' => 'products-table']) !!}
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
        // Use jQuery selector to avoid race condition with window.LaravelDataTables
        $('#products-table').on('preXhr.dt', function ( e, settings, data ) {
            data.brand_id = $('select[name="brand_id"]').val();
            data.quick_filter = $('select[name="quick_filter"]').val();
        });
    });

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
