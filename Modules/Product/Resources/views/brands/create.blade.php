@extends('layouts.app')
@section('title', 'Tambah Merek Baru')
@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
        <li class="breadcrumb-item"><a href="{{ route('brands.index') }}">Merek</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('brands.store') }}" method="POST">
            @csrf
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card">
                         <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nama Merek <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan <i class="bi bi-check"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection