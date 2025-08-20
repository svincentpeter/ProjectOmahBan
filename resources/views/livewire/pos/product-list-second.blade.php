<div>
    <div class="form-group mb-2">
        <input
            wire:model.debounce.400ms="query"
            type="text"
            class="form-control"
            placeholder="Cari produk bekas: nama / kode unik / size / ring / tahun…">
    </div>

    @once
        @push('page_css')
        <style>
            .second-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.85rem}
            @media (max-width:1200px){.second-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
            @media (max-width:768px){.second-grid{grid-template-columns:1fr}}
            .second-card{position:relative;border:1px solid #e9ecef;border-radius:.5rem;padding:.75rem;transition:transform .12s ease,box-shadow .12s ease;min-height:124px;background:#fff}
            .second-card:hover{transform:translateY(-2px);box-shadow:0 8px 16px rgba(17,24,39,.08)}
            .second-title{font-weight:600;font-size:.98rem;line-height:1.25rem;margin:0;color:#1f2937}
            .second-meta{font-size:.8rem;color:#6b7280}
            .second-price{font-weight:700;font-size:.95rem;white-space:nowrap;color:#4f46e5}
            .second-actions{display:flex;gap:.5rem;align-items:center;justify-content:flex-end}
            .badge-status{font-size:.7rem}
            .sold-overlay{position:absolute;inset:0;background:rgba(0,0,0,.35);display:flex;align-items:center;justify-content:center;border-radius:.5rem}
            .sold-overlay span{background:#ef4444;color:#fff;padding:.25rem .5rem;border-radius:.25rem;font-weight:700;letter-spacing:.05em}
        </style>
        @endpush
    @endonce

    <div class="second-grid">
        @forelse ($products as $p)
            @php
                $status = strtolower((string) $p->status);
                $isSold = $status !== 'available';
            @endphp

            <div class="second-card" wire:key="second-{{ $p->id }}">
                @if ($isSold)
                    <div class="sold-overlay"><span>SOLD</span></div>
                @endif

                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="second-title">{{ $p->name }}</h6>
                    <span class="badge badge-status {{ $isSold ? 'badge-secondary' : 'badge-success' }}">
                        {{ $isSold ? 'Terjual' : 'Tersedia' }}
                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="second-meta">
                            Kode: <strong>{{ $p->unique_code }}</strong>
                        </div>
                        <div class="second-meta">
                            Size/Ring/Tahun:
                            <strong>{{ $p->size ?? '-' }}</strong> /
                            <strong>{{ $p->ring ?? '-' }}</strong> /
                            <strong>{{ $p->product_year ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="second-price">{{ format_currency((int) $p->selling_price) }}</div>
                    </div>
                </div>

                <div class="second-actions mt-2">
                    <button
                        class="btn btn-sm btn-outline-primary"
                        wire:click="addSecondToCart({{ $p->id }})"
                        @if($isSold) disabled title="Item sudah terjual" @endif>
                        <i class="bi bi-cart-plus"></i> Tambah
                    </button>
                </div>
            </div>
        @empty
            <div class="text-muted">Tidak ada data.</div>
        @endforelse
    </div>

    <div class="mt-3">
        {{-- Jika pakai Bootstrap 5 pagination, aktifkan view berikut --}}
        {{ $products->links('pagination::bootstrap-5') }}
        {{-- Atau cukup: {{ $products->links() }} --}}
    </div>

    @once
        
    @endonce
</div>
