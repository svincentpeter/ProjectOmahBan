<div>
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
            <livewire:pos.filter :categories="$categories" />

            <div class="row position-relative">
                <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center"
                     style="top:0;right:0;left:0;bottom:0;background-color: rgba(255,255,255,0.5);z-index: 99;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>

                @forelse($products as $product)
                    <div wire:click.prevent="selectProduct({{ $product->id }})"
                         class="col-lg-4 col-md-6 col-xl-3" style="cursor: pointer;">
                        <div class="card border-0 shadow h-100">
                            <img height="200"
                                 src="{{ $product->getFirstMediaUrl('images') }}"
                                 class="card-img-top"
                                 alt="{{ $product->product_name }}">

                            <div class="card-body">
                                <h6 class="card-title mb-1" style="font-size: 13px;">
                                    {{ $product->product_name }}
                                </h6>

                                {{-- Baris info: kode produk (kiri) & badge stok (kanan) --}}
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted">{{ $product->product_code }}</small>

                                    @php
                                        $left   = (int) ($product->product_quantity ?? 0);
                                        $alert  = (int) ($product->product_stock_alert ?? 3);
                                        $badgeClass = $left <= $alert
                                            ? 'badge-danger'
                                            : ($left <= ($alert * 2) ? 'badge-warning' : 'badge-success');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        Stok: {{ $left }}
                                    </span>
                                </div>

                                <p class="card-text font-weight-bold mt-2 mb-0">
                                    {{ format_currency($product->product_price) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning mb-0">
                            Products Not Found...
                        </div>
                    </div>
                @endforelse
            </div>

            <div @class(['mt-3' => $products->hasPages()])>
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
