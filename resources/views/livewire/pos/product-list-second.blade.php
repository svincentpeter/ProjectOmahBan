<div>
    <div class="form-group mb-2">
        <input wire:model.debounce.400ms="query"
               type="text" class="form-control"
               placeholder="Cari produk bekas berdasarkan nama atau kode unik…">
    </div>

    @push('page_css')
    <style>
        .second-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.85rem}
        @media (max-width:1200px){.second-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media (max-width:768px){.second-grid{grid-template-columns:1fr}}
        .second-card{border:1px solid #e9ecef;border-radius:.5rem;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:transform .12s ease,box-shadow .12s ease;min-height:118px}
        .second-card:hover{transform:translateY(-2px);box-shadow:0 8px 16px rgba(17,24,39,.08)}
        .second-title{font-weight:600;font-size:.98rem;line-height:1.25rem;margin:0;color:#1f2937}
        .second-price{font-weight:700;font-size:.95rem;white-space:nowrap;color:#4f46e5}
        .second-meta{font-size:.78rem;color:#6b7280}
        .badge-status{font-size:.72rem;padding:.25rem .5rem;border-radius:.5rem}
        .badge-ready{background:#e7f6ee;color:#0f9255;border:1px solid #b8ead1}
        .badge-sold{background:#f0f1f2;color:#6b7280;border:1px solid #e5e7eb}
        .btn-second{padding:.375rem .6rem;font-size:.82rem;border-radius:.45rem}
        .second-empty{border:1px dashed #d1d5db;border-radius:.75rem;padding:1.25rem;color:#6b7280;background:#fafafa;text-align:center}
    </style>
    @endpush

    @if(isset($products) && $products->count())
        <div class="second-grid">
            @foreach($products as $second)
                @php
                    $status   = $second->status ?? 'available';
                    $isReady  = in_array(strtolower($status), ['ready','available','tersedia']);
                    $badgeCls = $isReady ? 'badge-ready' : 'badge-sold';
                    $badgeTxt = $isReady ? 'Ready' : 'Terjual';
                    $price = function_exists('format_currency')
                        ? format_currency($second->selling_price ?? $second->price ?? 0)
                        : 'Rp' . number_format($second->selling_price ?? $second->price ?? 0, 0, ',', '.');
                @endphp

                <div class="second-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="second-title">
                            {{ $second->name }}
                        </h6>
                        <div class="second-price">{{ $price }}</div>
                    </div>
                    <div class="second-meta mb-2">
                        {{ $second->unique_code ?? $second->sku ?? '-' }}
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge badge-status {{ $badgeCls }}">{{ $badgeTxt }}</span>
                        <button class="btn btn-primary btn-second"
                                wire:click="addSecondToCart({{ $second->id }})"
                                @if(!$isReady) disabled @endif>
                            <i class="bi bi-cart-plus"></i> Tambah
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        @if(method_exists($products,'links'))
            <div class="mt-3">{{ $products->links() }}</div>
        @endif
    @else
        <div class="second-empty">Belum ada hasil produk bekas untuk kata kunci ini.</div>
    @endif
</div>
<script>
window.addEventListener('notify', (e) => {
  const d = e.detail || {};
  // Contoh pakai Toastr jika ada
  if (window.toastr) {
    const msg = d.message || 'OK';
    const type = d.type || 'info';
    toastr[type](msg);
  } else {
    alert((d.type ? d.type.toUpperCase()+': ' : '') + (d.message || 'OK'));
  }
});

window.addEventListener('cartUpdated', () => {
  // kalau ada komponen cart, bisa trigger refresh via Livewire event, dsb.
});
</script>

