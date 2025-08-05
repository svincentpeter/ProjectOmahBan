<div>
    <div class="form-group">
        <input wire:model.debounce.300ms="query" type="text" class="form-control" placeholder="Cari produk bekas berdasarkan nama atau kode unik...">
    </div>

    <div class="row">
        @forelse($products as $product)
            <div class="col-lg-4 col-md-6" style="cursor: pointer;" wire:click="addToCart({{ $product->id }})">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                             <h5 class="card-title mb-0">{{ $product->name }}</h5>
                             <h6>{{ format_currency($product->selling_price) }}</h6>
                        </div>
                        <p class="card-text text-muted">{{ $product->unique_code }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>Produk bekas tidak ditemukan.</p>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>