@extends('layouts.pos')

@section('title', 'Point of Sale')

@section('content')
    <div class="grid gap-4 lg:grid-cols-3">

        {{-- ========================================
         KOLOM KIRI: HERO + TABS + FILTER + PRODUCTS
        ========================================= --}}
        <section class="space-y-4 lg:col-span-2">

            {{-- HERO POS / HEADER CARD dengan Tabs --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-4 flex flex-col gap-3">

                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h1 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-ob-soft text-ob-primary">
                                <i class="bi bi-cart3"></i>
                            </span>
                            Point of Sale
                        </h1>
                        <p class="text-sm text-slate-600">
                            Pilih
                            <span class="font-semibold">Produk Baru</span>,
                            <span class="font-semibold">Produk Bekas</span>,
                            <span class="font-semibold">Jasa</span>, atau
                            <span class="font-semibold">Input Manual</span> pada tab di bawah.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2 text-xs">
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-700">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                            Mode Online
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2.5 py-1 text-slate-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                            Auto-sync stok
                        </span>
                    </div>
                </div>

            {{-- TABS: 4 TAB UTAMA (NEW, SECOND, SERVICE, MANUAL) --}}
                <ul class="flex flex-wrap text-sm font-medium text-center text-slate-500 border-b border-slate-200" id="posTab" role="tablist">
    <li class="mr-2" role="presentation">
        <button class="inline-flex items-center gap-1 px-4 py-2 border-b-2 rounded-t-lg"
                id="tab-new" 
                data-tabs-target="#panel-new" 
                type="button" 
                role="tab" 
                aria-controls="panel-new" 
                aria-selected="false">
            <i class="bi bi-box-seam"></i> Produk Baru
        </button>
    </li>
    <li class="mr-2" role="presentation">
        <button class="inline-flex items-center gap-1 px-4 py-2 border-b-2 border-transparent rounded-t-lg hover:text-slate-800 hover:border-slate-200"
                id="tab-second" 
                data-tabs-target="#panel-second" 
                type="button" 
                role="tab" 
                aria-controls="panel-second" 
                aria-selected="false">
            <i class="bi bi-arrow-repeat"></i> Produk Bekas
        </button>
    </li>
    <li class="mr-2" role="presentation">
        <button class="inline-flex items-center gap-1 px-4 py-2 border-b-2 border-transparent rounded-t-lg hover:text-slate-800 hover:border-slate-200"
                id="tab-service" 
                data-tabs-target="#panel-service" 
                type="button" 
                role="tab" 
                aria-controls="panel-service" 
                aria-selected="false">
            <i class="bi bi-tools"></i> Jasa
        </button>
    </li>
    <li role="presentation">
        <button class="inline-flex items-center gap-1 px-4 py-2 border-b-2 border-transparent rounded-t-lg hover:text-slate-800 hover:border-slate-200"
                id="tab-manual" 
                data-tabs-target="#panel-manual" 
                type="button" 
                role="tab" 
                aria-controls="panel-manual" 
                aria-selected="false">
            <i class="bi bi-pencil-square"></i> Input Manual
        </button>
    </li>
</ul>
            </div>

            {{-- ========================================
             TAB CONTENT: GRID PRODUK / JASA / MANUAL
            ========================================= --}}
            <div id="posTabContent">
                {{-- PANEL: PRODUK BARU --}}
                <div id="panel-new" role="tabpanel" aria-labelledby="tab-new">
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                        
                        {{-- FILTER CONTAINER: Produk Baru --}}
                        <div class="bg-slate-50 border-b border-slate-200 p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                                    <i class="bi bi-funnel"></i> Filter Produk Baru
                                </h3>
                                <button type="button" onclick="resetFilters('new')" 
                                    class="text-xs text-slate-400 hover:text-red-500 transition-colors">
                                    <i class="bi bi-x-circle"></i> Reset
                                </button>
                            </div>
                            
                            {{-- Filter By Category --}}
                            <div>
                                <p class="text-[10px] font-semibold text-slate-500 mb-2">KATEGORI</p>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" onclick="selectCategory(null)"
                                        class="category-pill-new active inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border-2 border-ob-primary bg-ob-soft text-ob-primary text-xs font-semibold transition-all hover:shadow-md">
                                        <span>🌟</span> Semua
                                    </button>
                                    @foreach ($categories ?? [] as $cat)
                                    <button type="button" onclick="selectCategory({{ $cat->id }})"
                                        class="category-pill-new inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-medium transition-all hover:border-ob-primary hover:text-ob-primary hover:shadow-sm">
                                        <span>📦</span> {{ $cat->category_name }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            
                            {{-- Filter By Brand --}}
                            <div>
                                <p class="text-[10px] font-semibold text-slate-500 mb-2">BRAND</p>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" onclick="selectBrand(null)"
                                        class="brand-pill-new active inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border-2 border-indigo-500 bg-indigo-50 text-indigo-600 text-xs font-semibold transition-all hover:shadow-md">
                                        Semua Brand
                                    </button>
                                    @foreach ($brands ?? [] as $brand)
                                    <button type="button" onclick="selectBrand({{ $brand->id }})"
                                        class="brand-pill-new inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-medium transition-all hover:border-indigo-500 hover:text-indigo-600 hover:shadow-sm">
                                        {{ $brand->name }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        {{-- Product List --}}
                        <div class="p-3">
                            <livewire:pos.product-list :categories="$categories" />
                        </div>
                    </div>
                </div>

                {{-- PANEL: PRODUK BEKAS --}}
                <div id="panel-second" class="hidden" role="tabpanel" aria-labelledby="tab-second">
                    <div class="bg-white border border-emerald-200/80 rounded-2xl shadow-sm overflow-hidden">
                        
                        {{-- FILTER CONTAINER: Produk Bekas --}}
                        <div class="bg-emerald-50 border-b border-emerald-200 p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-2">
                                    <i class="bi bi-funnel"></i> Filter Produk Bekas
                                </h3>
                                <button type="button" onclick="resetFiltersSecond()" 
                                    class="text-xs text-emerald-400 hover:text-red-500 transition-colors">
                                    <i class="bi bi-x-circle"></i> Reset
                                </button>
                            </div>
                            
                            {{-- Filter By Brand (Second) --}}
                            <div>
                                <p class="text-[10px] font-semibold text-emerald-600 mb-2">BRAND</p>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" onclick="selectBrandSecond(null)"
                                        class="brand-pill-second active inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border-2 border-emerald-500 bg-emerald-100 text-emerald-700 text-xs font-semibold transition-all hover:shadow-md">
                                        Semua Brand
                                    </button>
                                    @foreach ($brands ?? [] as $brand)
                                    <button type="button" onclick="selectBrandSecond({{ $brand->id }})"
                                        class="brand-pill-second inline-flex items-center px-3 py-1.5 rounded-lg border border-emerald-200 bg-white text-emerald-600 text-xs font-medium transition-all hover:border-emerald-500 hover:text-emerald-700 hover:shadow-sm">
                                        {{ $brand->name }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        {{-- Product List Second --}}
                        <div class="p-3">
                            <livewire:pos.product-list-second />
                        </div>
                    </div>
                </div>

                {{-- PANEL: JASA --}}
                <div id="panel-service" class="hidden" role="tabpanel" aria-labelledby="tab-service">
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-4">
                        <livewire:pos.service-list />
                    </div>
                </div>

                {{-- PANEL: INPUT MANUAL PRODUK --}}
                <div id="panel-manual" class="hidden" role="tabpanel" aria-labelledby="tab-manual">
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">Input Manual Produk</h3>
                                <p class="text-xs text-slate-500">Untuk ban/velg second yang belum di-input ke sistem</p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                <i class="bi bi-shield-exclamation"></i>
                                Owner Ternotifikasi
                            </span>
                        </div>
                        
                        <form id="manual-product-form" onsubmit="addManualProductToCart(event)" class="space-y-3">
                            
                            {{-- Nama Produk --}}
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">
                                    Nama Produk <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="product-name"
                                       name="product_name"
                                       required
                                       placeholder="Contoh: Ban Bridgestone Turanza 185/65 R15 (Second)"
                                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-ob-primary focus:ring-2 focus:ring-ob-primary/20">
                            </div>
                            
                            {{-- Row: Harga Beli & Harga Jual --}}
                            <div class="grid gap-3 sm:grid-cols-2">
                                
                                {{-- Harga Beli (HPP) --}}
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">
                                        Harga Beli (HPP) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Rp</span>
                                        <input type="text"
                                               id="product-cost"
                                               name="product_cost"
                                               required
                                               placeholder="0"
                                               oninput="formatCurrencyInput(this); calculateProfit()"
                                               class="w-full rounded-lg border border-slate-200 px-3 py-2 pl-10 text-sm focus:border-ob-primary focus:ring-2 focus:ring-ob-primary/20">
                                    </div>
                                </div>
                                
                                {{-- Harga Jual --}}
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">
                                        Harga Jual <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Rp</span>
                                        <input type="text"
                                               id="product-price"
                                               name="product_price"
                                               required
                                               placeholder="0"
                                               oninput="formatCurrencyInput(this); calculateProfit()"
                                               class="w-full rounded-lg border border-slate-200 px-3 py-2 pl-10 text-sm focus:border-ob-primary focus:ring-2 focus:ring-ob-primary/20">
                                    </div>
                                </div>
                                
                            </div>
                            
                            {{-- Auto-Calculate Profit Display --}}
                            <div id="profit-display" class="rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 p-3 hidden">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-slate-500">Laba Bersih</p>
                                        <p id="profit-amount" class="text-base font-bold text-emerald-600">Rp 0</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-slate-500">Margin</p>
                                        <p id="profit-percent" class="text-base font-bold text-emerald-600">0%</p>
                                    </div>
                                </div>
                                
                                {{-- Warning if margin too low --}}
                                <div id="profit-warning" class="mt-2 hidden rounded-lg bg-amber-50 px-2 py-1.5 flex items-start gap-1.5">
                                    <i class="bi bi-exclamation-triangle text-amber-600 text-xs mt-0.5"></i>
                                    <span class="text-[10px] text-amber-700">
                                        <strong>Margin rendah!</strong> Pastikan harga jual sudah sesuai.
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Quantity --}}
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">
                                    Jumlah
                                </label>
                                <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white">
                                    <button type="button"
                                            onclick="changeQty('product', -1)"
                                            class="inline-flex items-center justify-center text-slate-600 hover:bg-slate-50 transition-colors"
                                            style="width: 36px; height: 36px;">
                                        <i class="bi bi-dash text-base"></i>
                                    </button>
                                    
                                    <input type="number"
                                           id="product-qty"
                                           value="1"
                                           min="1"
                                           class="w-16 border-x border-slate-200 bg-white text-center text-sm font-semibold focus:outline-none appearance-none"
                                           style="height: 36px;">
                                    
                                    <button type="button"
                                            onclick="changeQty('product', 1)"
                                            class="inline-flex items-center justify-center text-slate-600 hover:bg-slate-50 transition-colors"
                                            style="width: 36px; height: 36px;">
                                        <i class="bi bi-plus text-base"></i>
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Alasan Input Manual --}}
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">
                                    Alasan Input Manual <span class="text-red-500">*</span>
                                </label>
                                <select id="product-reason"
                                        name="reason"
                                        required
                                        onchange="toggleCustomReason(this)"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-ob-primary focus:ring-2 focus:ring-ob-primary/20">
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="Barang second belum di-input">Barang second belum di-input</option>
                                    <option value="Stok mendadak tidak ada di sistem">Stok mendadak tidak ada di sistem</option>
                                    <option value="Produk baru dari supplier">Produk baru dari supplier</option>
                                    <option value="Emergency - customer mendesak">Emergency - customer mendesak</option>
                                    <option value="custom">Lainnya (tulis manual)</option>
                                </select>
                            </div>
                            
                            {{-- Custom Reason Input (Hidden by default) --}}
                            <div id="custom-reason-container" class="hidden">
                                <textarea id="product-reason-custom"
                                          rows="2"
                                          placeholder="Jelaskan alasan Anda..."
                                          class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-ob-primary focus:ring-2 focus:ring-ob-primary/20"></textarea>
                            </div>
                            
                            {{-- Info Box --}}
                            <div class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2">
                                <div class="flex gap-2">
                                    <i class="bi bi-info-circle text-blue-600 text-sm mt-0.5"></i>
                                    <div class="text-xs text-blue-700">
                                        <strong>Catatan Penting:</strong> Setiap input manual akan tercatat dan dinotifikasi ke owner untuk tracking dan approval.
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Submit Button --}}
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-ob-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-ob-primary transition-all">
                                <i class="bi bi-plus-circle text-base"></i>
                                Tambahkan ke Keranjang
                            </button>
                            
                        </form>
                        
                    </div>
                </div>
            </div>

        </section>

        {{-- ========================================
         KOLOM KANAN: KERANJANG + CHECKOUT
        ========================================= --}}
        <aside class="space-y-4 lg:sticky lg:top-20 h-fit">
            <livewire:pos.checkout cartInstance="sale" />
        </aside>

    </div>
@endsection

@push('page_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========================================
            // INISIALISASI FLOWBITE TABS
            // ========================================
            const tabsElement = document.getElementById('posTab');
            
            if (tabsElement && typeof Tabs !== 'undefined') {
                const tabElements = [
                    { id: 'panel-new', triggerEl: document.querySelector('#tab-new'), targetEl: document.querySelector('#panel-new') },
                    { id: 'panel-second', triggerEl: document.querySelector('#tab-second'), targetEl: document.querySelector('#panel-second') },
                    { id: 'panel-service', triggerEl: document.querySelector('#tab-service'), targetEl: document.querySelector('#panel-service') },
                    { id: 'panel-manual', triggerEl: document.querySelector('#tab-manual'), targetEl: document.querySelector('#panel-manual') }
                ];

                const options = {
                    defaultTabId: 'panel-new',
                    activeClasses: 'text-ob-primary border-ob-primary',
                    inactiveClasses: 'text-slate-500 border-transparent hover:text-slate-800 hover:border-slate-200'
                };

                // Inisialisasi Tabs instance
                const tabs = new Tabs(tabsElement, tabElements, options);
                
                // Set default tab
                tabs.show('panel-new');
            }

            // ========================================
            // LIVEWIRE HOOKS
            // ========================================
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('morph.updated', ({ el, component }) => {
                    // Re-init Flowbite setelah Livewire update
                    if (typeof initFlowbite === 'function') {
                        initFlowbite();
                    }
                });
            }

            // ========================================
            // SWEET ALERT CONFIRMATION
            // ========================================
            window.confirmReset = function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Reset Keranjang?',
                        text: 'Semua item akan dihapus.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Reset',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed && typeof Livewire !== 'undefined') {
                            Livewire.dispatch('resetCart');
                        }
                    });
                }
            };
        });

        // ========================================
        // FILTER FUNCTIONS - PRODUK BARU
        // ========================================
        function selectCategory(categoryId) {
            // Update UI - Category Pills
            document.querySelectorAll('.category-pill-new').forEach(btn => {
                btn.classList.remove('active', 'border-ob-primary', 'bg-ob-soft', 'text-ob-primary', 'border-2');
                btn.classList.add('border', 'border-slate-200', 'bg-white', 'text-slate-600');
            });
            
            const activeBtn = event.target.closest('.category-pill-new');
            if (activeBtn) {
                activeBtn.classList.add('active', 'border-ob-primary', 'bg-ob-soft', 'text-ob-primary', 'border-2');
                activeBtn.classList.remove('border-slate-200', 'bg-white', 'text-slate-600');
            }
            
            // Emit to Livewire
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('selectedCategory', { category_id: categoryId });
            }
        }

        function selectBrand(brandId) {
            // Update UI - Brand Pills
            document.querySelectorAll('.brand-pill-new').forEach(btn => {
                btn.classList.remove('active', 'border-indigo-500', 'bg-indigo-50', 'text-indigo-600', 'border-2');
                btn.classList.add('border', 'border-slate-200', 'bg-white', 'text-slate-600');
            });
            
            const activeBtn = event.target.closest('.brand-pill-new');
            if (activeBtn) {
                activeBtn.classList.add('active', 'border-indigo-500', 'bg-indigo-50', 'text-indigo-600', 'border-2');
                activeBtn.classList.remove('border-slate-200', 'bg-white', 'text-slate-600');
            }
            
            // Emit to Livewire
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('selectedBrand', { brand_id: brandId });
            }
        }

        function resetFilters(type) {
            if (type === 'new') {
                selectCategory(null);
                selectBrand(null);
                
                // Reset UI to first buttons
                const firstCat = document.querySelector('.category-pill-new');
                const firstBrand = document.querySelector('.brand-pill-new');
                if (firstCat) firstCat.click();
                if (firstBrand) firstBrand.click();
            }
        }

        // ========================================
        // FILTER FUNCTIONS - PRODUK BEKAS
        // ========================================
        function selectBrandSecond(brandId) {
            // Update UI - Brand Pills Second
            document.querySelectorAll('.brand-pill-second').forEach(btn => {
                btn.classList.remove('active', 'border-emerald-500', 'bg-emerald-100', 'text-emerald-700', 'border-2');
                btn.classList.add('border', 'border-emerald-200', 'bg-white', 'text-emerald-600');
            });
            
            const activeBtn = event.target.closest('.brand-pill-second');
            if (activeBtn) {
                activeBtn.classList.add('active', 'border-emerald-500', 'bg-emerald-100', 'text-emerald-700', 'border-2');
                activeBtn.classList.remove('border-emerald-200', 'bg-white', 'text-emerald-600');
            }
            
            // Emit to Livewire - ProductListSecond
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('selectedBrandSecond', { brand_id: brandId });
            }
        }

        function resetFiltersSecond() {
            selectBrandSecond(null);
            const firstBrand = document.querySelector('.brand-pill-second');
            if (firstBrand) firstBrand.click();
        }

        // ========================================
        // INPUT MANUAL PRODUCT FUNCTIONS
        // ========================================
        
        /**
         * Parse currency string to integer
         */
        function parseCurrency(value) {
            if (!value) return 0;
            // Remove all non-digit except minus
            return parseInt(value.toString().replace(/[^\d-]/g, '')) || 0;
        }

        /**
         * Format number to currency string
         */
        function formatCurrency(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        }

        /**
         * Format input field as currency while typing
         */
        function formatCurrencyInput(input) {
            let value = input.value.replace(/[^\d]/g, '');
            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(parseInt(value));
            }
        }

        /**
         * Calculate profit when cost or price changes
         */
        function calculateProfit() {
            const costInput = document.getElementById('product-cost');
            const priceInput = document.getElementById('product-price');
            const profitDisplay = document.getElementById('profit-display');
            const profitAmount = document.getElementById('profit-amount');
            const profitPercent = document.getElementById('profit-percent');
            const profitWarning = document.getElementById('profit-warning');
            
            const cost = parseCurrency(costInput.value);
            const price = parseCurrency(priceInput.value);
            
            if (cost > 0 && price > 0) {
                const profit = price - cost;
                const margin = ((profit / price) * 100).toFixed(2);
                
                profitAmount.textContent = formatCurrency(profit);
                profitPercent.textContent = margin + '%';
                
                // Show profit display
                profitDisplay.classList.remove('hidden');
                
                // Show warning if margin < 10%
                if (parseFloat(margin) < 10) {
                    profitWarning.classList.remove('hidden');
                } else {
                    profitWarning.classList.add('hidden');
                }
                
                // Color coding
                if (profit < 0) {
                    profitAmount.classList.remove('text-emerald-600');
                    profitAmount.classList.add('text-red-600');
                    profitPercent.classList.remove('text-emerald-600');
                    profitPercent.classList.add('text-red-600');
                } else {
                    profitAmount.classList.remove('text-red-600');
                    profitAmount.classList.add('text-emerald-600');
                    profitPercent.classList.remove('text-red-600');
                    profitPercent.classList.add('text-emerald-600');
                }
            } else {
                profitDisplay.classList.add('hidden');
            }
        }

        /**
         * Toggle custom reason textarea
         */
        function toggleCustomReason(select) {
            const customContainer = document.getElementById('custom-reason-container');
            const customTextarea = document.getElementById('product-reason-custom');
            
            if (select.value === 'custom') {
                customContainer.classList.remove('hidden');
                customTextarea.required = true;
            } else {
                customContainer.classList.add('hidden');
                customTextarea.required = false;
            }
        }

        /**
         * Change quantity for manual inputs
         */
        function changeQty(type, delta) {
            const input = document.getElementById(type + '-qty');
            let value = parseInt(input.value) || 1;
            value = Math.max(1, value + delta);
            input.value = value;
        }

        /**
         * Add manual product to cart via Livewire
         */
        function addManualProductToCart(event) {
            event.preventDefault();
            
            const name = document.getElementById('product-name').value.trim();
            const cost = parseCurrency(document.getElementById('product-cost').value);
            const price = parseCurrency(document.getElementById('product-price').value);
            const qty = parseInt(document.getElementById('product-qty').value) || 1;
            const reasonSelect = document.getElementById('product-reason');
            let reason = reasonSelect.value;
            
            // If custom reason, use textarea value
            if (reason === 'custom') {
                reason = document.getElementById('product-reason-custom').value.trim();
            }
            
            // Validation
            if (!name) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Nama produk wajib diisi!', timer: 2000, showConfirmButton: false });
                return;
            }
            if (price <= 0) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Harga jual harus lebih dari 0!', timer: 2000, showConfirmButton: false });
                return;
            }
            if (!reason) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Alasan input manual wajib dipilih!', timer: 2000, showConfirmButton: false });
                return;
            }
            
            // Send to Livewire Checkout component
            if (typeof Livewire !== 'undefined') {
                console.log('Dispatching addManualProduct:', { name, cost_price: cost, price, qty, reason });
                
                // Livewire 3: dispatch dengan format [payload]
                Livewire.dispatch('addManualProduct', [name, cost, price, qty, reason]);
                
                // Reset form
                document.getElementById('manual-product-form').reset();
                document.getElementById('product-qty').value = 1;
                document.getElementById('profit-display').classList.add('hidden');
                document.getElementById('custom-reason-container').classList.add('hidden');
                
                // Show success
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil!', 
                    text: 'Item manual ditambahkan ke keranjang. Owner akan menerima notifikasi.', 
                    timer: 2500, 
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        }
    </script>
@endpush

@push('styles')
<style>
    /* Premium Scrollbar */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Glassmorphism */
    .glass {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    @keyframes bounce-short {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-3px) scale(1.02); }
    }
    .animate-bounce-short {
        animation: bounce-short 0.3s ease-out;
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border-color: #6366f1; /* indigo-500 */
    }
    
    .category-pill.active {
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2), 0 2px 4px -1px rgba(79, 70, 229, 0.1);
    }
</style>
@endpush
