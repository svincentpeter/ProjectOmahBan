<div wire:key="pl-root">


        {{-- Filter Card --}}
        <div class="p-5 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <div class="p-2 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-600/20">
                        <i class="bi bi-wallet2 text-xl"></i>
                    </div>
                    Laporan Laba Rugi
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 pl-[3.25rem]">
                    Analisa pendapatan, HPP, dan beban operasional perusahaan.
                </p>
            </div>

            {{-- Export & Comparison Buttons --}}
            <div class="flex items-center gap-2 pl-[3.25rem] md:pl-0">
                <button wire:click="toggleComparison" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border {{ $showComparison ? 'bg-purple-50 text-purple-700 border-purple-200 ring-1 ring-purple-500/20 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700' }} transition-all shadow-sm">
                    <i class="bi bi-arrow-left-right mr-2"></i> Bandingkan
                </button>
                <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-2 hidden md:block"></div>
                <div class="flex rounded-xl shadow-sm" role="group">
                    <button wire:click="exportExcel" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-white border border-gray-200 rounded-l-xl hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-green-400 dark:hover:bg-gray-700 transition-all">
                        <i class="bi bi-file-earmark-excel mr-2"></i> Excel
                    </button>
                    <button wire:click="exportPdf" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border-t border-b border-r border-gray-200 rounded-r-xl hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-red-400 dark:hover:bg-gray-700 transition-all">
                        <i class="bi bi-file-earmark-pdf mr-2"></i> PDF
                    </button>
                </div>
            </div>
        </div>

            <form wire:submit.prevent="generateReport">
                <div class="flex flex-col md:flex-row gap-6 items-end">
                    
                    {{-- Date Range Inputs --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Dari Tanggal
                            </label>
                            <input wire:model="startDate" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transaction-colors">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Sampai Tanggal
                            </label>
                            <input wire:model="endDate" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transaction-colors">
                        </div>
                    </div>

                    {{-- Quick Filters --}}
                    <div class="flex md:flex-col gap-2 overflow-x-auto pb-1 md:pb-0">
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:block mb-1">Filter Cepat</label>
                        <div class="flex gap-2">
                            <button type="button" wire:click="setPeriod('this_month')" class="px-3 py-1.5 text-xs font-medium border rounded-full transition-all whitespace-nowrap {{ $activePeriod === 'this_month' ? 'bg-blue-100 text-blue-700 border-blue-200 ring-2 ring-blue-500/20 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-700' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700' }}">
                                Bulan Ini
                            </button>
                            <button type="button" wire:click="setPeriod('last_month')" class="px-3 py-1.5 text-xs font-medium border rounded-full transition-all whitespace-nowrap {{ $activePeriod === 'last_month' ? 'bg-blue-100 text-blue-700 border-blue-200 ring-2 ring-blue-500/20 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-700' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700' }}">
                                Bulan Lalu
                            </button>
                            <button type="button" wire:click="setPeriod('this_year')" class="px-3 py-1.5 text-xs font-medium border rounded-full transition-all whitespace-nowrap {{ $activePeriod === 'this_year' ? 'bg-blue-100 text-blue-700 border-blue-200 ring-2 ring-blue-500/20 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-700' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700' }}">
                                Tahun Ini
                            </button>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div>
                         <button type="submit" class="w-full md:w-auto text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-6 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-all shadow-md hover:shadow-lg flex items-center justify-center min-w-[120px]">
                            <span wire:loading.remove wire:target="generateReport, setPeriod">
                                <i class="bi bi-filter mr-2"></i> Terapkan
                            </span>
                            <div wire:loading wire:target="generateReport, setPeriod">
                                <span class="flex items-center gap-2"><i class="bi bi-arrow-repeat animate-spin"></i> Loading...</span>
                            </div>
                        </button>
                    </div>
                </div>
            </form>
        </div>


    {{-- Period Comparison Section --}}
    @if($showComparison)
    <div class="mb-6 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-purple-50 dark:bg-purple-900/20 border-b border-purple-100 dark:border-purple-800">
            <h4 class="text-base font-bold text-purple-800 dark:text-purple-300 flex items-center gap-2">
                <i class="bi bi-arrow-left-right"></i>
                Perbandingan dengan Periode Sebelumnya
            </h4>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Revenue --}}
                @php
                    $revDiff = $revenue - $prevRevenue;
                    $revPct = $prevRevenue > 0 ? round(($revDiff / $prevRevenue) * 100, 1) : 0;
                @endphp
                <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Revenue</p>
                    <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ format_currency($revenue) }}</div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $revDiff >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $revPct >= 0 ? '+' : '' }}{{ $revPct }}%
                    </span>
                </div>

                {{-- COGS --}}
                @php
                    $cogsDiff = $cogs - $prevCogs;
                    $cogsPct = $prevCogs > 0 ? round(($cogsDiff / $prevCogs) * 100, 1) : 0;
                @endphp
                <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">COGS</p>
                    <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ format_currency($cogs) }}</div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $cogsDiff <= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $cogsPct >= 0 ? '+' : '' }}{{ $cogsPct }}%
                    </span>
                </div>

                {{-- Gross Profit --}}
                @php
                    $gpDiff = $grossProfit - $prevGrossProfit;
                    $gpPct = $prevGrossProfit != 0 ? round(($gpDiff / abs($prevGrossProfit)) * 100, 1) : 0;
                @endphp
                <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Laba Kotor</p>
                    <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ format_currency($grossProfit) }}</div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $gpDiff >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $gpPct >= 0 ? '+' : '' }}{{ $gpPct }}%
                    </span>
                </div>

                {{-- Expenses --}}
                @php
                    $expDiff = $expenses - $prevExpenses;
                    $expPct = $prevExpenses > 0 ? round(($expDiff / $prevExpenses) * 100, 1) : 0;
                @endphp
                <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Beban</p>
                    <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ format_currency($expenses) }}</div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $expDiff <= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $expPct >= 0 ? '+' : '' }}{{ $expPct }}%
                    </span>
                </div>

                {{-- Net Profit --}}
                @php
                    $npDiff = $netProfit - $prevNetProfit;
                    $npPct = $prevNetProfit != 0 ? round(($npDiff / abs($prevNetProfit)) * 100, 1) : 0;
                @endphp
                <div class="text-center p-4 rounded-xl bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800">
                    <p class="text-xs text-green-600 dark:text-green-400 mb-1 font-semibold">Laba Bersih</p>
                    <div class="text-lg font-bold text-green-700 dark:text-green-300 mb-1">{{ format_currency($netProfit) }}</div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $npDiff >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $npPct >= 0 ? '+' : '' }}{{ $npPct }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Report Results --}}
    @if ($revenue !== null)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative">
            {{-- Left: Net Profit Display --}}
            <div class="flex flex-col items-center justify-center text-center p-6 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-700 rounded-2xl border border-gray-100 dark:border-gray-600">
                <h6 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4 dark:text-gray-400 flex items-center gap-2">
                    <i class="bi bi-wallet2"></i> Laba Bersih Periode Ini
                </h6>
                <div class="mb-6 relative group">
                    <div class="absolute inset-0 bg-green-200 dark:bg-green-900 rounded-full blur-2xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
                    <span class="relative text-4xl md:text-5xl lg:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-teal-500 drop-shadow-sm">
                        {{ format_currency($netProfit) }}
                    </span>
                </div>
                
                <div class="w-full bg-white dark:bg-gray-900/50 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-600 space-y-4">
                    <div class="flex justify-between items-center border-b border-dashed border-gray-200 dark:border-gray-600 pb-3">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div> Laba Kotor
                        </span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ format_currency($grossProfit) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-orange-500"></div> Beban Operasional
                        </span>
                        <span class="text-lg font-bold text-red-500 dark:text-red-400">
                            ({{ format_currency($expenses) }})
                        </span>
                    </div>
                </div>
            </div>

            {{-- Vertical Divider (Desktop Only) --}}
            <div class="hidden md:block absolute left-1/2 top-8 bottom-8 w-px bg-gradient-to-b from-transparent via-gray-200 to-transparent dark:via-gray-600 -translate-x-1/2"></div>
            
            {{-- Horizontal Divider (Mobile Only) --}}
            <div class="block md:hidden w-full h-px bg-gray-200 dark:bg-gray-600 my-4"></div>

            {{-- Right: Financial Details --}}
            <div class="space-y-4">
                <h6 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4 dark:text-gray-400 flex items-center gap-2">
                    <i class="bi bi-list-check"></i> Rincian Perhitungan
                </h6>

                {{-- Revenue --}}
                <div class="group flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-500 hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mr-4 flex-shrink-0 dark:bg-blue-900/30 dark:text-blue-400 group-hover:scale-110 transition-transform">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-0.5 uppercase tracking-wide">Revenue</div>
                        <div class="text-lg font-bold text-blue-700 dark:text-blue-300">
                            {{ format_currency($revenue) }}
                        </div>
                    </div>
                </div>

                {{-- COGS --}}
                <div class="group flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-500 hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl mr-4 flex-shrink-0 dark:bg-red-900/30 dark:text-red-400 group-hover:scale-110 transition-transform">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-0.5 uppercase tracking-wide">COGS (HPP)</div>
                        <div class="text-lg font-bold text-red-600 dark:text-red-400">
                            ({{ format_currency($cogs) }})
                        </div>
                    </div>
                </div>

                {{-- Gross Profit --}}
                <div class="group flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-teal-300 dark:hover:border-teal-500 hover:shadow-md transition-all">
                     <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl mr-4 flex-shrink-0 dark:bg-teal-900/30 dark:text-teal-400 group-hover:scale-110 transition-transform">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-0.5 uppercase tracking-wide">Gross Profit</div>
                        <div class="text-lg font-bold text-teal-600 dark:text-teal-400">
                            {{ format_currency($grossProfit) }}
                        </div>
                    </div>
                </div>

                {{-- Expenses --}}
                <div class="group flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-500 hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl mr-4 flex-shrink-0 dark:bg-orange-900/30 dark:text-orange-400 group-hover:scale-110 transition-transform">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-0.5 uppercase tracking-wide">Beban Operasional</div>
                        <div class="text-lg font-bold text-orange-600 dark:text-orange-400">
                            ({{ format_currency($expenses) }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
