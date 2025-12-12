{{-- Modules/Product/Resources/views/products/create.blade.php --}}
@extends('layouts.app-flowbite')

@section('title', 'Tambah Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Daftar Produk', 'url' => route('products.index')],
        ['text' => 'Tambah Produk', 'url' => '#', 'icon' => 'bi bi-plus-circle-fill']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Sticky Action Bar --}}
        <div class="sticky top-[72px] z-50 mb-6 p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 transition-transform duration-300">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h5 class="flex items-center text-lg font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-plus-circle me-2 text-blue-600"></i>
                         Tambah Produk Baru
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Lengkapi informasi produk di bawah ini</p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('products.index') }}" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition-all font-medium text-sm">
                        <i class="bi bi-x me-1"></i> Batal
                    </a>
                    <button type="submit" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-all font-medium text-sm shadow-md hover:shadow-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan Produk
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Form (Shared with Edit) --}}
        @include('product::products.partials._form')

    </form>
@endsection

{{-- Scripts (MaskMoney, validation, etc) --}}
@include('product::products.partials._scripts')
