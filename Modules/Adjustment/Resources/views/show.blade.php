@extends('layouts.app')

@section('title', 'Detail Penyesuaian Stok')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Penyesuaian Stok</a></li>
    <li class="breadcrumb-item active">{{ $adjustment->reference }}</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text text-primary"></i> 
                        Detail Penyesuaian Stok #{{ $adjustment->reference }}
                    </h5>
                    <div>
                        <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('adjustments.pdf', $adjustment->id) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="bi bi-printer"></i> Print PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Info Header --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h6 class="text-muted mb-2">
                                    <i class="bi bi-calendar3"></i> Tanggal
                                </h6>
                                <p class="mb-0 font-weight-bold">{{ \Carbon\Carbon::parse($adjustment->date)->format('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h6 class="text-muted mb-2">
                                    <i class="bi bi-hash"></i> Referensi
                                </h6>
                                <p class="mb-0 font-weight-bold">{{ $adjustment->reference }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h6 class="text-muted mb-2">
                                    <i class="bi bi-box-seam"></i> Total Produk
                                </h6>
                                <p class="mb-0 font-weight-bold">{{ $adjustment->adjustedProducts->count() }} Produk</p>
                            </div>
                        </div>
                    </div>

                    @if($adjustment->note)
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-sticky"></i> Catatan
                        </h6>
                        <p class="mb-0">{{ $adjustment->note }}</p>
                    </div>
                    @endif

                    {{-- Products Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="35%">Produk</th>
                                    <th width="15%">Kode</th>
                                    <th width="15%" class="text-center">Jumlah</th>
                                    <th width="15%" class="text-center">Tipe</th>
                                    <th width="15%" class="text-right">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjustment->adjustedProducts as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->product->product_name }}</strong>
                                        @if($item->product->category)
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-tag"></i> {{ $item->product->category->category_name }}
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $item->product->product_code }}</code>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $item->type == 'add' ? 'success' : 'danger' }} badge-pill" style="font-size: 1rem;">
                                            {{ $item->quantity }} {{ $item->product->product_unit }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->type == 'add')
                                            <span class="badge badge-success">
                                                <i class="bi bi-plus-circle"></i> Penambahan
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="bi bi-dash-circle"></i> Pengurangan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <small class="text-muted">
                                            Stok saat ini: <strong>{{ $item->product->product_quantity }} {{ $item->product->product_unit }}</strong>
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Tidak ada produk yang disesuaikan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Statistics --}}
                    @if($adjustment->adjustedProducts->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-arrow-up-circle"></i> Total Penambahan
                                    </h6>
                                    <h3 class="mb-0">
                                        {{ $adjustment->adjustedProducts->where('type', 'add')->count() }} Produk
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-arrow-down-circle"></i> Total Pengurangan
                                    </h6>
                                    <h3 class="mb-0">
                                        {{ $adjustment->adjustedProducts->where('type', 'sub')->count() }} Produk
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
