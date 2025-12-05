{{--
    Product Counting Card
    
    Required: $item (StockOpnameItem model)
--}}

<div class="col-md-4 col-lg-3 mb-4">
    <div class="card product-card {{ $item->actual_qty !== null ? 'counted' : '' }} {{ $item->variance_type !== 'pending' ? 'variance-' . $item->variance_type : '' }}"
         data-item-id="{{ $item->id }}"
         data-product-name="{{ $item->product->product_name }}"
         data-product-code="{{ $item->product->product_code }}"
         data-system-qty="{{ $item->system_qty }}"
         data-actual-qty="{{ $item->actual_qty }}"
         data-category-id="{{ $item->product->category_id }}">
        
        <div class="card-header bg-white border-bottom position-relative">
            <h6 class="mb-0 font-weight-bold text-truncate" title="{{ $item->product->product_name }}">
                {{ $item->product->product_name }}
            </h6>
            <small class="text-muted">{{ $item->product->product_code }}</small>

            @if($item->actual_qty !== null)
                <i class="bi bi-check-circle-fill text-success counted-icon" 
                   style="font-size: 1.5rem; position: absolute; top: 10px; right: 10px;"></i>
            @endif
        </div>

        <div class="card-body">
            {{-- Category --}}
            <div class="mb-2">
                <small class="text-muted">
                    <i class="bi bi-tag"></i> {{ $item->product->category->category_name }}
                </small>
            </div>

            {{-- System Qty --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Stok Sistem:</span>
                <span class="badge badge-primary badge-pill">
                    {{ number_format($item->system_qty) }} unit
                </span>
            </div>

            {{-- Actual Qty --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Hasil Hitung:</span>
                @if($item->actual_qty !== null)
                    <span class="badge badge-success badge-pill actual-qty-display">
                        {{ number_format($item->actual_qty) }} unit
                    </span>
                @else
                    <span class="badge badge-secondary badge-pill">
                        Belum dihitung
                    </span>
                @endif
            </div>

            {{-- Variance --}}
            <div class="mt-3 pt-2 border-top">
                <div class="text-center variance-display">
                    @if($item->variance_type === 'match')
                        <span class="badge badge-success">Cocok ✓</span>
                    @elseif($item->variance_type === 'surplus')
                        <span class="badge badge-info">+{{ $item->variance_qty }} (Lebih)</span>
                    @elseif($item->variance_type === 'shortage')
                        <span class="badge badge-danger">{{ $item->variance_qty }} (Kurang)</span>
                    @else
                        <span class="text-muted small">
                            <i class="bi bi-calculator"></i> Klik untuk hitung
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card Footer: Last Counted --}}
        @if($item->counted_at)
            <div class="card-footer bg-light border-top-0">
                <small class="text-muted">
                    <i class="bi bi-clock"></i> {{ $item->counted_at->diffForHumans() }}
                </small>
            </div>
        @endif
    </div>
</div>
