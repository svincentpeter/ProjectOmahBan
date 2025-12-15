@extends('layouts.app-flowbite')

@section('title', 'Laporan Pembelian')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => 'Laporan Pembelian', 'url' => '#', 'icon' => 'bi bi-cart-plus'],
        ]
    ])
@endsection

@section('content')
    {{-- Main Control Card --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
             <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-cart-plus text-orange-600"></i>
                    Laporan Pembelian
                </h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                    <span class="flex items-center">
                        <i class="bi bi-calendar-range mr-1.5"></i>
                        Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    <i class="bi bi-printer mr-2"></i> Cetak
                </button>
            </div>
        </div>

        <form action="{{ url()->current() }}" method="GET" class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                     <label for="from" class="block mb-2 text-sm font-medium text-gray-900">Dari Tanggal</label>
                     <input type="date" id="from" name="from" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" value="{{ $from }}" required>
                </div>
                <div>
                    <label for="to" class="block mb-2 text-sm font-medium text-gray-900">Sampai Tanggal</label>
                    <input type="date" id="to" name="to" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" value="{{ $to }}" required>
                </div>
                <div>
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                    <select id="status" name="status" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="">Semua Status</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div>
                     <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5">
                        <i class="bi bi-filter mr-2"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <i class="bi bi-cart text-2xl"></i>
            </div>
            <p class="text-orange-100 text-sm font-medium mb-1">Total Pembelian</p>
            <h3 class="text-2xl font-bold">{{ format_currency($summary['total']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <i class="bi bi-check-circle text-2xl"></i>
            </div>
            <p class="text-green-100 text-sm font-medium mb-1">Terbayar</p>
            <h3 class="text-2xl font-bold">{{ format_currency($summary['paid']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <i class="bi bi-exclamation-circle text-2xl"></i>
            </div>
            <p class="text-red-100 text-sm font-medium mb-1">Belum Lunas</p>
            <h3 class="text-2xl font-bold">{{ format_currency($summary['due']) }}</h3>
        </div>
    </div>

    {{-- Purchases Table --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 pt-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="bi bi-table text-blue-600"></i>
                Daftar Pembelian
            </h3>
        </div>
        <div class="px-6 pb-6 overflow-x-auto">
            <table class="w-full text-sm text-left mt-4">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Dibayar</th>
                        <th class="px-4 py-3 text-right">Sisa</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($purchases as $purchase)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('purchases.show', $purchase) }}" class="text-blue-600 hover:underline">{{ $purchase->reference }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $purchase->supplier_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ format_currency($purchase->total_amount) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">{{ format_currency($purchase->paid_amount) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">{{ format_currency($purchase->due_amount) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($purchase->payment_status == 'Paid')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Lunas</span>
                            @elseif($purchase->payment_status == 'Partial')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Sebagian</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <i class="bi bi-inbox text-4xl mb-3 block text-gray-300"></i>
                            Tidak ada data pembelian pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
