@extends('layouts.app-flowbite')

@section('title', 'Tambah Kategori Pengeluaran')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Beranda', 'url' => route('home')],
            ['text' => 'Kategori Pengeluaran', 'url' => route('expense-categories.index')],
            ['text' => 'Tambah', 'url' => '#'],
        ]
    ])
@endsection

@section('content')
    <div class="mx-auto max-w-4xl">
        {{-- Alerts --}}
        @include('utils.alerts')

        <div class="bg-white border border-zinc-200 shadow-sm rounded-2xl overflow-hidden">
            {{-- Header --}}
            <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
                <h2 class="text-lg font-bold text-zinc-800 flex items-center gap-2">
                    <i class="bi bi-plus-circle text-blue-600"></i>
                    Tambah Kategori Baru
                </h2>
                <p class="text-sm text-zinc-500 mt-1">Buat kategori baru untuk pengelompokan pengeluaran.</p>
            </div>

            <form action="{{ route('expense-categories.store') }}" method="POST" autocomplete="off">
                @csrf
                
                <div class="p-6">
                    @include('expense::categories._form')
                </div>

                <div class="p-6 border-t border-zinc-100 bg-zinc-50 flex justify-end gap-3">
                    <a href="{{ route('expense-categories.index') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 focus:ring-4 focus:ring-zinc-100 transition-all">
                        <i class="bi bi-arrow-left mr-2"></i> Kembali
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm">
                        <i class="bi bi-save mr-2"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
