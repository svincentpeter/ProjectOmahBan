<div>
    {{-- SINGLE ROOT ELEMENT FOR LIVEWIRE --}}
    <div class="space-y-4">
        
        {{-- Search Bar --}}
        <div class="sticky top-0 bg-white/80 backdrop-blur-md pb-2 pt-1 mb-2 border-b border-transparent focus-within:border-indigo-100 transition-all">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                    <i class="bi bi-search" wire:loading.remove wire:target="search"></i>
                    <svg wire:loading wire:target="search" class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-11 p-3 shadow-sm transition-all hover:border-indigo-300 hover:bg-white focus:bg-white"
                    placeholder="Ketuk '/ ' untuk cari produk baru...">
            </div>
            
            {{-- Search Keyboard Shortcut --}}
            <script>
                document.addEventListener('keydown', function(e) {
                    if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        const searchInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.300ms="search"]');
                        if (searchInput) searchInput.focus();
                    }
                });
            </script>
        </div>
        
        {{-- PRODUCT GRID --}}
        @if($products->count() > 0)
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 max-h-[600px] overflow-y-auto pr-1.5 custom-scrollbar pb-24">
                
                @foreach($products as $product)
                    @php
                        // Data Variables
                        $stock = (int) ($product->product_quantity ?? 0);
                        $price = $product->product_price ?? 0;
                        $hasImage = $product->hasMedia('images');
                        $imageUrl = $hasImage 
                            ? $product->getFirstMediaUrl('images', 'pos-grid') 
                            : asset('images/fallback_product_image.png');
                        
                        // Fallback URL check (in case conversion doesn't exist)
                        if ($hasImage && empty($imageUrl)) {
                            $imageUrl = $product->getFirstMediaUrl('images');
                        }
                        
                        $brandName = $product->brand?->name ?? $product->brand?->brand_name ?? 'No Brand';
                        $productName = $product->product_name;
                        $productCode = $product->product_code;
                        
                        // Stock Logic
                        $alert = (int) ($product->product_stock_alert ?? 3);
                        $isOutOfStock = $stock <= 0;
                        $isLowStock = $stock <= $alert && !$isOutOfStock;
                        
                        // Styling based on Stock
                        if ($isOutOfStock) {
                            $stockBadgeClass = 'bg-red-50 text-red-600 border-red-100';
                            $stockDotClass = 'bg-red-500';
                            $stockText = 'Habis';
                        } elseif ($isLowStock) {
                            $stockBadgeClass = 'bg-amber-50 text-amber-600 border-amber-100';
                            $stockDotClass = 'bg-amber-500';
                            $stockText = 'Sisa ' . $stock;
                        } else {
                            $stockBadgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                            $stockDotClass = 'bg-emerald-500';
                            $stockText = $stock . ' Unit';
                        }
                        
                        // JSON Data for Detail Modal & Add Cart
                        $productData = json_encode([
                            'id' => $product->id,
                            'name' => htmlspecialchars($productName), // Create safe string
                            'price' => $price,
                            'formatted_price' => format_currency($price),
                            'stock' => $stock,
                            'code' => $productCode,
                            'image' => $imageUrl,
                            'brand' => $brandName,
                            'description' => htmlspecialchars($product->product_note ?? 'Tidak ada deskripsi'),
                            'category' => $product->category?->category_name ?? '-',
                            'location' => $product->location ?? '-'
                        ]);
                    @endphp
                    
                    {{-- PRODUCT CARD --}}
                    <article class="group relative flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm transition-all duration-300 hover:shadow-lg hover:border-indigo-200 hover:-translate-y-1 h-full overflow-hidden" 
                             wire:key="prod-{{ $product->id }}">
                        
                        {{-- 1. Image Area --}}
                        <div class="relative h-44 bg-slate-50 overflow-hidden flex items-center justify-center p-4">
                            {{-- Product Image --}}
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $productName }}"
                                 loading="lazy"
                                 class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-110 drop-shadow-sm mix-blend-multiply"
                                 onerror="this.src='{{ asset('images/fallback_product_image.png') }}'">
                            
                            {{-- Brand Badge (Left Top) --}}
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg bg-white/90 backdrop-blur-sm border border-slate-200 text-[10px] font-bold text-slate-600 shadow-sm">
                                    {{ $brandName }}
                                </span>
                            </div>
                            
                            {{-- Stock Badge (Right Top) --}}
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg border text-[10px] font-bold shadow-sm {{ $stockBadgeClass }} bg-white/90 backdrop-blur-sm">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $stockDotClass }} animate-pulse"></span>
                                    {{ $stockText }}
                                </span>
                            </div>

                            {{-- Overlay Actions (Visible on Hover) --}}
                            <div class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-2 backdrop-blur-[1px]">
                                <button type="button" 
                                        onclick='openProductDetail(@json($productData))'
                                        class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 bg-white text-slate-700 hover:text-indigo-600 hover:bg-slate-50 rounded-full h-10 w-10 flex items-center justify-center shadow-lg border border-slate-100 z-10"
                                        title="Lihat Detail">
                                    <i class="bi bi-eye text-lg"></i>
                                </button>
                            </div>
                        </div>
                        
                        {{-- 2. Content Area --}}
                        <div class="flex flex-col flex-1 p-4">
                            
                            {{-- Title & Code --}}
                            <div class="mb-3 min-h-[4rem]">
                                <h3 class="text-sm font-bold text-slate-800 leading-snug line-clamp-2 mb-1 group-hover:text-indigo-700 transition-colors" title="{{ $productName }}">
                                    {{ $productName }}
                                </h3>
                                <p class="text-xs text-slate-500 flex items-center gap-1">
                                    <i class="bi bi-upc-scan"></i>
                                    {{ $productCode }}
                                </p>
                            </div>
                            
                            {{-- Price Section --}}
                            <div class="mt-auto pt-3 border-t border-slate-50 flex items-end justify-between">
                                <div>
                                    <span class="block text-[10px] text-slate-400 font-medium uppercase tracking-wider">Harga Satuan</span>
                                    <span class="text-lg font-bold text-indigo-600">
                                        {{ format_currency($price) }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- 3. Action Footer (Qty & Add) --}}
                            <div class="mt-3 flex items-center gap-2">
                                {{-- Qty Control (Optional for Cashier speed) --}}
                                <div class="flex items-center rounded-xl bg-slate-50 border border-slate-200 px-1 py-1 w-24 shadow-inner">
                                    <button type="button" 
                                            onclick="adjustCardQty(this, -1)" 
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-red-500 hover:shadow-sm transition-all focus:outline-none"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $stock }}" 
                                           class="w-full text-center bg-transparent border-none p-0 text-sm font-bold text-slate-700 focus:ring-0 item-qty-input pointer-events-none"
                                           readonly
                                           data-max="{{ $stock }}">
                                    <button type="button" 
                                            onclick="adjustCardQty(this, 1)" 
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-emerald-500 hover:shadow-sm transition-all focus:outline-none"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>

                                {{-- Add Button --}}
                                <button type="button"
                                        onclick='addToCartFromCard(this, @json($productData))'
                                        {{ $isOutOfStock ? 'disabled' : '' }}
                                        class="flex-1 overflow-hidden relative inline-flex items-center justify-center h-10 px-4 rounded-xl text-sm font-bold text-white shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 {{ $isOutOfStock ? 'bg-slate-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 hover:shadow-indigo-200 active:scale-95' }}">
                                    @if($isOutOfStock)
                                        <span class="text-slate-500">Habis</span>
                                    @else
                                        <div class="flex items-center gap-1.5">
                                            <i class="bi bi-cart-plus text-lg"></i>
                                            <span>Tambah</span>
                                        </div>
                                    @endif
                                    
                                    {{-- Loading Spinner overlay handled by Livewire or generic JS --}}
                                </button>
                            </div>

                        </div>
                    </article>
                    
                @endforeach
                
            </div>
            
            {{-- Pagination with elegant style --}}
            @if($products->hasPages())
                <div class="mt-6 flex justify-center pb-8">
                    <div class="bg-white rounded-full shadow-sm border border-slate-200 px-4 py-2">
                        {{ $products->links('pagination::simple-tailwind') }}
                    </div>
                </div>
            @endif
            
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200 m-4">
                <div class="bg-indigo-50 p-6 rounded-full mb-4 animate-bounce-slow">
                    <i class="bi bi-search text-4xl text-indigo-300"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Produk Tidak Ditemukan</h3>
                <p class="text-slate-500 max-w-md mx-auto">
                    Coba cari dengan kata kunci lain atau ubah filter kategori.
                </p>
            </div>
        @endif

    </div>

    {{-- PRODUCT DETAIL MODAL --}}
    <div id="product-detail-modal" tabindex="-1" aria-hidden="true" 
         class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none bg-slate-900/60 backdrop-blur-sm transition-all duration-300">
        
        <div class="relative w-auto my-6 mx-auto max-w-4xl opacity-0 scale-95 transition-all duration-300 transform" id="product-modal-panel">
            {{-- Modal Content --}}
            <div class="border-0 rounded-2xl shadow-2xl relative flex flex-col w-full bg-white outline-none focus:outline-none overflow-hidden h-[90vh] md:h-auto">
                
                {{-- Header --}}
                <div class="flex items-start justify-between p-5 border-b border-slate-100 rounded-t bg-slate-50">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800" id="modal-product-name">
                            Detail Produk
                        </h3>
                        <p class="text-sm text-slate-500 mt-1" id="modal-product-brand">
                            Brand Info
                        </p>
                    </div>
                    <button class="p-1 ml-auto bg-transparent border-0 text-slate-400 hover:text-slate-600 float-right text-3xl leading-none font-semibold outline-none focus:outline-none transition-colors" onclick="closeProductDetail()">
                        <span class="h-6 w-6 block outline-none focus:outline-none">
                            <i class="bi bi-x-lg text-xl"></i>
                        </span>
                    </button>
                </div>
                
                {{-- Body --}}
                <div class="relative p-6 flex-auto overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- Left: Image --}}
                        <div class="relative h-64 md:h-80 bg-slate-50 rounded-xl flex items-center justify-center p-6 border border-slate-100">
                            <img id="modal-product-image" src="" alt="Product" class="max-h-full max-w-full object-contain drop-shadow-lg">
                            <div class="absolute bottom-4 left-4">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur rounded-lg shadow-sm text-xs font-bold text-indigo-600 border border-slate-100" id="modal-product-code">
                                    CODE-123
                                </span>
                            </div>
                        </div>
                        
                        {{-- Right: Info --}}
                        <div class="flex flex-col h-full">
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-slate-800 mb-2">Deskripsi</h4>
                                <p class="text-slate-600 text-sm leading-relaxed mb-6" id="modal-product-description">
                                    Deskripsi produk akan muncul di sini.
                                </p>
                                
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                                        <span class="block text-xs font-semibold text-indigo-500 uppercase tracking-wider mb-1">Harga</span>
                                        <span class="block text-2xl font-bold text-indigo-700" id="modal-product-price">Rp 0</span>
                                    </div>
                                    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                                        <span class="block text-xs font-semibold text-emerald-500 uppercase tracking-wider mb-1">Stok Tersedia</span>
                                        <span class="block text-2xl font-bold text-emerald-700" id="modal-product-stock">0</span>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-sm text-slate-500">Kategori</span>
                                        <span class="text-sm font-medium text-slate-800" id="modal-product-category">-</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-sm text-slate-500">Rak / Lokasi</span>
                                        <span class="text-sm font-medium text-slate-800" id="modal-product-location">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Footer: Actions --}}
                <div class="flex items-center justify-end p-5 border-t border-slate-100 rounded-b bg-slate-50 gap-3">
                    <button class="px-6 py-2.5 rounded-xl border border-slate-300 text-slate-600 font-semibold text-sm hover:bg-slate-100 transition-colors focus:outline-none" onclick="closeProductDetail()">
                        Tutup
                    </button>
                    
                    <div class="flex items-center bg-white border border-slate-300 rounded-xl h-10 shadow-sm">
                         <button type="button" onclick="adjustModalQty(-1)" class="w-10 h-full flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-l-xl transition-colors">
                            <i class="bi bi-dash"></i>
                         </button>
                         <input type="number" id="modal-qty-input" value="1" min="1" class="w-12 text-center border-none p-0 text-sm font-bold text-slate-800 focus:ring-0" readonly>
                         <button type="button" onclick="adjustModalQty(1)" class="w-10 h-full flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-r-xl transition-colors">
                            <i class="bi bi-plus"></i>
                         </button>
                    </div>

                    <button class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm shadow-md hover:bg-indigo-700 hover:shadow-lg transform active:scale-95 transition-all flex items-center gap-2" onclick="addCartFromModal()">
                        <i class="bi bi-cart-plus-fill"></i>
                        Masukan Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- START SCRIPTS --}}
    <script>
        // Global variables for Modal
        let currentModalProduct = null;

        /**
         * Open Product Detail Modal
         */
        function openProductDetail(productDataString) {
            // Parse if string, otherwise use directly
            const product = typeof productDataString === 'string' ? JSON.parse(productDataString) : productDataString;
            currentModalProduct = product;
            
            // Populate Modal
            document.getElementById('modal-product-name').textContent = product.name;
            document.getElementById('modal-product-brand').textContent = product.brand;
            document.getElementById('modal-product-image').src = product.image;
            document.getElementById('modal-product-code').textContent = product.code;
            document.getElementById('modal-product-description').textContent = product.description;
            document.getElementById('modal-product-price').textContent = product.formatted_price;
            document.getElementById('modal-product-stock').textContent = product.stock;
            document.getElementById('modal-product-category').textContent = product.category;
            document.getElementById('modal-product-location').textContent = product.location;
            
            // Allow formatting safety
            let desc = product.description;
            if(desc.length > 150) desc = desc.substring(0, 150) + '...';
            document.getElementById('modal-product-description').textContent = desc;

            // Reset Qty
            document.getElementById('modal-qty-input').value = 1;

            // Disable add button if no stock
            // (Implementation optional based on button reference)
            
            // Show Modal
            const modal = document.getElementById('product-detail-modal');
            const panel = document.getElementById('product-modal-panel');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Animation timeout
            setTimeout(() => {
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            }, 10);
            
            document.body.style.overflow = 'hidden';
        }

        /**
         * Close Product Detail Modal
         */
        function closeProductDetail() {
            const modal = document.getElementById('product-detail-modal');
            const panel = document.getElementById('product-modal-panel');
            
            // Animation
            panel.classList.remove('opacity-100', 'scale-100');
            panel.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
        }

        /**
         * Adjust Qty in Card
         */
        function adjustCardQty(btn, delta) {
            const container = btn.parentElement;
            const input = container.querySelector('input');
            const max = parseInt(input.getAttribute('data-max'));
            let val = parseInt(input.value);
            
            val += delta;
            
            if (val < 1) val = 1;
            if (val > max) val = max;
            
            input.value = val;
        }

        /**
         * Adjust Qty in Modal
         */
        function adjustModalQty(delta) {
            if(!currentModalProduct) return;
            const input = document.getElementById('modal-qty-input');
            const max = currentModalProduct.stock;
            let val = parseInt(input.value);
            
            val += delta;
            
            if (val < 1) val = 1;
            if (val > max) val = max;
            
            input.value = val;
        }

        /**
         * Add to Cart from Card
         */
        function addToCartFromCard(btn, productDataString) {
            const product = typeof productDataString === 'string' ? JSON.parse(productDataString) : productDataString;
            // Find qty input in this card
            // Traverse up to article, then down to input
            // But simpler: the button is sibling to qty-control div
            const qtyInput = btn.previousElementSibling.querySelector('input');
            const qty = parseInt(qtyInput.value);
            
            // Call global AddToCart
            // addToCart(id, name, price, type, qty, image)
            if (typeof addToCart === 'function') {
                addToCart(product.id, product.name, product.price, 'product', qty, product.image);
                
                // Reset qty to 1 for better UX
                qtyInput.value = 1;
                
                // Animation feedback on button
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg text-lg"></i><span>Masuk</span>';
                btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                
                setTimeout(() => {
                    btn.innerHTML = originalContent;
                    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                }, 1500);
            } else {
                console.error('addToCart function not found');
            }
        }

        /**
         * Add Cart From Modal
         */
        function addCartFromModal() {
            if(!currentModalProduct) return;
            
            const qty = parseInt(document.getElementById('modal-qty-input').value);
            
             if (typeof addToCart === 'function') {
                addToCart(
                    currentModalProduct.id, 
                    currentModalProduct.name, 
                    currentModalProduct.price, 
                    'product', 
                    qty, 
                    currentModalProduct.image
                );
                closeProductDetail();
            }
        }

        // Close modal on backdrop click
        document.addEventListener('DOMContentLoaded', () => {
             const modal = document.getElementById('product-detail-modal');
             modal.addEventListener('click', (e) => {
                 if(e.target === modal) closeProductDetail();
             });
        });
    </script>
</div>
