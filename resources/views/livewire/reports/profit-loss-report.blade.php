<div wire:key="pl-root">
    {{-- Filter Card (Styled to match layouts.filter-card) --}}
    <div class="mb-6 p-5 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400">
                    <i class="bi bi-funnel-fill"></i>
                </div>
                Filter Laporan Laba Rugi
            </h3>
        </div>
        
        <form wire:submit.prevent="generateReport">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-calendar-event mr-1.5 text-blue-600"></i> Tanggal Mulai
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-calendar-range text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <input wire:model="startDate" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <div class="md:col-span-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-calendar-event mr-1.5 text-blue-600"></i> Tanggal Akhir
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-calendar-range text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <input wire:model="endDate" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-all shadow-md hover:shadow-lg flex items-center justify-center">
                        <span wire:loading.remove wire:target="generateReport" class="flex items-center">
                            <i class="bi bi-search mr-2"></i> Tampilkan
                        </span>
                        <div wire:loading wire:target="generateReport" class="flex items-center">
                             <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </div>
                    </button>
                </div>
            </div>

            {{-- Loading Indicator --}}
            <div wire:loading.flex wire:target="generateReport" class="flex items-center mt-4 p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-medium">Sedang menganalisa data keuangan...</span>
            </div>
        </form>
    </div>

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
