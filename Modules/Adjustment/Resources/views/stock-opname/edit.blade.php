@extends('layouts.app-flowbite')

@section('title', 'Edit Stock Opname - ' . $stockOpname->reference)

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite')
@endsection

@section('breadcrumb_items')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <a href="{{ route('stock-opnames.index') }}" class="text-sm font-medium text-zinc-500 hover:text-blue-600">Stock Opname</a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <a href="{{ route('stock-opnames.show', $stockOpname->id) }}" class="text-sm font-medium text-zinc-500 hover:text-blue-600">{{ $stockOpname->reference }}</a>
        </div>
    </li>
    <li aria-current="page">
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-zinc-400 mx-2 text-xs"></i>
            <span class="text-sm font-bold text-zinc-900">Edit</span>
        </div>
    </li>
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <div class="max-w-5xl mx-auto">
        {{-- HEADER CARD --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 mb-6">
            <div class="p-6 bg-gradient-to-r from-amber-500 to-orange-500 rounded-t-2xl">
                <h4 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="bi bi-pencil-square"></i> Edit Stock Opname
                </h4>
                <p class="text-amber-100 text-sm mt-1 flex items-center gap-2">
                    {{ $stockOpname->reference }} 
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white">
                        {{ ucfirst(str_replace('_', ' ', $stockOpname->status)) }}
                    </span>
                </p>
            </div>

            <div class="p-6">
                {{-- WARNING ALERT --}}
                <div class="bg-amber-50 border-l-4 border-amber-500 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="text-amber-600">
                            <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-amber-800 mb-1">Perhatian!</h5>
                            <p class="text-sm text-amber-700">
                                Opname ini berstatus <strong>DRAFT</strong>. 
                                Anda masih bisa mengubah tanggal, scope produk, atau catatan. 
                                Namun, <strong>jika Anda mengubah produk yang dipilih, semua item akan direset ulang</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- INCLUDE PARTIAL FORM --}}
                @include('adjustment::stock-opname.partials._form', [
                    'isEdit' => true,
                    'stockOpname' => $stockOpname,
                    'categories' => $categories
                ])
            </div>
        </div>
    </div>
@endsection
