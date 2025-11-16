{{-- Modules/Product/Resources/views/partials/_form.blade.php --}}

<div class="row mt-4">
    {{-- Left Column: Product Info --}}
    <div class="col-lg-8">
        {{-- Section 1: Basic Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-info mr-2 text-primary"></i>
                    Informasi Dasar
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Product Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_name" class="form-label font-weight-semibold">
                                <i class="cil-tag mr-1 text-muted"></i> Nama Barang
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="product_name"
                                name="product_name"
                                class="form-control form-control-lg @error('product_name') is-invalid @enderror"
                                value="{{ old('product_name', $product->product_name ?? '') }}"
                                placeholder="Contoh: Ban Mobil Bridgestone"
                                required
                            >
                            @error('product_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Product Code --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_code" class="form-label font-weight-semibold">
                                <i class="cil-barcode mr-1 text-muted"></i> Kode Barang
                                <span class="text-danger">*</span>
                            </label>

                            @php
                                $defaultCode = 'PRD-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                            @endphp

                            <div class="input-group">
                                <input
                                    type="text"
                                    id="product_code"
                                    name="product_code"
                                    class="form-control form-control-lg @error('product_code') is-invalid @enderror"
                                    value="{{ old('product_code', $product->product_code ?? $defaultCode) }}"
                                    placeholder="PRD-0001"
                                    required
                                >
                                {{-- Tombol generate hanya dipakai di create, tapi aman juga saat edit --}}
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button"
                                            id="generateCode"
                                            title="Generate Kode Baru">
                                        <i class="cil-reload"></i>
                                    </button>
                                </div>
                            </div>
                            @error('product_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-info mr-1"></i>
                                Kode unik untuk identifikasi produk
                            </small>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id" class="form-label font-weight-semibold">
                                <i class="cil-folder mr-1 text-muted"></i> Kategori
                                <span class="text-danger">*</span>
                            </label>
                            <select
                                id="category_id"
                                name="category_id"
                                class="form-control form-control-lg @error('category_id') is-invalid @enderror"
                                required
                            >
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id ?? null) == $category->id ? 'selected' : '' }}
                                    >
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Brand --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="brand_id" class="form-label font-weight-semibold">
                                <i class="cil-bookmark mr-1 text-muted"></i> Merek
                            </label>
                            <select
                                id="brand_id"
                                name="brand_id"
                                class="form-control form-control-lg @error('brand_id') is-invalid @enderror"
                            >
                                <option value="">-- Tanpa Merek --</option>
                                @foreach ($brands as $brand)
                                    <option
                                        value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id ?? null) == $brand->id ? 'selected' : '' }}
                                    >
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Product Specifications --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-settings mr-2 text-primary"></i>
                    Spesifikasi Produk
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Size --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_size" class="form-label font-weight-semibold">
                                <i class="cil-resize-both mr-1 text-muted"></i> Ukuran
                            </label>
                            <input
                                type="text"
                                id="product_size"
                                name="product_size"
                                class="form-control form-control-lg @error('product_size') is-invalid @enderror"
                                value="{{ old('product_size', $product->product_size ?? '') }}"
                                placeholder="235/75 R15"
                            >
                            @error('product_size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Ring --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ring" class="form-label font-weight-semibold">
                                <i class="cil-sun mr-1 text-muted"></i> Ring
                            </label>
                            <input
                                type="text"
                                id="ring"
                                name="ring"
                                class="form-control form-control-lg @error('ring') is-invalid @enderror"
                                value="{{ old('ring', $product->ring ?? '') }}"
                                placeholder="15"
                            >
                            @error('ring')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Year --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_year" class="form-label font-weight-semibold">
                                <i class="cil-calendar mr-1 text-muted"></i> Tahun Produksi
                            </label>
                            <input
                                type="number"
                                id="product_year"
                                name="product_year"
                                class="form-control form-control-lg @error('product_year') is-invalid @enderror"
                                value="{{ old('product_year', $product->product_year ?? '') }}"
                                placeholder="{{ date('Y') }}"
                                min="2000"
                                max="{{ date('Y') + 1 }}"
                            >
                            @error('product_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Pricing --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-dollar mr-2 text-primary"></i>
                    Harga & Modal
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Cost --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_cost" class="form-label font-weight-semibold">
                                <i class="cil-arrow-circle-bottom mr-1 text-muted"></i> Modal
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="product_cost"
                                name="product_cost"
                                class="form-control form-control-lg @error('product_cost') is-invalid @enderror"
                                value="{{ old('product_cost', $product->product_cost ?? 0) }}"
                                required
                            >
                            @error('product_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-info mr-1"></i>
                                Harga beli dari supplier
                            </small>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_price" class="form-label font-weight-semibold">
                                <i class="cil-arrow-circle-top mr-1 text-muted"></i> Harga Jual
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="product_price"
                                name="product_price"
                                class="form-control form-control-lg @error('product_price') is-invalid @enderror"
                                value="{{ old('product_price', $product->product_price ?? 0) }}"
                                required
                            >
                            @error('product_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-info mr-1"></i>
                                Harga jual ke customer
                            </small>
                        </div>
                    </div>

                    {{-- Profit Margin Display --}}
                    <div class="col-12">
                        <div class="alert alert-info" id="profitMarginAlert" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Margin Keuntungan:</strong>
                                    <span id="profitAmount"></span>
                                </div>
                                <div>
                                    <span class="badge badge-primary badge-lg" id="profitPercentage"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Stock --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-layers mr-2 text-primary"></i>
                    Stok & Satuan
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Initial Stock --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stok_awal" class="form-label font-weight-semibold">
                                <i class="cil-plus mr-1 text-muted"></i> Stok Awal
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                id="stok_awal"
                                name="stok_awal"
                                class="form-control form-control-lg @error('stok_awal') is-invalid @enderror"
                                value="{{ old('stok_awal', $product->stok_awal ?? 0) }}"
                                min="0"
                                required
                            >
                            @error('stok_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Current Stock (Readonly, hanya saat edit) --}}
                    @if (isset($product))
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_quantity" class="form-label font-weight-semibold">
                                    <i class="cil-layers mr-1 text-muted"></i> Stok Sisa
                                </label>
                                <input
                                    type="number"
                                    id="product_quantity"
                                    name="product_quantity"
                                    class="form-control form-control-lg"
                                    value="{{ $product->product_quantity }}"
                                    readonly
                                    style="background-color: #f8f9fa;"
                                >
                                <small class="form-text text-muted">
                                    <i class="cil-lock-locked mr-1"></i>
                                    Stok saat ini (tidak bisa diedit)
                                </small>
                            </div>
                        </div>
                    @endif

                    {{-- Stock Alert --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_stock_alert" class="form-label font-weight-semibold">
                                <i class="cil-warning mr-1 text-muted"></i> Stok Minimum
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                id="product_stock_alert"
                                name="product_stock_alert"
                                class="form-control form-control-lg @error('product_stock_alert') is-invalid @enderror"
                                value="{{ old('product_stock_alert', $product->product_stock_alert ?? 4) }}"
                                min="0"
                                required
                            >
                            @error('product_stock_alert')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-bell mr-1"></i>
                                Alert saat stok mencapai jumlah ini
                            </small>
                        </div>
                    </div>

                    {{-- Unit --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_unit" class="form-label font-weight-semibold">
                                <i class="cil-spreadsheet mr-1 text-muted"></i> Unit Satuan
                                <span class="text-danger">*</span>
                            </label>
                            <select
                                id="product_unit"
                                name="product_unit"
                                class="form-control form-control-lg @error('product_unit') is-invalid @enderror"
                                required
                            >
                                @foreach (\Modules\Setting\Entities\Unit::all() as $unit)
                                    <option
                                        value="{{ $unit->short_name }}"
                                        {{ old('product_unit', $product->product_unit ?? $unit->short_name) == $unit->short_name ? 'selected' : '' }}
                                    >
                                        {{ $unit->name }} ({{ $unit->short_name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 5: Notes --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-notes mr-2 text-primary"></i>
                    Catatan Tambahan
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="form-group mb-0">
                    <label for="product_note" class="form-label font-weight-semibold">
                        <i class="cil-pencil mr-1 text-muted"></i> Catatan
                    </label>
                    <textarea
                        id="product_note"
                        name="product_note"
                        rows="4"
                        class="form-control @error('product_note') is-invalid @enderror"
                        placeholder="Tambahkan catatan atau informasi tambahan tentang produk..."
                    >{{ old('product_note', $product->product_note ?? '') }}</textarea>
                    @error('product_note')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="cil-info mr-1"></i>
                        Informasi opsional untuk keperluan internal
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Images --}}
    <div class="col-lg-4">
        <div class="card shadow-sm sticky-sidebar">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-image mr-2 text-primary"></i>
                    Gambar Produk
                </h6>
            </div>
            <div class="card-body p-4">
                @if (isset($product))
                    {{-- Edit mode: kirim model ke komponen --}}
                    <x-image-upload
                        :model="$product"
                        max-files="3"
                        label=""
                        max-size="2"
                        help-text="Upload maksimal 3 gambar produk. Format: JPG, PNG. Ukuran maks: 2MB per file."
                    />
                @else
                    {{-- Create mode --}}
                    <x-image-upload
                        max-files="3"
                        label=""
                        max-size="2"
                        help-text="Upload maksimal 3 gambar produk. Format: JPG, PNG. Ukuran maks: 2MB per file."
                    />
                @endif

                <div class="alert alert-info mt-3" role="alert">
                    <small>
                        <i class="cil-lightbulb mr-1"></i>
                        <strong>Tips:</strong> Gunakan gambar dengan kualitas baik dan pencahayaan cukup untuk hasil terbaik.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
