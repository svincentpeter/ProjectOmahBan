<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="p-1.5 bg-amber-100 text-amber-600 rounded-lg">
                    <i class="bi bi-trophy"></i>
                </div>
                Produk Terlaris
            </h3>
            <div class="flex gap-1">
                <button wire:click="setPeriod('week')" class="px-2.5 py-1 text-xs rounded-lg transition-colors {{ $period == 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Minggu
                </button>
                <button wire:click="setPeriod('month')" class="px-2.5 py-1 text-xs rounded-lg transition-colors {{ $period == 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Bulan
                </button>
                <button wire:click="setPeriod('year')" class="px-2.5 py-1 text-xs rounded-lg transition-colors {{ $period == 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Tahun
                </button>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-4">
        @if(count($products) > 0)
            <div class="space-y-3">
                @foreach($products as $product)
                    <div class="flex items-center gap-3 p-3 rounded-xl {{ $loop->first ? 'bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200' : 'bg-gray-50 hover:bg-gray-100' }} transition-colors">
                        {{-- Rank Badge --}}
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full font-bold text-sm
                            {{ $product['rank'] == 1 ? 'bg-amber-500 text-white' : ($product['rank'] == 2 ? 'bg-gray-400 text-white' : ($product['rank'] == 3 ? 'bg-amber-700 text-white' : 'bg-gray-200 text-gray-600')) }}">
                            {{ $product['rank'] }}
                        </div>
                        
                        {{-- Product Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $product['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $product['code'] }}</p>
                        </div>
                        
                        {{-- Stats --}}
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-gray-900">{{ number_format($product['qty']) }} unit</p>
                            <p class="text-xs text-green-600">{{ format_currency($product['revenue']) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="bi bi-inbox text-4xl mb-2 block text-gray-300"></i>
                <p class="text-sm">Belum ada data penjualan</p>
            </div>
        @endif
    </div>
</div>
