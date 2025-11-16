<div class="row">
    {{-- Left Column --}}
    <div class="col-lg-8">
        {{-- Alert for Used Products --}}
        @if (!isset($product))
            <div class="alert alert-warning second-alert-warning second-shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="cil-info mr-3 mt-1" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Produk Bekas/Second</strong>
                        <p class="mb-0">
                            <small>Produk ini adalah barang bekas yang tidak ada dalam master produk. Isi detail manual
                                dan upload foto kondisi aktual.</small>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Basic Info Card --}}
        <div class="card second-shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-info mr-2 text-primary"></i>
                    Informasi Produk Bekas
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label font-weight-semibold">
                                <i class="cil-tag mr-1 text-muted"></i> Nama Barang
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control form-control-lg second-form-control-lg @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name ?? '') }}"
                                placeholder="Contoh: Ban Bekas Bridgestone 185/70 R14"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-lightbulb mr-1"></i>
                                Nama lengkap dengan merek & ukuran jika diketahui
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
                                class="form-control form-control-lg second-form-control-lg @error('category_id') is-invalid @enderror"
                                required
                            >
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        {{ (isset($product) && $product->category_id == $category->id) || old('category_id') == $category->id ? 'selected' : '' }}
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
                                class="form-control form-control-lg second-form-control-lg @error('brand_id') is-invalid @enderror"
                            >
                                <option value="">-- Tanpa Merek --</option>
                                @foreach ($brands as $brand)
                                    <option
                                        value="{{ $brand->id }}"
                                        {{ (isset($product) && $product->brand_id == $brand->id) || old('brand_id') == $brand->id ? 'selected' : '' }}
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

                    {{-- Unique Code --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unique_code" class="form-label font-weight-semibold">
                                <i class="cil-barcode mr-1 text-muted"></i> Kode Unik
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="unique_code"
                                name="unique_code"
                                class="form-control form-control-lg second-form-control-lg @error('unique_code') is-invalid @enderror"
                                value="{{ old('unique_code', $product->unique_code ?? 'SH-' . strtoupper(substr(md5(time()), 0, 6))) }}"
                                placeholder="SH-XXXXXX"
                                required
                            >
                            @error('unique_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-info mr-1"></i>
                                Kode unik untuk tracking produk bekas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Specifications Card --}}
        <div class="card second-shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-settings mr-2 text-primary"></i>
                    Spesifikasi
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Size --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="size" class="form-label font-weight-semibold">
                                <i class="cil-resize-both mr-1 text-muted"></i> Ukuran
                            </label>
                            <input
                                type="text"
                                id="size"
                                name="size"
                                class="form-control form-control-lg second-form-control-lg @error('size') is-invalid @enderror"
                                value="{{ old('size', $product->size ?? '') }}"
                                placeholder="185/70 R14"
                            >
                            @error('size')
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
                                class="form-control form-control-lg second-form-control-lg @error('ring') is-invalid @enderror"
                                value="{{ old('ring', $product->ring ?? '') }}"
                                placeholder="14"
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
                                <i class="cil-calendar mr-1 text-muted"></i> Tahun
                            </label>
                            <input
                                type="number"
                                id="product_year"
                                name="product_year"
                                class="form-control form-control-lg second-form-control-lg @error('product_year') is-invalid @enderror"
                                value="{{ old('product_year', $product->product_year ?? '') }}"
                                placeholder="{{ date('Y') }}"
                                min="1990"
                                max="{{ date('Y') }}"
                            >
                            @error('product_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pricing Card --}}
        <div class="card second-shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-dollar mr-2 text-primary"></i>
                    Harga
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Purchase Price --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="purchase_price" class="form-label font-weight-semibold">
                                <i class="cil-arrow-circle-bottom mr-1 text-muted"></i> Harga Beli
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="purchase_price"
                                name="purchase_price"
                                class="form-control form-control-lg second-form-control-lg @error('purchase_price') is-invalid @enderror"
                                value="{{ old('purchase_price', $product->purchase_price ?? '0') }}"
                                required
                            >
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-info mr-1"></i>
                                Harga beli dari penjual lama
                            </small>
                        </div>
                    </div>

                    {{-- Selling Price --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="selling_price" class="form-label font-weight-semibold">
                                <i class="cil-arrow-circle-top mr-1 text-muted"></i> Harga Jual
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="selling_price"
                                name="selling_price"
                                class="form-control form-control-lg second-form-control-lg @error('selling_price') is-invalid @enderror"
                                value="{{ old('selling_price', $product->selling_price ?? '0') }}"
                                required
                            >
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-info mr-1"></i>
                                Harga jual ke customer
                            </small>
                        </div>
                    </div>

                    {{-- Profit Margin --}}
                    <div class="col-12">
                        <div class="alert alert-info second-alert-info" id="profitMarginAlert" style="display: none;">
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

        {{-- Condition & Status Card --}}
        <div class="card second-shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-notes mr-2 text-primary"></i>
                    Kondisi{{ isset($product) ? ' & Status' : '' }}
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Status (only in edit) --}}
                    @if (isset($product))
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status" class="form-label font-weight-semibold">
                                    <i class="cil-task mr-1 text-muted"></i> Status Produk
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    id="status"
                                    name="status"
                                    class="form-control form-control-lg second-form-control-lg @error('status') is-invalid @enderror"
                                    required
                                >
                                    <option value="available" {{ $product->status == 'available' ? 'selected' : '' }}>
                                        Tersedia
                                    </option>
                                    <option value="sold" {{ $product->status == 'sold' ? 'selected' : '' }}>
                                        Terjual
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="cil-info mr-1"></i>
                                    Status ketersediaan produk
                                </small>
                            </div>
                        </div>
                    @endif

                    {{-- Condition Notes --}}
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label for="condition_notes" class="form-label font-weight-semibold">
                                <i class="cil-pencil mr-1 text-muted"></i> Deskripsi Kondisi
                            </label>
                            <textarea
                                id="condition_notes"
                                name="condition_notes"
                                rows="4"
                                class="form-control second-form-textarea @error('condition_notes') is-invalid @enderror"
                                placeholder="Jelaskan kondisi fisik, kerusakan, atau kekurangan produk secara detail..."
                            >{{ old('condition_notes', $product->condition_notes ?? '') }}</textarea>
                            @error('condition_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-info mr-1"></i>
                                Detail kondisi untuk referensi dan transparansi ke customer
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Images --}}
    <div class="col-lg-4">
        <div class="card second-shadow-sm sticky-sidebar">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold">
                    <i class="cil-image mr-2 text-primary"></i>
                    Foto Produk Bekas
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="dropzone-container">
                    <div id="document-dropzone" class="dropzone"></div>
                </div>

                <div class="alert alert-warning second-alert-warning mt-3" role="alert">
                    <small>
                        <i class="cil-camera mr-1"></i>
                        <strong>Sangat Penting:</strong> Upload foto kondisi aktual dari berbagai sudut.
                    </small>
                </div>

                <div class="image-guidelines mt-3">
                    <p class="small font-weight-bold mb-2">
                        <i class="cil-lightbulb mr-1 text-warning"></i>
                        Tips Foto Produk Bekas:
                    </p>
                    <ul class="small text-muted mb-0">
                        <li>Ambil dari berbagai sudut</li>
                        <li>Tampilkan kondisi jelas</li>
                        <li>Foto bagian rusak/cacat (jika ada)</li>
                        <li>Gunakan pencahayaan baik</li>
                        <li>Maks 3 foto, 1MB per foto</li>
                        <li>Format: JPG, JPEG, PNG</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page_styles')
    <style>
        /* Khusus halaman Produk Bekas / Second */

        .second-shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        .second-form-control-lg {
            height: 50px;
            font-size: 1rem;
        }

        .second-form-control-lg:focus,
        .second-form-textarea:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

        .second-form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .second-alert-info {
            background-color: #e7f6fc;
            border-color: #8ad4ee;
            color: #115293;
            border-radius: 8px;
        }

        .second-alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
            border-radius: 8px;
        }

        .dropzone-container {
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .dropzone {
            min-height: 200px;
            border: none;
            background: transparent;
            padding: 20px;
        }

        .dropzone .dz-message {
            color: #6c757d;
            font-weight: 600;
        }

        .dropzone .dz-preview .dz-image {
            border-radius: 8px;
        }

        .image-guidelines ul {
            padding-left: 1.25rem;
        }

        .image-guidelines li {
            margin-bottom: 0.25rem;
        }

        @media (max-width: 992px) {
            .sticky-sidebar {
                position: relative;
                top: 0;
                margin-top: 1rem;
            }
        }
    </style>
@endpush
