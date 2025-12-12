@extends('layouts.app-flowbite')

@section('title', 'Edit Produk Bekas')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Produk Bekas', 'url' => route('products_second.index')],
        ['text' => 'Edit Produk', 'url' => route('products_second.edit', $product->id), 'icon' => 'bi bi-pencil']
    ]])
@endsection

@section('content')
    <div class="max-w-7xl mx-auto">
        {{-- Alerts --}}
        @include('utils.alerts')

        <form id="product-form" action="{{ route('products_second.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Sticky Action Bar --}}
            <div class="sticky top-[72px] z-30 mb-6 p-4 bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h5 class="flex items-center gap-2 text-lg font-bold text-slate-800">
                        <i class="bi bi-pencil-square text-blue-600"></i>
                        Edit Produk Bekas
                    </h5>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $product->name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('products_second.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm hover:shadow transition-all">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>

            {{-- Include Modern Form (edit mode: dengan $product) --}}
            @include('product::second.partials._form', ['product' => $product])
        </form>
    </div>
@endsection

@push('page_scripts')
    @include('product::second.partials._scripts')
@endpush
