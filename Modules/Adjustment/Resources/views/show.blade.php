@extends('layouts.app')

@section('title', 'Adjustment Details')

@push('page_css')
    @livewireStyles
@endpush

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Adjustments</a></li>
    <li class="breadcrumb-item active">Details</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- Header dengan Referensi & Tombol Cetak --}}
                <div class="card-header d-flex align-items-center">
                    <div>
                        Referensi: <strong>{{ $adjustment->reference }}</strong>
                    </div>
                    <a
                        href="{{ route('adjustments.pdf', $adjustment->id) }}"
                        target="_blank"
                        class="btn btn-sm btn-secondary ms-auto d-print-none"
                    >
                        <i class="bi bi-printer"></i> Cetak
                    </a>
                </div>

                <div class="card-body">
                    {{-- Info Penyesuaian --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h5 class="mb-3 border-bottom pb-2">Info Penyesuaian</h5>
                            <p><strong>Tanggal:</strong>
                                {{ \Carbon\Carbon::parse($adjustment->date)->format('d M Y') }}
                            </p>
                            <p><strong>Referensi:</strong> {{ $adjustment->reference }}</p>
                            <p class="mt-2">
                                <strong>Catatan:</strong><br>
                                {{ $adjustment->note ?? 'Tidak ada catatan.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Tabel Produk yang Disesuaikan --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Code</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjustment->adjustedProducts as $item)
                                    <tr>
                                        <td>{{ $item->product->product_name }}</td>
                                        <td>{{ $item->product->product_code }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">
                                            @if($item->type === 'add')
                                                <span class="badge bg-success">+ Addition</span>
                                            @else
                                                <span class="badge bg-danger">– Subtraction</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            Tidak ada produk yang disesuaikan.
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
