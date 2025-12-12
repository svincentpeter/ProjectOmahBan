@extends('layouts.app-flowbite')

@section('title', 'Edit Kategori Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Kategori Produk', 'url' => route('product-categories.index'), 'icon' => 'bi bi-folder'],
        ['text' => 'Edit', 'url' => '#']
    ]])
@endsection

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-lg">
            @include('utils.alerts')
            
            <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-zinc-100 dark:border-gray-700">
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-pencil-square text-amber-500"></i>
                         Edit Kategori
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Perbarui informasi kategori produk</p>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('product-categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('patch')
                        
                        <div class="mb-5">
                            <label for="category_code" class="block mb-2 text-sm font-bold text-black dark:text-white">
                                Kode Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="category_code" 
                                   name="category_code" 
                                   value="{{ old('category_code', $category->category_code) }}"
                                   class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white font-medium" 
                                   required>
                        </div>
                        
                        <div class="mb-6">
                            <label for="category_name" class="block mb-2 text-sm font-bold text-black dark:text-white">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="category_name" 
                                   name="category_name" 
                                   value="{{ old('category_name', $category->category_name) }}"
                                   class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white font-medium" 
                                   required>
                        </div>
                        
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-gray-700">
                            <a href="{{ route('product-categories.index') }}" 
                               class="px-5 py-2.5 text-sm font-semibold text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 focus:ring-4 focus:outline-none focus:ring-zinc-200 transition-all">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" 
                                    class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

