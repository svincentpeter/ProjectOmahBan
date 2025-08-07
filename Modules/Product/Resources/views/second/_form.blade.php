<div class="card">
    <div class="card-body">
        {{-- Baris 1: Nama & Kategori --}}
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required value="{{ old('name', $product->name ?? '') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="category_id">Kategori <span class="text-danger">*</span></label>
                    <select class="form-control" name="category_id" id="category_id" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (isset($product) && $product->category_id == $category->id) || old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Baris 2: Merek & Kode Unik --}}
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="brand_id">Merek</label>
                    <select class="form-control" name="brand_id" id="brand_id">
                        <option value="">Tanpa Merek</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ (isset($product) && $product->brand_id == $brand->id) || old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="unique_code">Kode Unik <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="unique_code" required value="{{ old('unique_code', $product->unique_code ?? '') }}">
                </div>
            </div>
        </div>

        {{-- Baris 3: Ukuran, Ring, Tahun --}}
        <div class="form-row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="size">Ukuran</label>
                    <input type="text" class="form-control" name="size" placeholder="Contoh: 235/75 R15" value="{{ old('size', $product->size ?? '') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="ring">Ring</label>
                    <input type="text" class="form-control" name="ring" placeholder="Contoh: 15" value="{{ old('ring', $product->ring ?? '') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="product_year">Tahun Produksi</label>
                    <input type="number" class="form-control" name="product_year" placeholder="Contoh: 2020" value="{{ old('product_year', $product->product_year ?? '') }}">
                </div>
            </div>
        </div>

        {{-- Baris 4: Modal & Harga Jual --}}
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="purchase_price">Modal (Harga Beli) <span class="text-danger">*</span></label>
                    <input id="purchase_price" type="text" class="form-control" name="purchase_price" required value="{{ old('purchase_price', $product->purchase_price ?? '') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="selling_price">Harga Jual <span class="text-danger">*</span></label>
                    <input id="selling_price" type="text" class="form-control" name="selling_price" required value="{{ old('selling_price', $product->selling_price ?? '') }}">
                </div>
            </div>
        </div>

        {{-- Baris 5: Deskripsi Kondisi --}}
        <div class="form-group">
            <label for="condition_notes">Deskripsi Kondisi</label>
            <textarea class="form-control" name="condition_notes" id="condition_notes" rows="4">{{ old('condition_notes', $product->condition_notes ?? '') }}</textarea>
        </div>

        {{-- Hanya tampil di halaman edit --}}
        @if(isset($product))
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select class="form-control" name="status" id="status" required>
                <option value="available" {{ $product->status == 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="sold" {{ $product->status == 'sold' ? 'selected' : '' }}>Terjual</option>
            </select>
        </div>
        @endif
    </div>
</div>

{{-- Card baru untuk upload gambar --}}
<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label for="document">Gambar Produk <i class="bi bi-question-circle-fill text-info"
                data-toggle="tooltip" data-placement="top"
                title="Maks. 3 file, ukuran maks. 1MB, format: .jpg, .jpeg, .png"></i></label>
            <div class="dropzone d-flex flex-wrap align-items-center justify-content-center" id="document-dropzone">
                <div class="dz-message" data-dz-message>
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>
