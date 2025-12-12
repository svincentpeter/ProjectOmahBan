@extends('layouts.app-flowbite')

@section('title', 'Laporan Laba Rugi')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => 'Laba Rugi', 'url' => '#', 'icon' => 'bi bi-graph-up-arrow'],
        ]
    ])
@endsection

@section('content')
    {{-- Filter Card --}}
    @include('layouts.filter-card', [
        'action' => route('reports.profit_loss.generate'),
        'method' => 'POST',
        'title' => 'Filter Periode Laporan',
        'icon' => 'bi bi-calendar-range',
        'quickFilters' => [
             ['label' => 'Hari Ini', 'url' => route('reports.profit_loss.index', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]), 'param' => 'ignore', 'value' => 'ignore', 'icon' => 'bi bi-calendar-event'],
             ['label' => '7 Hari Terakhir', 'url' => route('reports.profit_loss.index', ['start_date' => now()->subDays(6)->toDateString(), 'end_date' => now()->toDateString()]), 'param' => 'ignore', 'value' => 'ignore', 'icon' => 'bi bi-calendar-week'],
             ['label' => 'Bulan Ini', 'url' => route('reports.profit_loss.index', ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()]), 'param' => 'ignore', 'value' => 'ignore', 'icon' => 'bi bi-calendar-month'],
             ['label' => 'Bulan Lalu', 'url' => route('reports.profit_loss.index', ['start_date' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(), 'end_date' => now()->subMonthNoOverflow()->endOfMonth()->toDateString()]), 'param' => 'ignore', 'value' => 'ignore', 'icon' => 'bi bi-calendar-minus'],
        ],
        'filters' => [
            [
                'name' => 'start_date',
                'label' => 'Tanggal Awal',
                'type' => 'date',
                'value' => old('start_date', $startDate ?? date('Y-m-d')),
                'required' => true
            ],
            [
                'name' => 'end_date',
                'label' => 'Tanggal Akhir',
                'type' => 'date',
                'value' => old('end_date', $endDate ?? date('Y-m-d')),
                'required' => true
            ]
        ]
    ])

    @if (!empty($generated))
    @php
        $gpMargin = ($revenue ?? 0) > 0 ? round(($grossProfit / max($revenue,1)) * 100, 1) : 0;
        $npMargin = ($revenue ?? 0) > 0 ? round(($netProfit   / max($revenue,1)) * 100, 1) : 0;
        $gpPos    = ($grossProfit ?? 0) >= 0;
        $npPos    = ($netProfit   ?? 0) >= 0;
    @endphp

    {{-- Report Header --}}
    <div class="mb-6 p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet text-blue-600"></i>
                Laporan Laba Rugi
            </h2>
            <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center">
                    <i class="bi bi-calendar-range mr-1.5"></i>
                    {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMM Y') }} – {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMM Y') }}
                </span>
            </div>
        </div>
        <div class="text-right">
            <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all shadow-sm">
                <i class="bi bi-printer mr-2"></i> Cetak Laporan
            </button>
        </div>
    </div>

    {{-- KPI Cards Row 1: High Level --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Revenue --}}
        <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-wallet2 text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                    <i class="bi bi-cash text-2xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1 tracking-wide">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($revenue) }}</h3>
                    <p class="text-xs text-blue-200 mt-1 opacity-80">Dari penjualan produk</p>
                </div>
            </div>
        </div>

        {{-- COGS --}}
        <div class="relative bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-basket text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                    <i class="bi bi-box-seam text-2xl"></i>
                </div>
                <div>
                    <p class="text-rose-100 text-sm font-medium mb-1 tracking-wide">HPP / COGS</p>
                    <h3 class="text-2xl font-bold tracking-tight">({{ format_currency($cogs) }})</h3>
                    <p class="text-xs text-rose-200 mt-1 opacity-80">Harga pokok penjualan</p>
                </div>
            </div>
        </div>

        {{-- Gross Profit --}}
        <div class="relative bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-graph-up text-9xl"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                     <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                        <i class="bi bi-layers-half text-2xl"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-white/20 text-white text-xs font-bold rounded-lg backdrop-blur-sm">
                        {{ $gpMargin }}%
                    </span>
                </div>
                <div>
                    <p class="text-teal-100 text-sm font-medium mb-1 tracking-wide">Laba Kotor</p>
                    <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($grossProfit) }}</h3>
                    <div class="w-full bg-black/20 rounded-full h-1.5 mt-2">
                         <div class="h-1.5 rounded-full bg-white/80" style="width:{{ min(max(abs($gpMargin),0),100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row 2: OpEx & Net Profit --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Operating Expenses --}}
        <div class="relative bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-building text-9xl"></i>
            </div>
            <div class="relative z-10">
                 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                    <i class="bi bi-shop text-2xl"></i>
                </div>
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1 tracking-wide">Beban Operasional</p>
                    <h3 class="text-2xl font-bold tracking-tight">({{ format_currency($operatingExpenses) }})</h3>
                    <p class="text-xs text-orange-200 mt-1 opacity-80">Biaya gaji, listrik, sewa, dll</p>
                </div>
            </div>
        </div>

        {{-- Net Profit --}}
        <div class="md:col-span-2 relative bg-gradient-to-br from-green-600 to-emerald-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.01] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-trophy text-9xl"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6 h-full">
                <div class="flex-1">
                     <div class="flex items-center gap-4 mb-2">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-inner hidden md:flex">
                            <i class="bi bi-piggy-bank text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-green-100 text-sm font-medium tracking-wide">Laba Bersih Sebelum Pajak</p>
                            <h3 class="text-3xl font-bold tracking-tight mt-1">{{ format_currency($netProfit) }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/10 md:min-w-[200px]">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-green-100 text-xs">Net Margin</span>
                        <span class="text-white font-bold">{{ $npMargin }}%</span>
                    </div>
                    <div class="w-full bg-black/20 rounded-full h-2">
                         <div class="h-2 rounded-full bg-white shadow-sm" style="width: {{ min(max(abs($npMargin),0),100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Table --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400">
                    <i class="bi bi-list-check"></i>
                </div>
                Ringkasan Perhitungan Detail
            </h3>
        </div>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white w-1/2 flex items-center gap-2">
                            <i class="bi bi-plus-lg text-blue-500"></i> Pendapatan (Revenue)
                        </th>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            {{ format_currency($revenue) }}
                        </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white w-1/2 flex items-center gap-2">
                            <i class="bi bi-dash-lg text-red-500"></i> Harga Pokok Penjualan (HPP / COGS)
                        </th>
                        <td class="px-6 py-4 text-right font-bold text-red-600 dark:text-red-400">
                            ({{ format_currency($cogs) }})
                        </td>
                    </tr>
                    <tr class="bg-teal-50/50 dark:bg-teal-900/10 border-t border-b border-teal-100 dark:border-teal-800/30">
                        <th scope="row" class="px-6 py-4 font-bold text-teal-800 dark:text-teal-300 flex items-center gap-2">
                            <i class="bi bi-pause-fill rotate-90 text-teal-600"></i> Laba Kotor (Gross Profit)
                        </th>
                        <td class="px-6 py-4 text-right font-bold {{ $gpPos ? 'text-teal-700 dark:text-teal-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ format_currency($grossProfit) }}
                        </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white w-1/2 flex items-center gap-2">
                            <i class="bi bi-dash-lg text-orange-500"></i> Beban Operasional
                        </th>
                        <td class="px-6 py-4 text-right font-bold text-orange-600 dark:text-orange-400">
                            ({{ format_currency($operatingExpenses) }})
                        </td>
                    </tr>
                    <tr class="bg-green-50/80 dark:bg-green-900/20 border-t border-green-200 dark:border-green-800">
                        <th scope="row" class="px-6 py-5 font-bold text-green-900 dark:text-green-100 text-lg flex items-center gap-2">
                            <i class="bi bi-check-lg text-green-600"></i> Laba Bersih Sebelum Pajak
                        </th>
                        <td class="px-6 py-5 text-right font-bold {{ $npPos ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} text-xl">
                            {{ format_currency($netProfit) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
