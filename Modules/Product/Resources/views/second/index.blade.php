@extends('layouts.app')

@section('title', 'Daftar Produk Bekas')

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
                    <div class="card-body">
                        <a href="{{ route('products_second.create') }}" class="btn btn-primary">
                            Tambah Produk Bekas <i class="bi bi-plus"></i>
                        </a>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Merk</th>
                                        <th>Tahun</th>
                                        <th>Ukuran</th>
                                        <th>Ring</th>
                                        <th>Modal</th>
                                        <th>Harga Jual</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->brand->name ?? '-' }}</td>
                                            <td>{{ $product->product_year ?? '-' }}</td>
                                            <td>{{ $product->size ?? '-' }}</td>
                                            <td>{{ $product->ring ?? '-' }}</td>
                                            <td>{{ format_currency($product->purchase_price) }}</td>
                                            <td>{{ format_currency($product->selling_price) }}</td>
                                            <td>{{ $product->condition_notes ?? '-' }}</td>
                                            <td>
                                                @if($product->status == 'available')
                                                    <span class="badge badge-success">Tersedia</span>
                                                @else
                                                    <span class="badge badge-danger">Terjual</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('products_second.edit', $product->id) }}" class="btn btn-info btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('products_second.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus produk ini?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Belum ada data produk bekas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
