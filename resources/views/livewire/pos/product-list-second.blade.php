<div class="card border-0 shadow-sm rounded-lg overflow-hidden">
    <div class="card-body p-4">
        {{-- Search Bar --}}
        <div class="mb-4">
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280;"></i>
                <input
                    wire:model.debounce.400ms="query"
                    type="text"
                    class="form-control pl-5"
                    style="border-radius: 0.5rem; border: 1px solid #e2e8f0; padding: 0.625rem 1rem 0.625rem 2.5rem;"
                    placeholder="Cari produk bekas: nama, kode unik, size, ring, tahun…">
            </div>
        </div>

        {{-- Loading Overlay --}}
        <div wire:loading.flex 
             class="position-relative justify-content-center align-items-center py-5"
             style="background: rgba(255,255,255,0.9);">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="text-muted small mt-2 mb-0">Memuat produk...</p>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="second-grid" wire:loading.remove>
            @forelse ($products as $p)
                @php
                    $status = strtolower((string) $p->status);
                    $isSold = $status !== 'available';
                @endphp

                <div class="second-card" wire:key="second-{{ $p->id }}">
                    {{-- Sold Overlay --}}
                    @if ($isSold)
                        <div class="sold-overlay">
                            <span>TERJUAL</span>
                        </div>
                    @endif

                    {{-- Card Header --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="second-title mb-0">{{ $p->name }}</h6>
                        @if (!$isSold)
                            <span class="badge badge-success" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="bi bi-check-circle-fill" style="font-size: 0.7rem;"></i> Tersedia
                            </span>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-upc-scan text-muted mr-1" style="font-size: 0.75rem;"></i>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Kode: <strong class="text-dark">{{ $p->unique_code }}</strong>
                            </small>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle text-muted mr-1" style="font-size: 0.75rem;"></i>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <strong class="text-dark">{{ $p->size ?? '-' }}</strong> / 
                                <strong class="text-dark">{{ $p->ring ?? '-' }}</strong> / 
                                <strong class="text-dark">{{ $p->product_year ?? '-' }}</strong>
                            </small>
                        </div>
                    </div>

                    {{-- Price & Action --}}
                    <div class="d-flex justify-content-between align-items-center pt-2" 
                         style="border-top: 1px solid #e2e8f0;">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em;">Harga</small>
                            <span class="font-weight-bold" style="font-size: 1rem; color: #5a67d8;">
                                {{ format_currency((int) $p->selling_price) }}
                            </span>
                        </div>
                        
                        <button
                            class="btn btn-sm btn-primary d-flex align-items-center"
                            style="border-radius: 0.375rem; padding: 0.375rem 0.75rem; gap: 0.375rem;"
                            wire:click="addSecondToCart({{ $p->id }})"
                            @if($isSold) disabled @endif>
                            <i class="bi bi-cart-plus" style="font-size: 0.875rem;"></i>
                            <span style="font-size: 0.8rem;">Tambah</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 2.5rem; color: #cbd5e0;"></i>
                    <p class="mb-0 mt-2 text-muted">Produk tidak ditemukan</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@push('page_css')
<style>
/* === Second Product Grid === */
.second-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
}

@media (max-width: 1200px) {
    .second-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .second-grid {
        grid-template-columns: 1fr;
    }
}

/* === Second Product Card === */
.second-card {
    position: relative;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 0.875rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    min-height: 140px;
    background: #fff;
}

.second-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
}

/* === Card Title === */
.second-title {
    font-weight: 600;
    font-size: 0.95rem;
    line-height: 1.3;
    color: #1f2937;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* === Sold Overlay === */
.sold-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    z-index: 10;
}

.sold-overlay span {
    background: #ef4444;
    color: #fff;
    padding: 0.375rem 0.875rem;
    border-radius: 0.375rem;
    font-weight: 700;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

/* === Search Input === */
.form-control:focus {
    border-color: #5a67d8;
    box-shadow: 0 0 0 3px rgba(90, 103, 216, 0.1);
}

/* === Button Disabled State === */
.btn-primary:disabled {
    background: #cbd5e0;
    border-color: #cbd5e0;
    cursor: not-allowed;
    opacity: 0.6;
}

/* === Pagination === */
.page-link {
    border-radius: 0.375rem !important;
    transition: all 0.2s ease;
}

.page-link:hover {
    background: #5a67d8;
    border-color: #5a67d8;
    color: white;
}

.page-item.active .page-link {
    background: #5a67d8;
    border-color: #5a67d8;
}

/* === Empty State === */
.col-span-full {
    grid-column: 1 / -1;
}
</style>
@endpush
