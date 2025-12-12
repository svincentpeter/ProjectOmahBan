@extends('layouts.app-flowbite')

@section('title', 'Laporan Kas Harian')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => 'Kas Harian', 'url' => '#', 'icon' => 'bi bi-wallet2'],
        ]
    ])
@endsection

@section('content')
    {{-- Filter Card --}}
    @include('layouts.filter-card', [
        'action' => route('reports.daily.generate'),
        'method' => 'POST',
        'title' => 'Filter Kas Harian',
        'icon' => 'bi bi-funnel',
        'filters' => [
            [
                'name' => 'report_date', // Must match backend expectation
                'label' => 'Tanggal Laporan',
                'type' => 'date',
                'value' => old('report_date', $reportDate ?? date('Y-m-d')),
                'required' => true
            ]
        ]
    ])

    @if (isset($generated) && $generated)
        @php
            $netPos   = ($netIncome ?? 0) >= 0;
            $gpMargin = ($totalOmset ?? 0) > 0 ? round(($netIncome / max($totalOmset,1)) * 100, 1) : 0;
        @endphp

        {{-- Report Header --}}
        <div class="mb-6 p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-file-earmark-text text-blue-600"></i>
                    Laporan Kas Harian
                </h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center">
                        <i class="bi bi-calendar-event mr-1.5"></i>
                        {{ \Carbon\Carbon::parse($reportDate)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all shadow-sm">
                    <i class="bi bi-printer mr-2"></i> Cetak Laporan
                </button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Omset Kotor --}}
            <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                    <i class="bi bi-wallet2 text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                        <i class="bi bi-cash-coin text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1 tracking-wide">Total Omset Kotor</p>
                        <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($totalOmset) }}</h3>
                        <p class="text-xs text-blue-200 mt-1 opacity-80">Semua transaksi hari ini</p>
                    </div>
                </div>
            </div>

            {{-- Pengeluaran --}}
            <div class="relative bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                    <i class="bi bi-building text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                        <i class="bi bi-cart-x text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-orange-100 text-sm font-medium mb-1 tracking-wide">Total Pengeluaran</p>
                        <h3 class="text-2xl font-bold tracking-tight">({{ format_currency($totalPengeluaran) }})</h3>
                        <p class="text-xs text-orange-200 mt-1 opacity-80">Biaya operasional hari ini</p>
                    </div>
                </div>
            </div>

            {{-- Pendapatan Bersih --}}
            <div class="relative bg-gradient-to-br from-emerald-500 to-green-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                    <i class="bi bi-graph-up-arrow text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between">
                         <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4 shadow-inner">
                            <i class="bi bi-piggy-bank text-2xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-white/20 text-white text-xs font-bold rounded-lg backdrop-blur-sm">
                            Margin: {{ $gpMargin }}%
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-emerald-100 text-sm font-medium mb-1 tracking-wide">Pendapatan Bersih</p>
                        <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($netIncome) }}</h3>
                        
                        {{-- Progress Bar for Visual Ratio --}}
                        <div class="w-full bg-black/20 rounded-full h-1.5 mt-3">
                            <div class="h-1.5 rounded-full bg-white/80" style="width: {{ min(max(abs($gpMargin),0),100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sales Table --}}
        <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400">
                        <i class="bi bi-cart3"></i>
                    </div>
                    Daftar Penjualan
                </h3>
                <span class="text-xs font-medium px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-300">
                    {{ count($sales) }} Transaksi
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-100 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">Reference</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Waktu</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Item Terjual</th>
                            <th scope="col" class="px-6 py-3 font-semibold text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($sales as $sale)
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white align-top">
                                {{ $sale->reference }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 align-top">
                                {{ optional($sale->created_at)->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 align-top">
                                 @if ($sale->saleDetails && $sale->saleDetails->count())
                                    <ul class="space-y-1.5">
                                        @foreach ($sale->saleDetails as $d)
                                            @php
                                                $name  = $d->item_name ?? optional($d->product)->name ?? ($d->product_name ?? 'Item');
                                                $qty   = (int) ($d->quantity ?? $d->qty ?? 1);
                                                $price = (int) ($d->unit_price ?? $d->price ?? 0);
                                                $sub   = (int) ($d->sub_total ?? ($qty * $price));
                                            @endphp
                                            <li class="flex items-start text-xs gap-2">
                                                <i class="bi bi-dot text-gray-300 mt-0.5"></i>
                                                <div class="flex-1">
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $name }}</span>
                                                    <div class="text-gray-500 text-[10px]">
                                                        {{ $qty }} x {{ format_currency($price) }}
                                                    </div>
                                                </div>
                                                <span class="font-semibold text-gray-900 dark:text-white">{{ format_currency($sub) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <em class="text-gray-400 text-xs">Tidak ada item</em>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white align-top">
                                {{ format_currency($sale->total_amount) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="bi bi-inbox text-4xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                                Tidak ada penjualan pada tanggal ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th colspan="3" class="px-6 py-4 text-right font-bold text-gray-700 dark:text-gray-300 uppercase text-xs tracking-wider">Total Penjualan</th>
                            <td class="px-6 py-4 text-right font-bold text-blue-600 dark:text-blue-400">{{ format_currency($totalOmset) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Expenses Table --}}
        <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
             <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <div class="p-1.5 bg-red-100 text-red-600 rounded-lg dark:bg-red-900/50 dark:text-red-400">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    Daftar Pengeluaran
                </h3>
                <span class="text-xs font-medium px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-300">
                    {{ count($expenses) }} Transaksi
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-100 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">Reference</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Kategori</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Detail</th>
                            <th scope="col" class="px-6 py-3 font-semibold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($expenses as $ex)
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white align-top">
                                {{ $ex->reference }}
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="bg-blue-50 text-blue-600 text-[10px] uppercase font-bold px-2 py-0.5 rounded border border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                    {{ optional($ex->category)->category_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="text-gray-800 dark:text-gray-200 font-medium mb-1">{{ $ex->details ?? '-' }}</div>
                                <div class="text-xs text-gray-500 flex items-center gap-3">
                                    <span class="flex items-center gap-1"><i class="bi bi-person"></i> {{ optional($ex->user)->name ?? '—' }}</span>
                                    <span class="flex items-center gap-1"><i class="bi bi-clock"></i> {{ optional($ex->created_at)->format('H:i') ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-red-600 dark:text-red-400 align-top">
                                {{ format_currency($ex->amount) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="bi bi-check-circle text-4xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                                Tidak ada pengeluaran pada tanggal ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th colspan="3" class="px-6 py-4 text-right font-bold text-gray-700 dark:text-gray-300 uppercase text-xs tracking-wider">Total Pengeluaran</th>
                            <td class="px-6 py-4 text-right font-bold text-red-600 dark:text-red-400">{{ format_currency($totalPengeluaran) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Summary Table --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <div class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg dark:bg-indigo-900/50 dark:text-indigo-400">
                        <i class="bi bi-calculator"></i>
                    </div>
                    Ringkasan Keuangan
                </h3>
            </div>
            <div>
                 <table class="w-full text-sm text-left">
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white w-1/3">
                                Total Omset Kotor
                            </th>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                {{ format_currency($totalOmset) }}
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                Total Pengeluaran
                            </th>
                            <td class="px-6 py-4 text-right font-bold text-red-600 dark:text-red-400">
                                ({{ format_currency($totalPengeluaran) }})
                            </td>
                        </tr>
                        <tr class="bg-emerald-50/50 dark:bg-emerald-900/10">
                            <th scope="row" class="px-6 py-4 font-bold text-emerald-800 dark:text-emerald-300">
                                Pendapatan Bersih
                            </th>
                            <td class="px-6 py-4 text-right font-bold {{ $netPos ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} text-lg">
                                {{ format_currency($netIncome) }}
                            </td>
                        </tr>
                        <tr class="bg-white dark:bg-gray-800 border-t-2 border-dashed border-gray-200 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white align-top pt-6">
                                Rincian Penerimaan (Payments)
                            </th>
                            <td class="px-6 py-4 text-right pt-6">
                                @include('reports::components.payments-summary', ['receipts' => $receipts])
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
