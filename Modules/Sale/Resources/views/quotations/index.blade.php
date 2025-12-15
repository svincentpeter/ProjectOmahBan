@extends('layouts.app-flowbite')

@section('title', 'Semua Penawaran')

@section('content')
@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penjualan', 'url' => route('sales.index')],
            ['text' => 'Daftar Penawaran', 'url' => '#', 'icon' => 'bi bi-file-earmark-text'],
        ],
    ])
@endsection

{{-- Main Card --}}
<div
    class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="px-6 pt-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                <i class="bi bi-file-earmark-text mr-2 text-blue-600"></i>
                Daftar Penawaran
            </h3>
            <p class="text-xs text-gray-500 mt-1">Kelola data penawaran harga</p>
        </div>
        <a href="{{ route('quotations.create') }}"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-blue-500/30 hover:scale-[1.02] transition-transform duration-200">
            <i class="bi bi-plus-lg mr-2"></i> Tambah Penawaran
        </a>
    </div>

    <div class="px-6 pb-6 pt-6">
        {{ $dataTable->table() }}
    </div>
</div>
@endsection

@push('page_styles')
@include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
@include('includes.datatables-flowbite-js')
{{ $dataTable->scripts() }}
@endpush
