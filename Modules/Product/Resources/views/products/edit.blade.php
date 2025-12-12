{{-- Modules/Product/Resources/views/products/edit.blade.php --}}
@extends('layouts.app-flowbite')

@section('title', 'Edit Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Daftar Produk', 'url' => route('products.index')],
        ['text' => 'Edit Produk', 'url' => '#', 'icon' => 'bi bi-pencil-square']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <form id="product-form" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- Sticky Action Bar --}}
        <div class="sticky top-[72px] z-50 mb-6 p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 transition-transform duration-300">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h5 class="flex items-center text-lg font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-pencil-square me-2 text-blue-600"></i>
                         Edit Produk: {{ $product->product_name }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Perbarui informasi produk</p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('products.index') }}" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition-all font-medium text-sm">
                        <i class="bi bi-x me-1"></i> Batal
                    </a>
                    <button type="submit" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-all font-medium text-sm shadow-md hover:shadow-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Form (Shared with Create) --}}
        @include('product::products.partials._form')

    </form>
@endsection

{{-- Scripts --}}
@include('product::products.partials._scripts')
