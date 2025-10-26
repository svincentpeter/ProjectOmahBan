@extends('layouts.app')

@section('title', 'Edit Produk')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>
@endsection

@section('content')
<div class="container-fluid mb-4">
    <form id="product-form" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                <div class="form-group">
                    <button class="btn btn-primary">Update Produk <i class="bi bi-check"></i></button>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_name">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="product_name" required value="{{ old('product_name', $product->product_name) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_code">Kode Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="product_code" required value="{{ old('product_code', $product->product_code) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control" name="category_id" id="category_id" required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
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
                                    <input type="text" class="form-control" name="product_size" placeholder="Contoh: 235/75 R15" value="{{ old('product_size', $product->product_size) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ring">Ring</label>
                                    <input type="text" class="form-control" name="ring" placeholder="Contoh: 15" value="{{ old('ring', $product->ring) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product_year">Tahun Produksi</label>
                                    <input type="number" class="form-control" name="product_year" placeholder="Contoh: {{ date('Y') }}" value="{{ old('product_year', $product->product_year) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_cost">Modal <span class="text-danger">*</span></label>
                                    <input id="product_cost" type="text" class="form-control" name="product_cost" required value="{{ old('product_cost', $product->product_cost) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_price">Harga Jual <span class="text-danger">*</span></label>
                                    <input id="product_price" type="text" class="form-control" name="product_price" required value="{{ old('product_price', $product->product_price) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stok_awal">Stok Awal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="stok_awal" required value="{{ old('stok_awal', $product->stok_awal ?? 0) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product_quantity">Stok Sisa</label>
                                    <input type="number" class="form-control" name="product_quantity" value="{{ $product->product_quantity }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product_stock_alert">Stok Minimum <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="product_stock_alert" required value="{{ old('product_stock_alert', $product->product_stock_alert) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_unit">Unit Satuan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="product_unit" id="product_unit" required>
                                        @foreach(\Modules\Setting\Entities\Unit::all() as $unit)
                                        <option {{ old('product_unit', $product->product_unit) == $unit->short_name ? 'selected' : '' }} value="{{ $unit->short_name }}">
                                            {{ $unit->name }} | {{ $unit->short_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_note">Catatan</label>
                                    <textarea name="product_note" id="product_note" rows="2" class="form-control">{{ old('product_note', $product->product_note) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IMAGE UPLOAD COMPONENT WITH EXISTING PRODUCT - SUPER SIMPLE! --}}
            <x-image-upload 
                :model="$product" 
                max-files="3" 
                label="Gambar Produk" 
                max-size="2"
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
        // Mask money
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
