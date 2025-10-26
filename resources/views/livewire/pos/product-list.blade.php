<div class="card border-0 shadow-sm mt-3 rounded-lg overflow-hidden">
    <div class="card-body p-4">
        <livewire:pos.filter :categories="$categories" :brands="$this->brands" />

        {{-- Products Grid --}}
        <div class="row position-relative mt-4" id="products-grid">
            {{-- Loading Overlay --}}
            <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center"
                style="top:0;right:0;left:0;bottom:0;background: rgba(255,255,255,0.9);z-index: 99;">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Memuat produk...</p>
                </div>
            </div>

            @forelse($products as $product)
                <div class="col-lg-4 col-md-6 col-xl-3 mb-3">
                    <div wire:click.prevent="selectProduct({{ $product->id }})"
                        class="product-card card border-0 shadow-sm h-100">

                        {{-- Product Image dengan Ukuran Tetap --}}
                        <div class="product-image-wrapper position-relative overflow-hidden"
                            style="height: 200px; background: linear-gradient(135deg, #f7fafc 0%, #e2e8f0 100%);">
                            @php
                                // Cek apakah produk punya gambar
                                $hasImage = $product->hasMedia('images');

                                // Gunakan conversion 'pos-grid' jika ada, fallback ke original
                                if ($hasImage) {
                                    $media = $product->getFirstMedia('images');
                                    $imageUrl = $media->hasGeneratedConversion('pos-grid')
                                        ? $media->getUrl('pos-grid')
                                        : $media->getUrl();
                                } else {
                                    $imageUrl = asset('images/fallback_product_image.png');
                                }
                            @endphp

                            <img src="{{ $imageUrl }}" class="product-image card-img-top h-100 w-100"
                                style="object-fit: cover; object-position: center;" alt="{{ $product->product_name }}"
                                onerror="this.onerror=null; this.src='{{ asset('images/fallback_product_image.png') }}'; this.style.objectFit='contain'; this.style.padding='20px';"
                                loading="lazy">

                            {{-- Badge untuk produk tanpa gambar --}}
                            @if (!$hasImage)
                                <div class="position-absolute d-flex align-items-center justify-content-center w-100 h-100"
                                    style="top: 0; left: 0;">
                                    <div class="text-center">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="text-muted small mb-0 mt-2" style="opacity: 0.5;">Tidak ada gambar</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body d-flex flex-column p-2">
                            {{-- Product Name --}}
                            <h6 class="product-title mb-1"
                                style="font-size: 0.875rem; font-weight: 600; line-height: 1.3; color: #2d3748;">
                                {{ Str::limit($product->product_name, 40) }}
                            </h6>

                            {{-- Product Code --}}
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-upc-scan text-muted mr-1" style="font-size: 0.75rem;"></i>
                                <small class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.02em;">
                                    {{ $product->product_code }}
                                </small>
                            </div>

                            {{-- Price & Stock Section --}}
                            <div class="mt-auto">
                                {{-- Price --}}
                                <div class="mb-2">
                                    <small class="text-muted d-block"
                                        style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em;">Harga</small>
                                    <span class="font-weight-bold" style="font-size: 1rem; color: #5a67d8;">
                                        {{ format_currency($product->product_price) }}
                                    </span>
                                </div>

                                {{-- Stock Indicator --}}
                                @php
                                    $left = (int) ($product->product_quantity ?? 0);
                                    $alert = (int) ($product->product_stock_alert ?? 3);

                                    if ($left <= $alert) {
                                        $stockColor = '#f56565';
                                    } elseif ($left <= $alert * 2) {
                                        $stockColor = '#ed8936';
                                    } else {
                                        $stockColor = '#48bb78';
                                    }
                                @endphp

                                <div class="d-flex align-items-center justify-content-between pt-2"
                                    style="border-top: 1px solid #e2e8f0;">
                                    <div class="d-flex align-items-center" style="gap: 6px;">
                                        <span
                                            style="width: 7px; height: 7px; border-radius: 50%; background-color: {{ $stockColor }}; flex-shrink: 0;"></span>
                                        <small class="text-muted" style="font-size: 0.9rem; font-weight: 500;">
                                            {{ $left }} Tersedia
                                        </small>
                                    </div>

                                    {{-- Add Button --}}
                                    <div class="add-btn-circle">
                                        <i class="bi bi-plus-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border d-flex align-items-center justify-content-center py-4">
                        <div class="text-center">
                            <i class="bi bi-inbox" style="font-size: 2.5rem; color: #cbd5e0;"></i>
                            <p class="mb-0 mt-2 text-muted">Produk tidak ditemukan</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

@push('page_css')
    <style>
        /* === Product Card === */
        .product-card {
            cursor: pointer;
            border-radius: 0.5rem !important;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(90, 103, 216, 0.15) !important;
        }

        /* === Product Image Wrapper === */
        .product-image-wrapper {
            position: relative;
            overflow: hidden;
        }

        .product-image-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 60%, rgba(0, 0, 0, 0.05) 100%);
            pointer-events: none;
            z-index: 1;
        }

        .product-image {
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        /* === Product Title === */
        .product-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.2rem;
        }

        /* === Add Button === */
        .add-btn-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5a67d8, #805ad5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(90, 103, 216, 0.3);
        }

        .product-card:hover .add-btn-circle {
            transform: scale(1.15) rotate(90deg);
            box-shadow: 0 4px 8px rgba(90, 103, 216, 0.4);
        }

        /* === Fallback Image Style === */
        .product-image[src*="fallback"] {
            object-fit: contain !important;
            padding: 20px;
            opacity: 0.6;
        }

        /* === Pagination === */
        .page-link {
            border-radius: 0.375rem !important;
            transition: all 0.2s ease;
            margin: 0 2px;
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

        /* === Responsive === */
        @media (max-width: 768px) {
            .product-card:hover {
                transform: translateY(-2px);
            }

            .product-image-wrapper {
                height: 180px !important;
            }
        }

        @media (max-width: 576px) {
            .product-image-wrapper {
                height: 160px !important;
            }
        }
    </style>
@endpush
