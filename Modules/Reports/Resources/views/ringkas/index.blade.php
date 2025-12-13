@extends('layouts.app-flowbite')

@section('title', 'Laporan Ringkasan Harian')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => 'Ringkasan Harian', 'url' => '#', 'icon' => 'bi bi-journal-text'],
        ]
    ])
@endsection

@section('content')
    {{-- Main Control Card --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
             <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-journal-text text-blue-600"></i>
                    Laporan Ringkasan Harian
                </h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center">
                        <i class="bi bi-calendar-range mr-1.5"></i>
                        Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all shadow-sm">
                    <i class="bi bi-printer mr-2"></i> Cetak Laporan
                </button>
            </div>
        </div>

        <form action="{{ url()->current() }}" method="GET" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                         <label for="from" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dari Tanggal</label>
                         <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-event text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <input type="date" id="from" name="from" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ $from }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sampai Tanggal</label>
                         <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-check text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <input type="date" id="to" name="to" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ $to }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="only_paid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Pembayaran</label>
                        <select id="only_paid" name="only_paid" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="1" {{ request('only_paid', 1) == '1' ? 'selected' : '' }}>Hanya Transaksi Lunas</option>
                            <option value="0" {{ request('only_paid', 1) == '0' ? 'selected' : '' }}>Semua Transaksi</option>
                        </select>
                    </div>
                </div>
                <div class="md:col-span-1">
                     <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors">
                        <i class="bi bi-filter mr-2"></i> Tampilkan
                    </button>
                </div>
            </div>
            {{-- Quick Filters Links --}}
            <div class="mt-4 flex flex-wrap gap-2">
                 <a href="{{ request()->fullUrlWithQuery(['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="text-xs px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">Hari Ini</a>
                 <a href="{{ request()->fullUrlWithQuery(['from' => now()->subDays(6)->toDateString(), 'to' => now()->toDateString()]) }}" class="text-xs px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">7 Hari Terakhir</a>
                 <a href="{{ request()->fullUrlWithQuery(['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()]) }}" class="text-xs px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">Bulan Ini</a>
            </div>
        </form>
    </div>

    @php
        $profitMargin = $sum['omset'] > 0 ? round(($sum['total_profit'] / $sum['omset']) * 100, 1) : 0;
    @endphp

    {{-- KPI Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Total Transaksi --}}
        <div class="relative bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-receipt text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                    <i class="bi bi-list-check text-2xl"></i>
                </div>
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1 tracking-wide">Total Transaksi</p>
                    <h3 class="text-3xl font-bold tracking-tight">{{ number_format($sum['trx_count']) }}</h3>
                    <p class="text-xs text-purple-200 mt-1 opacity-80">Jumlah penjualan</p>
                </div>
            </div>
        </div>

        {{-- Total Omset --}}
        <div class="relative bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-wallet2 text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                    <i class="bi bi-cash-coin text-2xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1 tracking-wide">Total Omset</p>
                    <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($sum['omset']) }}</h3>
                    <p class="text-xs text-blue-200 mt-1 opacity-80">Pendapatan kotor</p>
                </div>
            </div>
        </div>

        {{-- Total HPP --}}
        <div class="relative bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-basket text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                    <i class="bi bi-box-seam text-2xl"></i>
                </div>
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1 tracking-wide">Total HPP</p>
                    <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($sum['total_hpp']) }}</h3>
                    <p class="text-xs text-orange-200 mt-1 opacity-80">Harga Pokok Penjualan</p>
                </div>
            </div>
        </div>

        {{-- Total Profit --}}
        <div class="relative bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-graph-up-arrow text-9xl"></i>
            </div>
            <div class="relative z-10">
                 <div class="flex items-start justify-between">
                     <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                        <i class="bi bi-trophy text-2xl"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-white/20 text-white text-xs font-bold rounded-lg backdrop-blur-sm">
                        {{ $profitMargin }}%
                    </span>
                </div>
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1 tracking-wide">Total Profit</p>
                    <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($sum['total_profit']) }}</h3>
                    <div class="w-full bg-black/20 rounded-full h-1.5 mt-2">
                         <div class="h-1.5 rounded-full bg-white/80" style="width:{{ min(max(abs($profitMargin),0),100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Table --}}
    <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 pt-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                Ringkasan Keuangan Detail
            </h3>
        </div>
        <div class="px-6 pb-6 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white w-1/3 flex items-center gap-2">
                            <i class="bi bi-list-check text-purple-600"></i> Total Transaksi
                        </th>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            {{ number_format($sum['trx_count']) }} transaksi
                        </td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="bi bi-wallet2 text-blue-600"></i> Total Omset
                        </th>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            {{ format_currency($sum['omset']) }}
                        </td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="bi bi-basket text-red-600"></i> Total HPP
                        </th>
                        <td class="px-6 py-4 text-right font-bold text-red-600 dark:text-red-400">
                            ({{ format_currency($sum['total_hpp']) }})
                        </td>
                    </tr>
                    <tr class="bg-green-50/50 dark:bg-green-900/10 border-t border-green-100 dark:border-green-800">
                        <th scope="row" class="px-6 py-5 font-bold text-green-900 dark:text-green-100 text-lg flex items-center gap-2">
                            <i class="bi bi-trophy-fill text-green-600"></i> Total Profit
                        </th>
                        <td class="px-6 py-5 text-right font-bold text-green-600 dark:text-green-400 text-xl">
                            {{ format_currency($sum['total_profit']) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payment Method Breakdown --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 pt-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg dark:bg-indigo-900/50 dark:text-indigo-400">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                Rincian per Metode Pembayaran
            </h3>
        </div>
        <div class="px-6 pb-6 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-100 dark:border-gray-600">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Metode Pembayaran</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-center">Jumlah Transaksi</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-right">Total Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($byMethod as $m)
                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-500 rounded-lg dark:bg-gray-700 dark:text-gray-400 mr-3">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $m->payment_method ?: 'Tidak Diketahui' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                {{ number_format($m->count) }} transaksi
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            {{ format_currency($m->amount) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="bi bi-inbox text-4xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                            Tidak ada data transaksi. Coba ubah filter tanggal atau status.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($byMethod) > 0)
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="row" class="px-6 py-4 text-base font-bold text-gray-900 dark:text-white">Total</th>
                        <td class="px-6 py-4 text-center text-base font-bold text-gray-900 dark:text-white">{{ number_format($byMethod->sum('count')) }} transaksi</td>
                        <td class="px-6 py-4 text-right text-base font-bold text-blue-600 dark:text-blue-400">{{ format_currency($byMethod->sum('amount')) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
