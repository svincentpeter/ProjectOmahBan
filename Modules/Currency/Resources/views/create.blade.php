@extends('layouts.app')

@section('title', 'Tambah Mata Uang')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('currencies.index') }}">Mata Uang</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('currencies.store') }}" method="POST" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                <div class="form-group mb-3">
                    <button class="btn btn-primary">Simpan <i class="bi bi-check"></i></button>
                    <a href="{{ route('currencies.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <div class="form-row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="currency_name">Nama Mata Uang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="currency_name" name="currency_name"
                                           value="{{ old('currency_name') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="code">Kode (ISO 4217) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" id="code" name="code"
                                           maxlength="3" value="{{ old('code') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="symbol">Simbol <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="symbol" name="symbol"
                                           value="{{ old('symbol', 'Rp') }}" required>
                                    <small class="text-muted">Tampilan sistem diseragamkan: <strong>Rp 100.000</strong>.</small>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="thousand_separator">Pemisah Ribuan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-center" id="thousand_separator"
                                           name="thousand_separator" maxlength="1" value="{{ old('thousand_separator', '.') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="decimal_separator">Pemisah Desimal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-center" id="decimal_separator"
                                           name="decimal_separator" maxlength="1" value="{{ old('decimal_separator', ',') }}" required>
                                    <small class="text-muted">Tidak ditampilkan di UI karena menggunakan 0 desimal.</small>
                                </div>
                            </div>
                        </div>

                    </div> {{-- card-body --}}
                </div> {{-- card --}}
            </div> {{-- col --}}
        </div> {{-- row --}}
    </form>
</div>
@endsection
