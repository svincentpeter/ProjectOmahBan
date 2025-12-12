@extends('layouts.app-flowbite')

@section('title', 'Tambah Produk Bekas')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Produk Bekas', 'url' => route('products_second.index')],
        ['text' => 'Tambah Produk', 'url' => route('products_second.create'), 'icon' => 'bi bi-plus-lg']
    ]])
@endsection

@section('content')
    <div class="max-w-7xl mx-auto">
        {{-- Alerts --}}
        @include('utils.alerts')

        <form id="product-form" action="{{ route('products_second.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Sticky Action Bar --}}
            <div class="sticky top-[72px] z-30 mb-6 p-4 bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h5 class="flex items-center gap-2 text-lg font-bold text-slate-800">
                        <i class="bi bi-plus-circle text-blue-600"></i>
                        Tambah Produk Bekas
                    </h5>
                    <p class="text-sm text-slate-500 mt-0.5">Produk ban/velg bekas (second-hand)</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('products_second.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm hover:shadow transition-all">
                        <i class="bi bi-check-lg me-1"></i> Simpan Produk
                    </button>
                </div>
            </div>

            {{-- Include Modern Form (create mode: tanpa $product) --}}
            @include('product::second.partials._form')
        </form>
    </div>
@endsection

@push('page_scripts')
    @include('product::second.partials._scripts')
@endpush
