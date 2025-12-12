{{-- Modules/Product/Resources/views/products/partials/_form.blade.php --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    {{-- Left Column: Product Info --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Section 1: Basic Info --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-info-circle me-2 text-blue-600"></i>
                    Informasi Dasar
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Product Name --}}
                    <div>
                        <label for="product_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-tag me-1 text-gray-500"></i> Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="product_name" name="product_name" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('product_name') border-red-500 @enderror"
                            value="{{ old('product_name', $product->product_name ?? '') }}"
                            placeholder="Contoh: Ban Mobil Bridgestone" required>
                        @error('product_name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product Code --}}
                    <div>
                        <label for="product_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-upc-scan me-1 text-gray-500"></i> Kode Barang <span class="text-red-500">*</span>
                        </label>
                        @php
                            $defaultCode = 'PRD-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                        @endphp
                        <div class="flex">
                            <div class="relative w-full">
                                <input type="text" id="product_code" name="product_code" 
                                    class="rounded-none rounded-s-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('product_code') border-red-500 @enderror"
                                    value="{{ old('product_code', $product->product_code ?? $defaultCode) }}"
                                    placeholder="PRD-0001" required>
                            </div>
                            <button type="button" id="generateCode" class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-s-0 border-gray-300 rounded-e-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-500 transition-colors" title="Generate Kode Baru">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                        @error('product_code')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kode unik untuk identifikasi produk</p>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-folder me-1 text-gray-500"></i> Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id" name="category_id" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('category_id') border-red-500 @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? null) == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Brand --}}
                    <div>
                        <label for="brand_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-award me-1 text-gray-500"></i> Merek
                        </label>
                        <select id="brand_id" name="brand_id" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">-- Tanpa Merek --</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? null) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Product Specifications --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-sliders me-2 text-blue-600"></i>
                    Spesifikasi Produk
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Size --}}
                    <div>
                        <label for="product_size" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-arrows-angle-expand me-1 text-gray-500"></i> Ukuran
                        </label>
                        <input type="text" id="product_size" name="product_size" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ old('product_size', $product->product_size ?? '') }}"
                            placeholder="235/75 R15">
                        @error('product_size')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ring --}}
                    <div>
                        <label for="ring" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-record-circle me-1 text-gray-500"></i> Ring
                        </label>
                        <input type="text" id="ring" name="ring" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ old('ring', $product->ring ?? '') }}"
                            placeholder="15">
                        @error('ring')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Year --}}
                    <div>
                        <label for="product_year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-calendar-event me-1 text-gray-500"></i> Tahun Produksi
                        </label>
                        <input type="number" id="product_year" name="product_year" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ old('product_year', $product->product_year ?? '') }}"
                            placeholder="{{ date('Y') }}" min="2000" max="{{ date('Y') + 1 }}">
                        @error('product_year')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Pricing --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-cash-stack me-2 text-blue-600"></i>
                    Harga & Modal
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Cost --}}
                    <div>
                        <label for="product_cost" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-arrow-down-circle me-1 text-gray-500"></i> Modal <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="product_cost" name="product_cost" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('product_cost') border-red-500 @enderror"
                            value="{{ old('product_cost', $product->product_cost ?? 0) }}" required>
                        @error('product_cost')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Harga beli dari supplier</p>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="product_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-arrow-up-circle me-1 text-gray-500"></i> Harga Jual <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="product_price" name="product_price" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('product_price') border-red-500 @enderror"
                            value="{{ old('product_price', $product->product_price ?? 0) }}" required>
                        @error('product_price')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Harga jual ke customer</p>
                    </div>

                    {{-- Profit Margin Display --}}
                    <div class="col-span-1 md:col-span-2">
                        <div id="profitMarginAlert" class="hidden p-4 text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-700 dark:text-blue-400 dark:border-blue-800 relative" role="alert">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-bold"><i class="bi bi-graph-up-arrow me-1"></i> Margin Keuntungan:</span>
                                    <span id="profitAmount" class="ms-1 font-medium"></span>
                                </div>
                                <div>
                                    <span id="profitPercentage" class="inline-flex items-center justify-center px-2.5 py-0.5 rounded text-sm font-medium"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Stock --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-layers me-2 text-blue-600"></i>
                    Stok & Satuan
                </h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Initial Stock --}}
                    <div>
                        <label for="stok_awal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-plus-circle me-1 text-gray-500"></i> Stok Awal <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stok_awal" name="stok_awal" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('stok_awal') border-red-500 @enderror"
                            value="{{ old('stok_awal', $product->stok_awal ?? 0) }}" min="0" required>
                        @error('stok_awal')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Current Stock (Readonly, only edit) --}}
                    @if (isset($product))
                        <div>
                            <label for="product_quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                <i class="bi bi-box-seam me-1 text-gray-500"></i> Stok Sisa
                            </label>
                            <input type="number" id="product_quantity" name="product_quantity" 
                                class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400"
                                value="{{ $product->product_quantity }}" readonly>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Stok saat ini (tidak bisa diedit)</p>
                        </div>
                    @endif

                    {{-- Stock Alert --}}
                    <div>
                        <label for="product_stock_alert" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-bell me-1 text-gray-500"></i> Stok Minimum <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="product_stock_alert" name="product_stock_alert" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('product_stock_alert') border-red-500 @enderror"
                            value="{{ old('product_stock_alert', $product->product_stock_alert ?? 4) }}" min="0" required>
                        @error('product_stock_alert')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alert saat stok menipis</p>
                    </div>

                    {{-- Unit --}}
                    <div>
                        <label for="product_unit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-rulers me-1 text-gray-500"></i> Unit Satuan <span class="text-red-500">*</span>
                        </label>
                        <select id="product_unit" name="product_unit" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('product_unit') border-red-500 @enderror" required>
                            @foreach (\Modules\Setting\Entities\Unit::all() as $unit)
                                <option value="{{ $unit->short_name }}" {{ old('product_unit', $product->product_unit ?? $unit->short_name) == $unit->short_name ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->short_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_unit')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 5: Notes --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-sticky me-2 text-blue-600"></i>
                    Catatan Tambahan
                </h6>
            </div>
            <div class="p-6">
                <label for="product_note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    <i class="bi bi-pencil me-1 text-gray-500"></i> Catatan
                </label>
                <textarea id="product_note" name="product_note" rows="4" 
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Tambahkan catatan atau informasi tambahan tentang produk...">{{ old('product_note', $product->product_note ?? '') }}</textarea>
                @error('product_note')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Informasi opsional untuk keperluan internal</p>
            </div>
        </div>
    </div>

    {{-- Right Column: Images --}}
    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 lg:sticky lg:top-[88px]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-images me-2 text-blue-600"></i>
                    Gambar Produk
                </h6>
            </div>
            <div class="p-6">
                @if (isset($product))
                    {{-- Edit mode --}}
                    <x-image-upload :model="$product" max-files="3" label="" max-size="2" help-text="Upload maksimal 3 gambar. Format: JPG, PNG. Maks: 2MB/file." />
                @else
                    {{-- Create mode --}}
                    <x-image-upload max-files="3" label="" max-size="2" help-text="Upload maksimal 3 gambar. Format: JPG, PNG. Maks: 2MB/file." />
                @endif

                <div class="p-4 mt-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-700 dark:text-blue-400" role="alert">
                    <span class="font-medium"><i class="bi bi-lightbulb me-1"></i> Tips:</span> Gunakan gambar dengan kualitas baik dan pencahayaan cukup.
                </div>
            </div>
        </div>
    </div>
</div>
