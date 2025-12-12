<div wire:key="sales-report-root">
    {{-- Filter Card --}}
    <div class="mb-6 p-5 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400">
                    <i class="bi bi-funnel-fill"></i>
                </div>
                Filter Laporan Penjualan
            </h3>
        </div>
        
        <form wire:submit.prevent="generateReport">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Date Range --}}
                <div>
                     <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-calendar-event mr-1.5 text-blue-600"></i> Tanggal Mulai
                    </label>
                    <input wire:model="start_date" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                </div>
                
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-calendar-event mr-1.5 text-blue-600"></i> Tanggal Akhir
                    </label>
                    <input wire:model="end_date" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                </div>

                {{-- Customer --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-people mr-1.5 text-blue-600"></i> Pelanggan
                    </label>
                    <select wire:model="customer_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                        <option value="">Semua Pelanggan</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sale Status --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-info-circle mr-1.5 text-blue-600"></i> Status Penjualan
                    </label>
                    <select wire:model="sale_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="Completed">Selesai (Completed)</option>
                        <option value="Pending">Menunggu (Pending)</option>
                        <option value="Ordered">Dipesan (Ordered)</option>
                    </select>
                </div>

                {{-- Payment Status --}}
                 <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-credit-card-2-back mr-1.5 text-blue-600"></i> Status Pembayaran
                    </label>
                    <select wire:model="payment_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="Paid">Lunas (Paid)</option>
                        <option value="Partial">Sebagian (Partial)</option>
                        <option value="Due">Belum Lunas (Due)</option>
                    </select>
                </div>
                
                 <div class="flex items-end">
                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-all shadow-md hover:shadow-lg flex items-center justify-center h-[42px]">
                        <span wire:loading.remove wire:target="generateReport, start_date, end_date, customer_id, sale_status, payment_status" class="flex items-center">
                            <i class="bi bi-search mr-2"></i> Tampilkan
                        </span>
                         <div wire:loading wire:target="generateReport, start_date, end_date, customer_id, sale_status, payment_status" class="flex items-center">
                             <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </div>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Omset --}}
        <div class="relative bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-wallet2 text-9xl"></i>
            </div>
             <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                     <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-inner">
                        <i class="bi bi-cash-stack text-2xl"></i>
                    </div>
                </div>
                <div>
                   <p class="text-blue-100 text-sm font-medium mb-1 tracking-wide">Total Omset</p>
                   <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($totalOmset) }}</h3>
                   <p class="text-xs text-blue-200 mt-1 opacity-80">{{ $totalCount }} Transaksi</p>
               </div>
            </div>
        </div>

         {{-- Total Paid --}}
         <div class="relative bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
             <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-check-circle text-9xl"></i>
            </div>
            <div class="relative z-10">
                 <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-inner">
                        <i class="bi bi-piggy-bank text-2xl"></i>
                    </div>
                 </div>
                 <div>
                   <p class="text-green-100 text-sm font-medium mb-1 tracking-wide">Total Diterima (Lunas)</p>
                   <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($totalPaid) }}</h3>
                   <p class="text-xs text-green-200 mt-1 opacity-80">Pembayaran masuk</p>
               </div>
            </div>
        </div>

        {{-- Total Due --}}
        <div class="relative bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
             <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-exclamation-circle text-9xl"></i>
            </div>
            <div class="relative z-10">
                 <div class="flex items-center justify-between mb-4">
                     <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-inner">
                        <i class="bi bi-card-checklist text-2xl"></i>
                    </div>
                </div>
                 <div>
                   <p class="text-red-100 text-sm font-medium mb-1 tracking-wide">Total Piutang (Due)</p>
                   <h3 class="text-2xl font-bold tracking-tight">{{ format_currency($totalDue) }}</h3>
                   <p class="text-xs text-red-200 mt-1 opacity-80">Belum dibayar</p>
               </div>
            </div>
        </div>
        
         {{-- Transactions Count --}}
         <div class="relative bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
             <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                <i class="bi bi-receipt text-9xl"></i>
            </div>
            <div class="relative z-10">
                 <div class="flex items-center justify-between mb-4">
                     <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-inner">
                        <i class="bi bi-receipt-cutoff text-2xl"></i>
                    </div>
                </div>
                 <div>
                   <p class="text-purple-100 text-sm font-medium mb-1 tracking-wide">Jumlah Transaksi</p>
                   <h3 class="text-3xl font-bold tracking-tight">{{ number_format($totalCount) }}</h3>
                   <p class="text-xs text-purple-200 mt-1 opacity-80">Dalam periode ini</p>
               </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
         <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="bi bi-table text-blue-600"></i> Detail Data Penjualan
            </h3>
            <div class="text-sm text-gray-500">
                Menampilkan {{ $sales->firstItem() ?? 0 }} - {{ $sales->lastItem() ?? 0 }} dari {{ $sales->total() }} data
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Tanggal</th>
                        <th scope="col" class="px-6 py-4 font-bold">Ref No</th>
                        <th scope="col" class="px-6 py-4 font-bold">Pelanggan</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Status</th>
                         <th scope="col" class="px-6 py-4 font-bold text-center">Pembayaran</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Total</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Dibayar</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Hutang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($sales as $sale)
                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                             <a href="{{ route('sales.show', $sale->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-mono">
                                {{ $sale->reference }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            {{ $sale->customer_name }}
                        </td>
                         <td class="px-6 py-4 text-center">
                            @if ($sale->status == 'Completed')
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    {{ $sale->status }}
                                </span>
                            @elseif ($sale->status == 'Pending')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">
                                    {{ $sale->status }}
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                    {{ $sale->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                             @if ($sale->payment_status == 'Paid')
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    {{ $sale->payment_status }}
                                </span>
                            @elseif ($sale->payment_status == 'Partial')
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    {{ $sale->payment_status }}
                                </span>
                            @elseif ($sale->payment_status == 'Due')
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                    {{ $sale->payment_status }}
                                </span>
                             @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                    {{ $sale->payment_status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">
                            {{ format_currency($sale->total_amount) }}
                        </td>
                        <td class="px-6 py-4 text-right text-green-600 dark:text-green-400">
                            {{ format_currency($sale->paid_amount) }}
                        </td>
                        <td class="px-6 py-4 text-right text-red-600 dark:text-red-400">
                            {{ format_currency($sale->due_amount) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 dark:bg-gray-700">
                                    <i class="bi bi-search text-3xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-900 dark:text-white mb-1">Tidak ada data penjualan.</p>
                                <p class="text-sm text-gray-500">Sesuaikan filter untuk melihat data.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $sales->links() }} 
        </div>
    </div>
</div>
