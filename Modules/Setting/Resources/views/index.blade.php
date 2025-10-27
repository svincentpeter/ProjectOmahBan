@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Pengaturan</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Page Header --}}
            <div class="mb-4">
                <h3 class="mb-1 font-weight-bold">
                    <i class="cil-settings mr-2 text-primary"></i>
                    Pengaturan Sistem
                </h3>
                <p class="text-muted mb-0">Kelola informasi dasar perusahaan dan konfigurasi sistem</p>
            </div>

            <div class="row">
                {{-- Main Settings Form --}}
                <div class="col-12 col-xl-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-building mr-2 text-primary"></i>
                                Informasi Perusahaan
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Error Messages --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-warning mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                        <div class="flex-grow-1">
                                            <strong>Periksa kembali input Anda:</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($errors->all() as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Form --}}
                            <form action="{{ route('settings.update') }}" method="POST" autocomplete="off">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    {{-- Company Name --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-semibold" for="company_name">
                                            <i class="cil-building mr-1 text-muted"></i> Nama Perusahaan
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="company_name" name="company_name"
                                            class="form-control @error('company_name') is-invalid @enderror"
                                            value="{{ old('company_name', $settings->company_name) }}" required>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Company Email --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-semibold" for="company_email">
                                            <i class="cil-envelope-closed mr-1 text-muted"></i> Email Perusahaan
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" id="company_email" name="company_email"
                                            class="form-control @error('company_email') is-invalid @enderror"
                                            value="{{ old('company_email', $settings->company_email) }}" required>
                                        @error('company_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Company Phone --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-semibold" for="company_phone">
                                            <i class="cil-phone mr-1 text-muted"></i> Telepon Perusahaan
                                        </label>
                                        <input type="text" id="company_phone" name="company_phone"
                                            class="form-control @error('company_phone') is-invalid @enderror"
                                            value="{{ old('company_phone', $settings->company_phone) }}">
                                        @error('company_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Notification Email --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-semibold" for="notification_email">
                                            <i class="cil-bell mr-1 text-muted"></i> Email Notifikasi
                                        </label>
                                        <input type="email" id="notification_email" name="notification_email"
                                            class="form-control @error('notification_email') is-invalid @enderror"
                                            value="{{ old('notification_email', $settings->notification_email) }}">
                                        @error('notification_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Company Address --}}
                                    <div class="col-12 mb-3">
                                        <label class="form-label font-weight-semibold" for="company_address">
                                            <i class="cil-location-pin mr-1 text-muted"></i> Alamat Perusahaan
                                        </label>
                                        <textarea id="company_address" name="company_address" rows="3"
                                            class="form-control @error('company_address') is-invalid @enderror">{{ old('company_address', $settings->company_address) }}</textarea>
                                        @error('company_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Currency Settings --}}
                                    <div class="col-12 mb-3">
                                        <hr class="my-4">
                                        <h6 class="mb-3 font-weight-bold">
                                            <i class="cil-dollar mr-2 text-primary"></i>
                                            Pengaturan Mata Uang
                                        </h6>
                                    </div>

                                    {{-- Default Currency --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-semibold" for="default_currency_id">
                                            <i class="cil-wallet mr-1 text-muted"></i> Mata Uang Default
                                        </label>
                                        <select id="default_currency_id" class="form-control" disabled>
                                            @foreach (\Modules\Currency\Entities\Currency::query()->orderBy('currency_name')->get() as $currency)
                                                <option value="{{ $currency->id }}"
                                                    {{ old('default_currency_id', $settings->default_currency_id) == $currency->id ? 'selected' : '' }}>
                                                    {{ $currency->currency_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="default_currency_id"
                                            value="{{ old('default_currency_id', $settings->default_currency_id) }}">
                                        <small class="form-text text-muted">
                                            <i class="cil-lock-locked mr-1"></i>
                                            Dikunci untuk konsistensi sistem. Format: <strong>Rp 100.000</strong>
                                        </small>
                                    </div>

                                    {{-- Currency Position --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-semibold" for="default_currency_position">
                                            <i class="cil-arrows-vertical mr-1 text-muted"></i> Posisi Simbol
                                        </label>
                                        <select id="default_currency_position" class="form-control" disabled>
                                            <option selected>Prefix (Rp 100.000)</option>
                                        </select>
                                        <input type="hidden" name="default_currency_position" value="prefix">
                                        <small class="form-text text-muted">
                                            <i class="cil-lock-locked mr-1"></i>
                                            Dikunci agar format tetap konsisten
                                        </small>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">
                                        <i class="cil-x mr-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="cil-save mr-1"></i> Simpan Pengaturan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Info Sidebar --}}
                <div class="col-12 col-xl-4">
                    {{-- Currency Format Info --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-info mr-2 text-primary"></i>
                                Format Mata Uang
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="info-list">
                                <div class="info-item">
                                    <i class="cil-dollar text-primary"></i>
                                    <div class="info-content">
                                        <div class="info-label">Simbol</div>
                                        <div class="info-value">Rp</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="cil-arrow-right text-primary"></i>
                                    <div class="info-content">
                                        <div class="info-label">Posisi</div>
                                        <div class="info-value">Prefix (di depan)</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="cil-list text-primary"></i>
                                    <div class="info-content">
                                        <div class="info-label">Pemisah Ribuan</div>
                                        <div class="info-value">. (titik)</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="cil-blur-circular text-primary"></i>
                                    <div class="info-content">
                                        <div class="info-label">Desimal</div>
                                        <div class="info-value">Tidak ditampilkan (0)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-light mt-3 mb-0" role="alert">
                                <strong>Penggunaan di Blade:</strong>
                                <div class="mt-2">
                                    <code class="d-block mb-1">@{{ format_currency(100000) }}</code>
                                    <code class="d-block mb-1">@{{ money(100000) }}</code>
                                </div>
                                <div class="mt-2">
                                    <i class="cil-arrow-right mr-1"></i>
                                    <strong>Rp 100.000</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tips Card --}}
                    <div class="card shadow-sm border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="tips-icon mr-3">
                                    <i class="cil-lightbulb"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-2">Tips</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.875rem;">
                                        Pastikan informasi perusahaan selalu update agar muncul dengan benar
                                        di invoice, laporan, dan dokumen lainnya.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* ========== Animations ========== */
        .animated.fadeIn {
            animation: fadeIn 0.3s ease-in;
        }

        @@keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========== Card Shadow ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Form Enhancements ========== */
        .form-control {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        /* ========== Info List ========== */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .info-item>i {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
            margin-left: 0.75rem;
        }

        .info-label {
            font-size: 0.8125rem;
            color: #6c757d;
            font-weight: 500;
        }

        .info-value {
            font-size: 0.9375rem;
            color: #1f2937;
            font-weight: 700;
            margin-top: 0.125rem;
        }

        /* ========== Tips Icon ========== */
        .tips-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.25);
        }

        /* ========== Alert Styling ========== */
        .alert-light {
            background-color: #f8f9fa;
            border-color: #e9ecef;
        }

        .alert-light code {
            background-color: #ffffff;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            border: 1px solid #e9ecef;
            font-size: 0.8125rem;
        }

        /* ========== Border Primary Card ========== */
        .border-primary {
            border-left: 4px solid #4834DF !important;
        }

        /* ========== Responsive ========== */
        @@media (max-width: 768px) {
            .tips-icon {
                width: 40px;
                height: 40px;
                font-size: 1.25rem;
            }

            .info-item {
                padding: 0.5rem;
            }

            .info-item>i {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }
        }
    </style>
@endpush
