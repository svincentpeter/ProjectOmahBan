{{--
    Product Counting Card (Tailwind Version)
    
    Required: $item (StockOpnameItem model)
--}}

<div class="product-card bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden {{ $item->actual_qty !== null ? 'counted' : '' }} {{ $item->variance_type !== 'pending' ? 'variance-' . $item->variance_type : '' }}"
     data-item-id="{{ $item->id }}"
     data-product-name="{{ $item->product->product_name }}"
     data-product-code="{{ $item->product->product_code }}"
     data-system-qty="{{ $item->system_qty }}"
     data-actual-qty="{{ $item->actual_qty }}"
     data-category-id="{{ $item->product->category_id }}">
    
    <div class="product-card-header p-4 border-b border-zinc-100 relative">
        <h6 class="font-bold text-black truncate" title="{{ $item->product->product_name }}">
            {{ $item->product->product_name }}
        </h6>
        <p class="text-xs text-zinc-500 mt-0.5">{{ $item->product->product_code }}</p>

        @if($item->actual_qty !== null)
            <i class="bi bi-check-circle-fill text-emerald-500 counted-icon absolute top-3 right-3 text-xl"></i>
        @endif
    </div>

    <div class="p-4">
        {{-- Category --}}
        <div class="mb-3">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600">
                <i class="bi bi-tag me-1"></i> {{ $item->product->category->category_name }}
            </span>
        </div>

        {{-- System Qty --}}
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-black">Stok Sistem:</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                {{ number_format($item->system_qty) }} unit
            </span>
        </div>

        {{-- Actual Qty --}}
        <div class="flex justify-between items-center mb-3">
            <span class="text-sm text-black">Hasil Hitung:</span>
            @if($item->actual_qty !== null)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 actual-qty-display">
                    {{ number_format($item->actual_qty) }} unit
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-500">
                    Belum dihitung
                </span>
            @endif
        </div>

        {{-- Variance --}}
        <div class="pt-3 border-t border-zinc-100">
            <div class="text-center variance-display">
                @if($item->variance_type === 'match')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Cocok ✓</span>
                @elseif($item->variance_type === 'surplus')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">+{{ $item->variance_qty }} (Lebih)</span>
                @elseif($item->variance_type === 'shortage')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $item->variance_qty }} (Kurang)</span>
                @else
                    <span class="text-zinc-400 text-sm flex items-center justify-center gap-1">
                        <i class="bi bi-calculator"></i> Klik untuk hitung
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Card Footer: Last Counted --}}
    @if($item->counted_at)
        <div class="px-4 py-2 bg-zinc-50 border-t border-zinc-100">
            <p class="text-xs text-zinc-500">
                <i class="bi bi-clock me-1"></i> {{ $item->counted_at->diffForHumans() }}
            </p>
        </div>
    @endif
</div>
