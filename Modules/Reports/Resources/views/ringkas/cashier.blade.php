@extends('layouts.app-flowbite')

@section('title', 'Laporan Kinerja Kasir')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => 'Kinerja Kasir', 'url' => '#', 'icon' => 'bi bi-person-lines-fill'],
        ]
    ])
@endsection

@section('content')
    @php
        // Fallback vars are handled in controller usually, but here we keep safety
        $from = $from ?? request('from', now()->startOfMonth()->toDateString());
        $to = $to ?? request('to', now()->toDateString());

        // Calculate aggregate
        $totTrx = (int) $rows->sum('trx_count');
        $totOmset = (int) $rows->sum('omset');
        $totHpp = (int) $rows->sum('total_hpp');
        $totProfit = (int) $rows->sum('total_profit');
        $npMargin = $totOmset > 0 ? round(($totProfit / max($totOmset, 1)) * 100, 1) : 0;

        // Helper format angka (biar Rp tidak “ikut membesar”)
        $fmtMoney = fn($n) => number_format((int) $n, 0, ',', '.');
    @endphp

    {{-- Main Control Card --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
             <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-file-earmark-person text-blue-600"></i>
                    Laporan Kinerja Kasir
                </h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
                     <span class="flex items-center">
                        <i class="bi bi-calendar-range mr-1.5"></i>
                        Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="text-right flex items-center justify-end gap-2">
                <a href="{{ route('ringkas-report.cashier.export-excel', request()->all()) }}"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-xl hover:bg-green-100 focus:ring-4 focus:ring-green-100 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800 dark:hover:bg-green-900/50 transition-all shadow-sm">
                    <i class="bi bi-file-earmark-excel mr-2"></i> Export Excel
                </a>
                <a href="{{ route('ringkas-report.cashier.export-pdf', request()->all()) }}" target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 focus:ring-4 focus:ring-red-100 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800 dark:hover:bg-red-900/50 transition-all shadow-sm">
                    <i class="bi bi-file-earmark-pdf mr-2"></i> Export PDF
                </a>
            </div>
        </div>

        <form action="{{ route('ringkas-report.cashier') }}" method="GET"
              class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-4 gap-4">
                     <div>
                         <label for="from" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dari Tanggal</label>
                         <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-event text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <input type="date" id="from" name="from"
                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                   value="{{ $from }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sampai Tanggal</label>
                         <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-check text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <input type="date" id="to" name="to"
                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                   value="{{ $to }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Kasir</label>
                        <select id="user_id" name="user_id"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">— Semua Kasir —</option>
                            @foreach($cashiers as $id => $name)
                                <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="only_paid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Pembayaran</label>
                        <select id="only_paid" name="only_paid"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="1" {{ request('only_paid', 1) == '1' ? 'selected' : '' }}>Hanya Lunas</option>
                            <option value="0" {{ request('only_paid', 1) == '0' ? 'selected' : '' }}>Semua</option>
                        </select>
                    </div>
                </div>
                <div class="md:col-span-1">
                     <button type="submit"
                             class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors">
                        <i class="bi bi-filter mr-2"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards (Line Color - no full gradient) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        {{-- Total Transaksi --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                    border-l-4 border-l-blue-600">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-300">Total Transaksi</p>
                    <h3 class="mt-1 text-3xl font-extrabold text-slate-900 dark:text-white leading-none tabular-nums">
                        {{ number_format($totTrx, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">Semua kasir terpilih</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center
                            bg-blue-50 text-blue-700 ring-1 ring-blue-100
                            dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-900/50">
                    <i class="bi bi-list-check text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Omset --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                    border-l-4 border-l-indigo-600">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-300">Total Omset</p>

                    <div class="mt-1 flex items-baseline gap-2 whitespace-nowrap">
                        <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">Rp</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white leading-none tabular-nums tracking-tight">
                            {{ $fmtMoney($totOmset) }}
                        </h3>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">Akumulasi penjualan</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center
                            bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100
                            dark:bg-indigo-900/30 dark:text-indigo-300 dark:ring-indigo-900/50">
                    <i class="bi bi-wallet2 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total HPP --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                    border-l-4 border-l-amber-600">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-300">Total HPP</p>

                    <div class="mt-1 flex items-baseline gap-2 whitespace-nowrap">
                        <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">(Rp</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white leading-none tabular-nums tracking-tight">
                            {{ $fmtMoney($totHpp) }}
                        </h3>
                        <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">)</span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">Harga pokok terjual</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center
                            bg-amber-50 text-amber-700 ring-1 ring-amber-100
                            dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-900/50">
                    <i class="bi bi-box-seam text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Profit --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition
                    border-l-4 border-l-emerald-600">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-slate-600 dark:text-gray-300">Total Profit</p>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-lg
                                     bg-emerald-50 text-emerald-700 border border-emerald-100
                                     dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-900/50">
                            {{ $npMargin }}%
                        </span>
                    </div>

                    <div class="mt-1 flex items-baseline gap-2 whitespace-nowrap">
                        <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">Rp</span>
                        <h3 class="text-2xl font-extrabold leading-none tabular-nums tracking-tight
                                   {{ $totProfit >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $fmtMoney($totProfit) }}
                        </h3>
                    </div>

                    <div class="w-full bg-slate-100 dark:bg-gray-700 rounded-full h-1.5 mt-3 overflow-hidden">
                        <div class="h-1.5 rounded-full bg-emerald-500"
                             style="width:{{ min(max(abs($npMargin),0),100) }}%"></div>
                    </div>
                </div>

                <div class="w-12 h-12 rounded-xl flex items-center justify-center
                            bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100
                            dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-900/50">
                    <i class="bi bi-graph-up-arrow text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Performance Table --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 pt-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
                Kinerja Individual Kasir
            </h3>
        </div>
        <div class="px-6 pb-6 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-100 dark:border-gray-600">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Nama Kasir</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-center">Transaksi</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-right">Omset</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-right">HPP</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-right">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($rows as $r)
                        @php
                            $p = (int) ($r->total_profit ?? 0);
                            $m = ($r->omset ?? 0) > 0 ? round(($p / max($r->omset, 1)) * 100, 1) : 0;
                        @endphp
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400 mr-3">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ optional($r->user)->name ?? 'Unknown' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                    {{ number_format($r->trx_count, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">
                                {{ format_currency($r->omset) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400">
                                {{ format_currency($r->total_hpp) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-col items-end">
                                    <span class="font-bold {{ $p >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ format_currency($p) }}
                                    </span>
                                    <span class="text-xs text-gray-500 mt-0.5">{{ $m }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="bi bi-inbox text-4xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                                Tidak ada data transaksi. Coba ubah filter periode atau kasir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($rows) > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="row" class="px-6 py-4 text-base font-bold text-gray-900 dark:text-white">TOTAL</th>
                            <td class="px-6 py-4 text-center text-base font-bold text-gray-900 dark:text-white">{{ number_format($totTrx, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-base font-bold text-blue-600 dark:text-blue-400">{{ format_currency($totOmset) }}</td>
                            <td class="px-6 py-4 text-right text-base font-bold text-orange-600 dark:text-orange-400">{{ format_currency($totHpp) }}</td>
                            <td class="px-6 py-4 text-right text-base font-bold {{ $totProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ format_currency($totProfit) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
