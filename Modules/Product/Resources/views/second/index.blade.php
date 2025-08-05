@extends('layouts.app')

@section('title', 'Produk Bekas')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Produk Bekas</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('products_second.create') }}" class="btn btn-primary">
                            Tambah Produk Bekas <i class="bi bi-plus"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode Unik</th>
                                        <th>Nama Produk</th>
                                        <th>Harga Beli (HPP)</th>
                                        <th>Harga Jual</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $product->unique_code }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ format_currency($product->purchase_price) }}</td>
                                            <td>{{ format_currency($product->selling_price) }}</td>
                                            <td>
                                                @if($product->status == 'available')
                                                    <span class="badge badge-success">Tersedia</span>
                                                @else
                                                    <span class="badge badge-danger">Terjual</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- Tombol Edit & Hapus bisa ditambahkan di sini nanti --}}
                                                <a href="#" class="btn btn-info btn-sm">Edit</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data produk bekas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection