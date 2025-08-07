@extends('layouts.app')

@section('title', 'Detail Produk')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
    <li class="breadcrumb-item active">Detail</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Row: Detail Produk & Gambar --}}
    <div class="row">
        {{-- Detail Tabel --}}
        <div class="col-lg-9 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Detail Produk</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th>Kode Barang</th>
                                    <td>{{ $product->product_code }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Barang</th>
                                    <td>{{ $product->product_name }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $product->category->category_name }}</td>
                                </tr>
                                <tr>
                                    <th>Merek</th>
                                    <td>{{ $product->brand->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Ukuran / Ring / Tahun</th>
                                    <td>
                                        {{ $product->product_size ?? '-' }} /
                                        {{ $product->ring ?? '-' }} /
                                        {{ $product->product_year ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Modal</th>
                                    <td>{{ format_currency($product->product_cost) }}</td>
                                </tr>
                                <tr>
                                    <th>Harga Jual</th>
                                    <td>{{ format_currency($product->product_price) }}</td>
                                </tr>
                                <tr>
                                    <th>Stok Awal</th>
                                    <td>{{ $product->stok_awal }} {{ $product->product_unit }}</td>
                                </tr>
                                <tr>
                                    <th>Stok Sisa</th>
                                    <td>{{ $product->product_quantity }} {{ $product->product_unit }}</td>
                                </tr>
                                <tr>
                                    <th>Stok Minimum</th>
                                    <td>{{ $product->product_stock_alert }} {{ $product->product_unit }}</td>
                                </tr>
                                <tr>
                                    <th>Catatan</th>
                                    <td>{{ $product->product_note ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gambar Produk --}}
        <div class="col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Gambar Produk</strong>
                </div>
                <div class="card-body text-center">
                    @if($product->hasMedia('images'))
                        <img
                            src="{{ $product->getFirstMediaUrl('images', 'thumb') }}"
                            alt="Gambar Produk"
                            class="img-fluid img-thumbnail"
                        >
                    @else
                        <img
                            src="{{ asset('images/fallback_product_image.png') }}"
                            alt="Fallback Gambar Produk"
                            class="img-fluid img-thumbnail"
                        >
                    @endif
                </div>
            </div>
        </div>
    </div> {{-- end row --}}

    {{-- Riwayat Penyesuaian Stok --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <strong>Riwayat Penyesuaian Stok</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Referensi</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Tipe</th>
                                    <th>Catatan Penyesuaian</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($product->adjustedProducts as $adjusted)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($adjusted->adjustment->date)->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('adjustments.show', $adjusted->adjustment->id) }}">
                                                {{ $adjusted->adjustment->reference }}
                                            </a>
                                        </td>
                                        <td class="text-center">{{ $adjusted->quantity }}</td>
                                        <td class="text-center">
                                            {{-- ===== KODE PERBAIKAN DIMULAI DI SINI ===== --}}
                                            @if(strtolower($adjusted->type) == 'add')
    <span class="badge badge-success">Penambahan</span>
@else
    <span class="badge badge-danger">Pengurangan</span>
@endif
                                            {{-- ===== KODE PERBAIKAN SELESAI DI SINI ===== --}}
                                        </td>
                                        <td>{{ $adjusted->adjustment->note ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Belum ada riwayat penyesuaian stok untuk produk ini.
                                        </td>
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
