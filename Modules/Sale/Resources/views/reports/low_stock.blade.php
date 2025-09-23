@extends('layouts.app')

@section('title','Laporan Stok Menipis')
@section('content')
<div class="container py-3">
  <h4 class="mb-3">Produk dengan Stok ≤ {{ $limit }}</h4>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
      <tr>
        <th>Kode</th><th>Nama</th><th>Kategori</th><th>Merek</th><th>Ukuran</th><th>Stok</th><th>Batas Alert</th>
      </tr>
      </thead>
      <tbody>
      @forelse($products as $p)
        <tr>
          <td>{{ $p->product_code }}</td>
          <td>{{ $p->product_name }}</td>
          <td>{{ optional($p->category)->name }}</td>
          <td>{{ optional($p->brand)->name }}</td>
          <td>{{ $p->product_size }}</td>
          <td>{{ $p->product_quantity }}</td>
          <td>{{ $p->product_stock_alert }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted">Tidak ada item di bawah batas.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
