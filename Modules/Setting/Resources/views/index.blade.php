@extends('layouts.app')

@section('title', 'Pengaturan')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active">Pengaturan</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Pengaturan Umum</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <div class="fw-bold mb-1">Periksa kembali input Anda:</div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="company_name">Nama Perusahaan</label>
                                <input type="text" id="company_name" name="company_name"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       value="{{ old('company_name', $settings->company_name) }}" required>
                                @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="company_email">Email Perusahaan</label>
                                <input type="email" id="company_email" name="company_email"
                                       class="form-control @error('company_email') is-invalid @enderror"
                                       value="{{ old('company_email', $settings->company_email) }}" required>
                                @error('company_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="company_phone">Telepon Perusahaan</label>
                                <input type="text" id="company_phone" name="company_phone"
                                       class="form-control @error('company_phone') is-invalid @enderror"
                                       value="{{ old('company_phone', $settings->company_phone) }}">
                                @error('company_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="notification_email">Email Notifikasi</label>
                                <input type="email" id="notification_email" name="notification_email"
                                       class="form-control @error('notification_email') is-invalid @enderror"
                                       value="{{ old('notification_email', $settings->notification_email) }}">
                                @error('notification_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="company_address">Alamat Perusahaan</label>
                                <textarea id="company_address" name="company_address" rows="3"
                                          class="form-control @error('company_address') is-invalid @enderror">{{ old('company_address', $settings->company_address) }}</textarea>
                                @error('company_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Mata uang (dikunci) --}}
                            <div class="col-md-6">
                                <label class="form-label" for="default_currency_id">Mata Uang Default</label>
                                <select id="default_currency_id" class="form-control" disabled>
                                    @foreach(\Modules\Currency\Entities\Currency::query()->orderBy('currency_name')->get() as $currency)
                                        <option value="{{ $currency->id }}"
                                            {{ (old('default_currency_id', $settings->default_currency_id) == $currency->id) ? 'selected' : '' }}>
                                            {{ $currency->currency_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="default_currency_id" value="{{ old('default_currency_id', $settings->default_currency_id) }}">
                                <small class="text-muted">Dikunci. Tampilan uang: <strong>Rp 100.000</strong>.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="default_currency_position">Posisi Simbol</label>
                                <select id="default_currency_position" class="form-control" disabled>
                                    <option selected>Prefix (Rp 100.000)</option>
                                </select>
                                <input type="hidden" name="default_currency_position" value="prefix">
                                <small class="text-muted">Dikunci agar konsisten.</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kartu info --}}
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Info Format Uang</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-2">
                        <li>Simbol: <strong>Rp</strong></li>
                        <li>Posisi: <strong>Prefix</strong> (di depan)</li>
                        <li>Pemisah ribuan: <strong>.</strong></li>
                        <li>Desimal: <strong>0</strong> (tidak ditampilkan)</li>
                    </ul>
                    <p class="mb-0">
                        Di Blade gunakan: <code>@{{ rupiah(100000) }}</code> atau
                        <code>@{{ money(100000) }}</code> → <strong>Rp 100.000</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
