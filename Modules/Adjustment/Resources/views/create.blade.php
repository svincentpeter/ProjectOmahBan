@extends('layouts.app')

@section('title', 'Buat Pengajuan Penyesuaian Stok')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Penyesuaian Stok</a></li>
        <li class="breadcrumb-item active">Buat Pengajuan</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-file-earmark-plus mr-2"></i>
                            Form Pengajuan Penyesuaian Stok
                        </h4>

                        {{-- ⬇️ Panduan 3 Langkah --}}
                        <div class="mt-2 small">
                            <ol class="mb-0 pl-3">
                                <li>Pilih tanggal & isi alasan + keterangan singkat.</li>
                                <li>Tambah produk, isi jumlah & tipe (Penambahan/Pengurangan).</li>
                                <li>Upload bukti foto (maks 3), lalu klik <strong>Ajukan</strong>.</li>
                            </ol>
                        </div>
                        <p class="mb-0 mt-2">
                            <small>Pengajuan akan menunggu approval dari Owner.</small>
                        </p>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('adjustments.store') }}" method="POST" enctype="multipart/form-data"
                            id="adjustment-form">
                            @csrf
                            @include('adjustment::partials._form', ['isEdit' => false])

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                                        <i class="bi bi-send-fill mr-2"></i>Ajukan Penyesuaian
                                    </button>
                                    <a href="{{ route('adjustments.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="bi bi-x-circle mr-2"></i>Batal
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
