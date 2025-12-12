@extends('layouts.app-flowbite')

@section('title', 'Riwayat Pembayaran Pembelian')

@section('content')
    {{-- Breadcrumb --}}
    @section('breadcrumb')
        @include('layouts.breadcrumb-flowbite', [
            'items' => [
                ['text' => 'Pembelian', 'url' => route('purchases.index')],
                ['text' => 'Buku Pembelian', 'url' => '#', 'icon' => 'bi bi-box-seam'],
                ['text' => 'Pembayaran ' . $purchase->reference, 'url' => '#'],
            ]
        ])
    @endsection

    {{-- Info Card --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-blue-600">
        <div class="flex justify-between items-start">
            <div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                    Informasi Pembelian: <span class="text-blue-600">{{ $purchase->reference }}</span>
                </h4>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p><span class="font-semibold">Supplier:</span> {{ $purchase->supplier_name }}</p>
                    <p><span class="font-semibold">Tanggal:</span> {{ $purchase->date->format('d/m/Y') }}</p>
                    <p><span class="font-semibold">Total Tagihan:</span> {{ format_currency($purchase->total_amount) }}</p>
                    <p><span class="font-semibold">Status:</span> 
                        <span class="px-2 py-0.5 rounded text-xs font-semibold
                            {{ $purchase->payment_status == 'Paid' ? 'bg-green-100 text-green-800' : 
                               ($purchase->payment_status == 'Partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $purchase->payment_status }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Sisa Tagihan</p>
                <p class="text-2xl font-bold text-red-500">{{ format_currency($purchase->due_amount) }}</p>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-700/20">
            <div>
                <h5 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="bi bi-cash-stack text-green-600"></i>
                    Riwayat Pembayaran
                </h5>
                <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Daftar pembayaran untuk pembelian ini.</p>
            </div>

            <div class="flex items-center gap-2">
                @can('access_purchase_payments')
                    @if($purchase->due_amount > 0)
                        <a href="{{ route('purchase-payments.create', $purchase->id) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 shadow-lg shadow-blue-500/30 transition-all duration-200">
                            <i class="bi bi-plus-lg mr-2"></i>
                            Tambah Pembayaran
                        </a>
                    @endif
                @endcan
            </div>
        </div>

        {{-- DataTable --}}
        <div class="p-5">
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
