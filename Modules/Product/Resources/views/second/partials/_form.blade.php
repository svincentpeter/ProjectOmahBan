{{-- Modules/Product/Resources/views/second/partials/_form.blade.php --}}
{{-- Flowbite/Tailwind version - matching products/_form.blade.php styling --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    {{-- Left Column: Product Info --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Alert for Used Products --}}
        @if (!isset($product))
            <div class="p-4 text-blue-800 border border-blue-200 rounded-xl bg-blue-50 dark:bg-gray-800 dark:border-blue-900 dark:text-blue-400" role="alert">
                <div class="flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-xl mt-0.5"></i>
                    <div>
                        <span class="font-bold block">Produk Bekas/Second</span>
                        <p class="text-sm mt-1">
                            Produk ini adalah barang bekas yang tidak ada dalam master produk. Isi detail manual dan upload foto kondisi aktual.
                        </p>
                    </div>
                </div>
            </div>
        @endif

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
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-tag me-1 text-gray-500"></i> Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                            value="{{ old('name', $product->name ?? '') }}"
                            placeholder="Contoh: Ban Bekas Bridgestone 185/70 R14" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nama lengkap dengan merek & ukuran jika diketahui</p>
                    </div>

                    {{-- Unique Code --}}
                    <div>
                        <label for="unique_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-upc-scan me-1 text-gray-500"></i> Kode Unik <span class="text-red-500">*</span>
                        </label>
                        @php
                            $defaultCode = 'SH-' . strtoupper(substr(md5(time()), 0, 6));
                        @endphp
                        <div class="flex">
                            <div class="relative w-full">
                                <input type="text" id="unique_code" name="unique_code" 
                                    class="rounded-none rounded-s-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('unique_code') border-red-500 @enderror"
                                    value="{{ old('unique_code', $product->unique_code ?? $defaultCode) }}"
                                    placeholder="SH-XXXXXX" required>
                            </div>
                            <button type="button" id="generateCode" class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-s-0 border-gray-300 rounded-e-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-500 transition-colors" title="Generate Kode Baru">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                        @error('unique_code')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kode unik untuk identifikasi produk bekas</p>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-folder me-1 text-gray-500"></i> Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id" name="category_id" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('category_id') border-red-500 @enderror" required>
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
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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

        {{-- Section 2: Specifications --}}
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
                        <label for="size" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-arrows-angle-expand me-1 text-gray-500"></i> Ukuran
                        </label>
                        <input type="text" id="size" name="size" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ old('size', $product->size ?? '') }}"
                            placeholder="185/70 R14">
                        @error('size')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ring --}}
                    <div>
                        <label for="ring" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-record-circle me-1 text-gray-500"></i> Ring
                        </label>
                        <input type="text" id="ring" name="ring" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ old('ring', $product->ring ?? '') }}"
                            placeholder="14">
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
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="{{ old('product_year', $product->product_year ?? '') }}"
                            placeholder="{{ date('Y') }}" min="1990" max="{{ date('Y') + 1 }}">
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
                    {{-- Purchase Price --}}
                    <div>
                        <label for="purchase_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-arrow-down-circle me-1 text-gray-500"></i> Harga Beli <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="purchase_price" name="purchase_price" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('purchase_price') border-red-500 @enderror"
                            value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" required>
                        @error('purchase_price')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Harga beli dari penjual lama</p>
                    </div>

                    {{-- Selling Price --}}
                    <div>
                        <label for="selling_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-arrow-up-circle me-1 text-gray-500"></i> Harga Jual <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="selling_price" name="selling_price" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('selling_price') border-red-500 @enderror"
                            value="{{ old('selling_price', $product->selling_price ?? 0) }}" required>
                        @error('selling_price')
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

        {{-- Section 4: Condition & Status --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-file-text me-2 text-blue-600"></i>
                    Kondisi & Status
                </h6>
            </div>
            <div class="p-6">
                {{-- Status (only in edit) --}}
                @if (isset($product))
                    <div class="mb-4">
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <i class="bi bi-toggle-on me-1 text-gray-500"></i> Status Produk <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                            <option value="available" {{ old('status', $product->status ?? '') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="sold" {{ old('status', $product->status ?? '') == 'sold' ? 'selected' : '' }}>Terjual</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- Condition Notes --}}
                <div>
                    <label for="condition_notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <i class="bi bi-pencil me-1 text-gray-500"></i> Deskripsi Kondisi
                    </label>
                    <textarea id="condition_notes" name="condition_notes" rows="4" 
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 placeholder:text-gray-400 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Jelaskan kondisi fisik, kerusakan, atau kekurangan produk secara detail...">{{ old('condition_notes', $product->condition_notes ?? '') }}</textarea>
                    @error('condition_notes')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Detail kondisi untuk referensi dan transparansi</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Images --}}
    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 lg:sticky lg:top-[88px]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="bi bi-images me-2 text-blue-600"></i>
                    Foto Produk Bekas
                </h6>
            </div>
            <div class="p-6">
                {{-- Dropzone --}}
                <div class="dropzone-container mb-4">
                    <div id="document-dropzone" class="dropzone border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors p-6 text-center cursor-pointer min-h-[200px] flex flex-col items-center justify-center dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                        <div class="dz-message" data-dz-message>
                            <i class="bi bi-cloud-upload text-4xl text-gray-400 mb-2 dark:text-gray-300"></i>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Klik atau drag file ke sini</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Maks 3 gambar, 1MB per file</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-700 dark:text-blue-400" role="alert">
                    <span class="font-medium"><i class="bi bi-lightbulb me-1"></i> Tips:</span> Ambil dari berbagai sudut. Tampilkan kondisi jelas & bagian cacat (jika ada).
                </div>
            </div>
        </div>
    </div>
</div>
