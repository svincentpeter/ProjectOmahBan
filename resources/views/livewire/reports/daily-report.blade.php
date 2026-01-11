<div wire:key="daily-root">
    {{-- Filter Card (Styled to match layouts.filter-card) --}}
    {{-- Main Control Card --}}
    <div class="mb-6 p-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <div class="p-2 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-600/20">
                        <i class="bi bi-wallet2 text-xl"></i>
                    </div>
                    Laporan Kas Harian
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 pl-[3.25rem]">
                    Rekapitulasi transaksi harian, omset, dan pengeluaran operasional.
                </p>
            </div>

            {{-- Export & Comparison Buttons --}}
            <div class="flex items-center gap-2 pl-[3.25rem] md:pl-0">
                <button wire:click="toggleComparison" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border {{ $showComparison ? 'bg-purple-50 text-purple-700 border-purple-200 ring-1 ring-purple-500/20 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700' }} transition-all shadow-sm">
                    <i class="bi bi-arrow-left-right mr-2"></i> Bandingkan
                </button>
                <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-2 hidden md:block"></div>
                <div class="flex rounded-xl shadow-sm" role="group">
                    <button wire:click="exportCsv" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-l-xl hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition-all">
                        <i class="bi bi-filetype-csv mr-2"></i> CSV
                    </button>
                    <button wire:click="exportExcel" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-white border-t border-b border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-green-400 dark:hover:bg-gray-700 transition-all">
                        <i class="bi bi-file-earmark-excel mr-2"></i> Excel
                    </button>
                    <button wire:click="exportPdf" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-gray-200 rounded-r-xl hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-red-400 dark:hover:bg-gray-700 transition-all">
                        <i class="bi bi-file-earmark-pdf mr-2"></i> PDF
                    </button>
                </div>
            </div>
        </div>
        
        <form wire:submit.prevent>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-calendar-event mr-1.5 text-blue-600"></i> Tanggal
                    </label>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-calendar-date text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <input wire:model.debounce.500ms="date" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-person mr-1.5 text-blue-600"></i> Kasir
                    </label>
                     <div class="relative">
                         <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-person-badge text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <select wire:model="cashierId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                            <option value="">— Semua Kasir —</option>
                            @foreach($this->cashiers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-credit-card mr-1.5 text-blue-600"></i> Metode Pembayaran
                    </label>
                     <div class="relative">
                         <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-wallet2 text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <select wire:model="paymentMethod" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                            <option value="">— Semua Metode —</option>
                            @foreach($this->methodOptions as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-bank mr-1.5 text-blue-600"></i> Bank (Opsional)
                    </label>
                     <div class="relative">
                         <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-bank2 text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <input wire:model.debounce.500ms="bankName" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors" placeholder="e.g., BCA, QRIS...">
                    </div>
                </div>
            </div>

            {{-- Loading Indicator --}}
            <div wire:loading.flex class="flex items-center mt-4 p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                 <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-medium">Memuat ulang data laporan...</span>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        
        {{-- Total Transaksi --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition border-l-4 border-l-blue-600">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-300">Total Transaksi</p>
                    <h3 class="mt-1 text-3xl font-extrabold text-slate-900 dark:text-white leading-none tabular-nums">
                        {{ number_format($ringkasanPembayaran->sum('trx_count'), 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">Transaksi hari ini</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-50 text-blue-700 ring-1 ring-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-900/50">
                    <i class="bi bi-receipt text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Omset --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition border-l-4 border-l-indigo-600">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-300">Total Omset</p>
                    <div class="mt-1 flex items-baseline gap-2 whitespace-nowrap">
                        <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">Rp</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white leading-none tabular-nums tracking-tight">
                            {{ format_currency($omzet) }}
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">Pemasukan kotor</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:ring-indigo-900/50">
                    <i class="bi bi-wallet2 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Pengeluaran --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition border-l-4 border-l-amber-600">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-300">Total Pengeluaran</p>
                    <div class="mt-1 flex items-baseline gap-2 whitespace-nowrap">
                        <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">Rp</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white leading-none tabular-nums tracking-tight">
                            {{ format_currency($pengeluaran) }}
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">Beban operasional</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-700 ring-1 ring-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-900/50">
                    <i class="bi bi-cash-stack text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Income Bersih --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-md transition border-l-4 border-l-emerald-600">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-300">Income Bersih</p>
                    <div class="mt-1 flex items-baseline gap-2 whitespace-nowrap">
                        <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">Rp</span>
                        <h3 class="text-2xl font-extrabold leading-none tabular-nums tracking-tight {{ $incomeBersih >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ format_currency($incomeBersih) }}
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">Omset - Pengeluaran</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-900/50">
                    <i class="bi bi-graph-up-arrow text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Period Comparison Section --}}
    @if($showComparison)
    <div class="mb-6 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-purple-50 dark:bg-purple-900/20 border-b border-purple-100 dark:border-purple-800">
            <h4 class="text-base font-bold text-purple-800 dark:text-purple-300 flex items-center gap-2">
                <i class="bi bi-arrow-left-right"></i>
                Perbandingan dengan Hari Sebelumnya
            </h4>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Omzet Comparison --}}
                <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Omzet</p>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ format_currency($omzet) }}</div>
                    @php
                        $omzetDiff = $omzet - $prevOmzet;
                        $omzetPct = $prevOmzet > 0 ? round(($omzetDiff / $prevOmzet) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-sm {{ $omzetDiff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <i class="bi {{ $omzetDiff >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                            {{ $omzetDiff >= 0 ? '+' : '' }}{{ format_currency($omzetDiff) }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $omzetDiff >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $omzetPct >= 0 ? '+' : '' }}{{ $omzetPct }}%
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Kemarin: {{ format_currency($prevOmzet) }}</p>
                </div>

                {{-- Pengeluaran Comparison --}}
                <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Pengeluaran</p>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ format_currency($pengeluaran) }}</div>
                    @php
                        $expDiff = $pengeluaran - $prevPengeluaran;
                        $expPct = $prevPengeluaran > 0 ? round(($expDiff / $prevPengeluaran) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-sm {{ $expDiff <= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <i class="bi {{ $expDiff <= 0 ? 'bi-arrow-down' : 'bi-arrow-up' }}"></i>
                            {{ $expDiff >= 0 ? '+' : '' }}{{ format_currency($expDiff) }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $expDiff <= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $expPct >= 0 ? '+' : '' }}{{ $expPct }}%
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Kemarin: {{ format_currency($prevPengeluaran) }}</p>
                </div>

                {{-- Income Bersih Comparison --}}
                <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Income Bersih</p>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ format_currency($incomeBersih) }}</div>
                    @php
                        $incDiff = $incomeBersih - $prevIncomeBersih;
                        $incPct = $prevIncomeBersih != 0 ? round(($incDiff / abs($prevIncomeBersih)) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-sm {{ $incDiff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <i class="bi {{ $incDiff >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                            {{ $incDiff >= 0 ? '+' : '' }}{{ format_currency($incDiff) }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $incDiff >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $incPct >= 0 ? '+' : '' }}{{ $incPct }}%
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Kemarin: {{ format_currency($prevIncomeBersih) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Chart Section --}}
    @if(count($chartData ?? []) > 0)
    <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h4 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="bi bi-pie-chart text-blue-600"></i>
                Komposisi Metode Pembayaran
            </h4>
        </div>
        <div class="p-6">
            <div class="flex justify-center">
                <div class="w-full max-w-md">
                    <canvas id="paymentChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400" id="reportTab" data-tabs-toggle="#reportTabContent" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-flex items-center p-4 border-b-2 rounded-t-lg group hover:text-blue-600 hover:border-blue-600 dark:hover:text-blue-500 transition-colors" id="summary-tab" data-tabs-target="#summary-pane" type="button" role="tab" aria-controls="summary-pane" aria-selected="false">
                    <i class="bi bi-pie-chart-fill mr-2 text-lg"></i>
                    Ringkasan Penerimaan
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-flex items-center p-4 border-b-2 rounded-t-lg group hover:text-blue-600 hover:border-blue-600 dark:hover:text-blue-500 transition-colors" id="transactions-tab" data-tabs-target="#transactions-pane" type="button" role="tab" aria-controls="transactions-pane" aria-selected="false">
                    <i class="bi bi-journal-text mr-2 text-lg"></i>
                    Detail Transaksi
                </button>
            </li>
        </ul>
    </div>

    {{-- Tab Content --}}
    <div id="reportTabContent">
        {{-- Summary Tab --}}
        <div class="hidden p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700" id="summary-pane" role="tabpanel" aria-labelledby="summary-tab">
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="bi bi-wallet2 text-blue-600"></i> Rincian per Metode Pembayaran
            </h4>
            <div class="relative overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold rounded-tl-xl">Metode Pembayaran</th>
                            <th scope="col" class="px-6 py-4 font-bold">Bank</th>
                            <th scope="col" class="px-6 py-4 font-bold text-center">Jumlah Transaksi</th>
                            <th scope="col" class="px-6 py-4 font-bold text-right rounded-tr-xl">Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($ringkasanPembayaran as $row)
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <DIV class="flex items-center">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mr-3 dark:bg-indigo-900/30 dark:text-indigo-400">
                                        <i class="bi bi-credit-card-2-front"></i>
                                    </div>
                                    <span class="text-base">{{ $row->payment_method }}</span>
                                </DIV>
                            </td>
                            <td class="px-6 py-4">
                                @if($row->bank_name)
                                    <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-1 rounded dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        {{ $row->bank_name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                    {{ $row->trx_count }} Transaksi
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white text-base">
                                {{ format_currency($row->total_amount) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 dark:bg-gray-700">
                                        <i class="bi bi-inbox text-3xl text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-900 dark:text-white mb-1">Tidak ada data penerimaan.</p>
                                    <p class="text-sm text-gray-500">Belum ada transaksi pembayaran untuk periode ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($ringkasanPembayaran) > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 font-bold text-gray-900 dark:text-white">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right text-base rounded-bl-xl">TOTAL KESELURUHAN</td>
                            <td class="px-6 py-4 text-center text-base">{{ $ringkasanPembayaran->sum('trx_count') }}</td>
                            <td class="px-6 py-4 text-right text-blue-600 dark:text-blue-400 text-lg rounded-br-xl">{{ format_currency($ringkasanPembayaran->sum('total_amount')) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Transactions Tab --}}
        <div class="hidden p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700" id="transactions-pane" role="tabpanel" aria-labelledby="transactions-tab">
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="bi bi-list-columns-reverse text-blue-600"></i> Log Transaksi Masuk
            </h4>
            <div class="relative overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold rounded-tl-xl">Waktu Transaksi</th>
                            <th scope="col" class="px-6 py-4 font-bold">Ref No.</th>
                            <th scope="col" class="px-6 py-4 font-bold">Kasir</th>
                            <th scope="col" class="px-6 py-4 font-bold text-center">Status</th>
                            <th scope="col" class="px-6 py-4 font-bold text-right rounded-tr-xl">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($transaksi as $s)
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 dark:text-white">
                                        {{ \Illuminate\Support\Carbon::parse($s->date)->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="text-xs text-gray-500 flex items-center gap-1">
                                        <i class="bi bi-clock"></i> {{ \Illuminate\Support\Carbon::parse($s->date)->format('H:i') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 text-gray-700 text-xs font-mono font-medium px-2.5 py-1 rounded border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    #{{ $s->reference }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-2 text-xs dark:bg-indigo-900 dark:text-indigo-300">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $s->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $status = $s->payment_status ?? $s->status;
                                    $isPaid = $status === 'Paid';
                                @endphp
                                @if($isPaid)
                                    <span class="inline-flex items-center bg-green-100 text-green-800 text-xs font-bold px-2.5 py-1 rounded-full dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800">
                                        <i class="bi bi-check-circle-fill mr-1.5"></i> Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-1 rounded-full dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 sticky-status-badge">
                                        <i class="bi bi-hourglass-split mr-1.5"></i> {{ $status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-gray-900 dark:text-white">
                                    {{ format_currency($s->total_amount) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                 <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 dark:bg-gray-700">
                                        <i class="bi bi-journal-x text-3xl text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-900 dark:text-white mb-1">Tidak ada data transaksi.</p>
                                    <p class="text-sm text-gray-500">Coba ubah filter tanggal atau kriteria pencarian.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- Flowbite Tabs JS Helper + Chart.js for Pie Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initPaymentChart();
        });

        document.addEventListener('livewire:navigated', function () {
            initPaymentChart();
        });

        function initPaymentChart() {
            const chartCanvas = document.getElementById('paymentChart');
            if (!chartCanvas) return;

            // Destroy existing chart if any
            if (window.paymentChartInstance) {
                window.paymentChartInstance.destroy();
            }

            const chartData = @json($chartData ?? []);
            if (chartData.length === 0) return;

            const labels = chartData.map(item => item.label);
            const values = chartData.map(item => item.value);
            const colors = [
                '#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444',
                '#06b6d4', '#ec4899', '#84cc16', '#f97316', '#6366f1'
            ];

            window.paymentChartInstance = new Chart(chartCanvas, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors.slice(0, labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = ((value / total) * 100).toFixed(1);
                                    return `${context.label}: Rp ${value.toLocaleString('id-ID')} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</div>
