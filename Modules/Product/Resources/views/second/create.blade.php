@extends('layouts.app')

@section('title', 'Tambah Produk Bekas')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products_second.index') }}">Produk Bekas</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('products_second.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nama Produk <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unique_code">Kode Unik <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="unique_code" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="purchase_price">Harga Beli (HPP) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="purchase_price" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="selling_price">Harga Jual <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="selling_price" required>
                                    </div>
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="condition_notes">Catatan Kondisi</label>
                                <textarea class="form-control" name="condition_notes" rows="4"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="photo">Foto Produk</label>
                                <input type="file" class="form-control-file" name="photo">
                            </div>
                        </div>
                        <div class="card-footer">
                             <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection