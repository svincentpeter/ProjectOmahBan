<div class="h-full flex flex-col">
    {{-- Search Bar with Loading Indicator --}}
    <div class="mb-4 relative group">
        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-ob-primary transition-colors">
            <i class="bi bi-search" wire:loading.remove wire:target="query"></i>
            <svg wire:loading wire:target="query" class="animate-spin h-4 w-4 text-ob-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <input
            wire:model.debounce.500ms="query"
            type="text"
            class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full pl-11 p-3 shadow-sm transition-all hover:border-ob-primary"
            placeholder="Cari produk bekas: nama, kode unik, size, ring...">
    </div>

    {{-- Skeleton Loading --}}
    <div wire:loading.flex wire:target="query" 
         class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 max-h-[600px] overflow-y-auto pr-1.5">
        @for($i = 0; $i < 6; $i++)
        <div class="flex flex-col bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden h-full animate-pulse">
            <div class="p-4 space-y-3">
                <div class="flex justify-between">
                    <div class="h-5 bg-slate-200 rounded w-16"></div>
                    <div class="h-5 bg-slate-200 rounded w-20"></div>
                </div>
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                <div class="h-8 bg-slate-200 rounded"></div>
                <div class="grid grid-cols-3 gap-1">
                    <div class="h-10 bg-slate-200 rounded"></div>
                    <div class="h-10 bg-slate-200 rounded"></div>
                    <div class="h-10 bg-slate-200 rounded"></div>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <div class="h-6 bg-slate-200 rounded w-1/3"></div>
                    <div class="h-8 w-8 bg-slate-200 rounded-full"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- Products Grid --}}
    <div wire:loading.remove wire:target="query" 
         class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 max-h-[600px] overflow-y-auto pr-1.5 custom-scrollbar">
        @forelse ($products as $p)
            @php
                $status = strtolower((string) $p->status);
                $isSold = $status !== 'available';
            @endphp

            <div class="group relative flex flex-col bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md hover:border-ob-primary transition-all overflow-hidden h-full {{ $isSold ? 'opacity-60' : '' }}" 
                 wire:key="second-{{ $p->id }}">
                
                {{-- Quick Add Overlay (Clickable Card) --}}
                @if(!$isSold)
                <button type="button" 
                        wire:click="addSecondToCart({{ $p->id }})"
                        wire:loading.attr="disabled"
                        wire:loading.class="cursor-wait"
                        wire:target="addSecondToCart({{ $p->id }})"
                        class="absolute inset-0 z-10 w-full h-full cursor-pointer focus:outline-none active:scale-[0.98] transition-transform"
                        aria-label="Tambah {{ $p->name }}">
                </button>
                @endif

                {{-- Sold Overlay --}}
                @if ($isSold)
                    <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center z-20 backdrop-blur-sm">
                        <span class="bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold text-sm tracking-wider shadow-lg transform -rotate-6 border-2 border-white/20">TERJUAL</span>
                    </div>
                @endif

                {{-- Loading Spinner --}}
                <div wire:loading wire:target="addSecondToCart({{ $p->id }})" 
                     class="absolute inset-0 bg-white/80 flex items-center justify-center z-30 backdrop-blur-sm">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="animate-spin h-8 w-8 text-ob-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-xs font-medium text-slate-600">Menambahkan...</span>
                    </div>
                </div>
                
                <div class="flex-1 p-4 flex flex-col">
                    {{-- Header --}}
                    <div class="flex justify-between items-start mb-2">
                        <div class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">
                            SECOND
                        </div>
                        @if (!$isSold)
                            <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Tersedia
                            </span>
                        @endif
                    </div>

                    <h6 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-ob-primary transition-colors mb-3 line-clamp-2">{{ $p->name }}</h6>

                    {{-- Specs --}}
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-xs text-slate-500 bg-slate-50 p-2 rounded-lg border border-slate-100">
                            <i class="bi bi-upc-scan text-slate-400 mr-2"></i>
                            <span class="font-mono font-bold text-slate-700">{{ $p->unique_code }}</span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-1 text-[10px] text-center">
                             <div class="bg-slate-50 rounded border border-slate-100 py-1">
                                <span class="block text-slate-400 text-[9px]">Size</span>
                                <span class="font-bold text-slate-700">{{ $p->size ?? '-' }}</span>
                             </div>
                             <div class="bg-slate-50 rounded border border-slate-100 py-1">
                                <span class="block text-slate-400 text-[9px]">Ring</span>
                                <span class="font-bold text-slate-700">{{ $p->ring ?? '-' }}</span>
                             </div>
                             <div class="bg-slate-50 rounded border border-slate-100 py-1">
                                <span class="block text-slate-400 text-[9px]">Tahun</span>
                                <span class="font-bold text-slate-700">{{ $p->product_year ?? '-' }}</span>
                             </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-auto pt-3 border-t border-slate-100 flex items-end justify-between">
                         <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">Harga</p>
                            <p class="text-base font-bold text-ob-primary">
                                {{ format_currency((int) $p->selling_price) }}
                            </p>
                        </div>
                        <div class="w-8 h-8 rounded-full {{ $isSold ? 'bg-slate-200 border-slate-300 text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-400 group-hover:bg-ob-primary group-hover:border-ob-primary group-hover:text-white' }} border flex items-center justify-center transition-all">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-inbox text-2xl text-slate-400"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Produk tidak ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1">Coba kata kunci lain atau cek ejaan Anda.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $products->links('pagination::simple-tailwind') }}
        </div>
    @endif
</div>
