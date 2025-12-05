@extends('layouts.app')

@section('title', 'Buat Stock Opname Baru')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-opnames.index') }}">Stock Opname</a></li>
        <li class="breadcrumb-item active">Buat Baru</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- HEADER CARD --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-clipboard-check"></i> Buat Stock Opname Baru
                    </h4>
                    <small class="d-block mt-1">Penghitungan fisik stok untuk mencocokkan dengan data sistem</small>
                </div>

                <div class="card-body">
                    {{-- INFO ALERT --}}
                    <div class="alert alert-info border-left-info mb-4" role="alert">
                        <div class="d-flex">
                            <div class="mr-3">
                                <i class="bi bi-info-circle-fill" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Apa itu Stock Opname?</h5>
                                <p class="mb-0">
                                    Stock opname adalah proses penghitungan fisik seluruh atau sebagian produk di gudang/toko. 
                                    Hasil hitungan akan dibandingkan dengan stok di sistem. 
                                    Jika ada <strong>selisih (variance)</strong>, sistem akan otomatis membuat 
                                    <strong>Adjustment</strong> yang perlu disetujui.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- INCLUDE PARTIAL FORM --}}
                    @include('adjustment::stock-opname.partials._form', [
                        'isEdit' => false,
                        'stockOpname' => null,
                        'categories' => $categories
                    ])
                </div>
            </div>

            {{-- TIPS CARD --}}
            <div class="card border-left-success shadow-sm">
                <div class="card-body">
                    <h6 class="text-success mb-3">
                        <i class="bi bi-lightbulb-fill"></i> Tips Stock Opname
                    </h6>
                    <ul class="mb-0 small">
                        <li class="mb-2">
                            <strong>Pilih waktu yang tepat:</strong> Lakukan saat toko tutup atau stok tidak banyak bergerak
                        </li>
                        <li class="mb-2">
                            <strong>Per kategori lebih mudah:</strong> Untuk toko ban & velg, bisa opname per kategori dulu (Ban minggu ini, Velg minggu depan)
                        </li>
                        <li class="mb-2">
                            <strong>Siapkan barcode scanner:</strong> Akan mempercepat proses counting (opsional)
                        </li>
                        <li>
                            <strong>Tim 2 orang:</strong> 1 orang hitung fisik, 1 orang input ke sistem untuk akurasi lebih baik
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-info {
        border-left: 4px solid #36b9cc;
    }

    .border-left-success {
        border-left: 4px solid #1cc88a;
    }
</style>
@endsection
