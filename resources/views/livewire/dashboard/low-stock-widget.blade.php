<div>
    @if($count > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-2 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg mr-3 shadow-lg shadow-orange-500/30">
                    <i class="bi bi-exclamation-triangle text-white text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 dark:text-white text-sm">Stok Rendah</h4>
                    <p class="text-xs text-gray-500">{{ $count }} produk perlu restock</p>
                </div>
            </div>
            @if($count > 5)
            <button wire:click="toggleShowAll" class="text-xs text-blue-600 hover:underline">
                {{ $showAll ? 'Tampilkan Sedikit' : 'Lihat Semua' }}
            </button>
            @endif
        </div>

        {{-- Product List --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-96 overflow-y-auto">
            @foreach($products as $product)
            <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                            {{ $product['name'] }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $product['code'] }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="flex items-center gap-2">
                            @if($product['critical'])
                            <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">
                                <i class="bi bi-x-circle mr-1"></i> Habis
                            </span>
                            @else
                            <span class="text-sm font-bold {{ $product['percentage'] < 50 ? 'text-red-600' : 'text-orange-600' }}">
                                {{ $product['qty'] }} pcs
                            </span>
                            @endif
                        </div>
                        <div class="mt-1 w-20">
                            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $product['percentage'] < 30 ? 'bg-red-500' : ($product['percentage'] < 70 ? 'bg-orange-500' : 'bg-yellow-500') }}" 
                                     style="width: {{ min(100, max(5, $product['percentage'])) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" 
               class="text-sm text-blue-600 hover:underline flex items-center justify-center">
                <i class="bi bi-box-seam mr-1"></i>
                Kelola Stok Produk
            </a>
        </div>
    </div>
    @else
    {{-- Empty State --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 p-6">
        <div class="text-center">
            <div class="p-3 bg-green-100 rounded-full inline-flex mb-3">
                <i class="bi bi-check-circle text-green-600 text-2xl"></i>
            </div>
            <h4 class="font-bold text-gray-800 dark:text-white text-sm">Stok Aman</h4>
            <p class="text-xs text-gray-500 mt-1">Semua produk memiliki stok cukup</p>
        </div>
    </div>
    @endif
</div>
