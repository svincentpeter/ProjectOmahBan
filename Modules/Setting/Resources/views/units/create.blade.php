@extends('layouts.app')

@section('title', 'Tambah Satuan')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('units.index') }}">Satuan</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('units.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                <div class="form-group mb-3 d-flex gap-2">
                    <button class="btn btn-primary">
                        <i class="bi bi-check me-1"></i> Simpan
                    </button>
                    <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Satuan <span class="text-danger">*</span></label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Contoh: Buah, Pcs, Set, Lembar.</small>
                            </div>

                            <div class="col-md-6">
                                <label for="short_name" class="form-label">Singkatan <span class="text-danger">*</span></label>
                                <input type="text"
                                       id="short_name"
                                       name="short_name"
                                       class="form-control @error('short_name') is-invalid @enderror"
                                       value="{{ old('short_name') }}"
                                       maxlength="10"
                                       required>
                                @error('short_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maks 10 karakter. Contoh: pcs, set, lbr.</small>
                            </div>

                            <div class="col-12">
                                <label for="note" class="form-label">Keterangan (opsional)</label>
                                <textarea id="note"
                                          name="note"
                                          rows="2"
                                          class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> {{-- row --}}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
