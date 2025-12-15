@extends('layouts.app-flowbite')

@section('title', 'Laporan Pembayaran')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => 'Laporan Pembayaran', 'url' => '#', 'icon' => 'bi bi-credit-card'],
        ]
    ])
@endsection

@section('content')
    {{-- Main Control Card --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
             <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-credit-card text-green-600"></i>
                    Laporan Pembayaran
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
                    <label for="method" class="block mb-2 text-sm font-medium text-gray-900">Metode</label>
                    <select id="method" name="method" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="">Semua Metode</option>
                        <option value="Tunai" {{ request('method') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="Transfer" {{ request('method') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="QRIS" {{ request('method') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
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
        <div class="bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <i class="bi bi-cash-stack text-2xl"></i>
            </div>
            <p class="text-green-100 text-sm font-medium mb-1">Total Pembayaran</p>
            <h3 class="text-2xl font-bold">{{ format_currency($summary['total']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <i class="bi bi-receipt text-2xl"></i>
            </div>
            <p class="text-blue-100 text-sm font-medium mb-1">Jumlah Transaksi</p>
            <h3 class="text-2xl font-bold">{{ number_format($summary['count']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <i class="bi bi-calculator text-2xl"></i>
            </div>
            <p class="text-purple-100 text-sm font-medium mb-1">Rata-rata per Transaksi</p>
            <h3 class="text-2xl font-bold">{{ format_currency($summary['count'] > 0 ? $summary['total'] / $summary['count'] : 0) }}</h3>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 pt-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="bi bi-table text-blue-600"></i>
                Daftar Pembayaran
            </h3>
        </div>
        <div class="px-6 pb-6 overflow-x-auto">
            <table class="w-full text-sm text-left mt-4">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Sale Ref</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($payment->date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $payment->reference }}</td>
                        <td class="px-4 py-3">
                            @if($payment->sale)
                                <a href="{{ route('sales.show', $payment->sale) }}" class="text-blue-600 hover:underline">{{ $payment->sale->reference }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $payment->payment_method }} {{ $payment->bank_name ? "({$payment->bank_name})" : '' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">{{ format_currency($payment->amount) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                            <i class="bi bi-inbox text-4xl mb-3 block text-gray-300"></i>
                            Tidak ada data pembayaran pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
