@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
    <li class="breadcrumb-item active">Tambah</li>
</ol>
@endsection

@section('content')
<div class="container-fluid mb-4">
    <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                <div class="form-group">
                    <button class="btn btn-primary">Buat Produk <i class="bi bi-check"></i></button>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_name">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="product_name" required value="{{ old('product_name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_code">Kode Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="product_code" required value="{{ old('product_code', 'PRD-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control" name="category_id" id="category_id" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand_id">Merek</label>
                                    <select class="form-control" name="brand_id" id="brand_id">
                                        <option value="">Tanpa Merek</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product_size">Ukuran</label>
                                    <input type="text" class="form-control" name="product_size" placeholder="Contoh: 235/75 R15" value="{{ old('product_size') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ring">Ring</label>
                                    <input type="text" class="form-control" name="ring" placeholder="Contoh: 15" value="{{ old('ring') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product_year">Tahun Produksi</label>
                                    <input type="number" class="form-control" name="product_year" placeholder="Contoh: {{ date('Y') }}" value="{{ old('product_year') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_cost">Modal <span class="text-danger">*</span></label>
                                    <input id="product_cost" type="text" class="form-control" name="product_cost" required value="{{ old('product_cost', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_price">Harga Jual <span class="text-danger">*</span></label>
                                    <input id="product_price" type="text" class="form-control" name="product_price" required value="{{ old('product_price', 0) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stok_awal">Stok Awal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="stok_awal" required value="{{ old('stok_awal', 0) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product_stock_alert">Stok Minimum <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="product_stock_alert" required value="{{ old('product_stock_alert', 4) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product_unit">Unit Satuan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="product_unit" id="product_unit" required>
                                        @foreach(\Modules\Setting\Entities\Unit::all() as $unit)
                                        <option value="{{ $unit->short_name }}" {{ old('product_unit') == $unit->short_name ? 'selected' : '' }}>
                                            {{ $unit->name }} | {{ $unit->short_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="product_note">Catatan</label>
                            <textarea name="product_note" id="product_note" rows="4" class="form-control" placeholder="Catatan tambahan (opsional)">{{ old('product_note') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IMAGE UPLOAD COMPONENT - SUPER SIMPLE! --}}
            <x-image-upload 
                max-files="3" 
                label="Gambar Produk" 
                max-size="2"
                help-text="Upload maksimal 3 gambar produk dengan ukuran maks 2MB per file"
            />
        </div>
    </form>
</div>
@endsection

@section('third_party_scripts')
<script src="{{ asset('js/dropzone.js') }}"></script>
@endsection

@push('page_scripts')
<script src="{{ asset('js/jquery-mask-money.js') }}"></script>
<script>
    $(document).ready(function() {
        // Mask money for currency fields
        $('#product_cost, #product_price').maskMoney({
            prefix: '{{ settings()->currency->symbol }}',
            thousands: '{{ settings()->currency->thousand_separator }}',
            decimal: '{{ settings()->currency->decimal_separator }}',
        });

        $('#product_cost, #product_price').maskMoney('mask');

        // Unmask before submit
        $('#product-form').submit(function() {
            $('#product_cost').val($('#product_cost').maskMoney('unmasked')[0]);
            $('#product_price').val($('#product_price').maskMoney('unmasked')[0]);
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
