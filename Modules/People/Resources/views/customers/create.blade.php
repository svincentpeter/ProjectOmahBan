@extends('layouts.app')

@section('title', 'Tambah Customer')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customer</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- LEFT COLUMN: Form Input --}}
                    <div class="col-lg-8">
                        {{-- Informasi Customer --}}
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="cil-people mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Informasi Customer</h5>
                                        <small class="text-muted">Lengkapi data customer untuk transaksi penjualan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Nama Customer --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_name"
                                                class="form-label small font-weight-semibold text-dark">
                                                Nama Customer <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('customer_name') is-invalid @enderror"
                                                name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                                                placeholder="Contoh: Budi Santoso" required>
                                            @error('customer_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_email"
                                                class="form-label small font-weight-semibold text-dark">
                                                Email <span class="text-danger">*</span>
                                            </label>
                                            <input type="email"
                                                class="form-control @error('customer_email') is-invalid @enderror"
                                                name="customer_email" id="customer_email"
                                                value="{{ old('customer_email') }}" placeholder="email@customer.com"
                                                required>
                                            @error('customer_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- No. Telepon --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_phone"
                                                class="form-label small font-weight-semibold text-dark">
                                                No. Telepon <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('customer_phone') is-invalid @enderror"
                                                name="customer_phone" id="customer_phone"
                                                value="{{ old('customer_phone') }}"
                                                placeholder="08123456789 atau (021) 1234567" required>
                                            @error('customer_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Format: 08xxx atau +62-xxx atau (021) xxx</small>
                                        </div>
                                    </div>

                                    {{-- Kota --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="city" class="form-label small font-weight-semibold text-dark">
                                                Kota <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                                name="city" id="city" value="{{ old('city') }}"
                                                placeholder="Contoh: Jakarta, Bandung, Surabaya" required>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Negara (Default Indonesia) --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="country" class="form-label small font-weight-semibold text-dark">
                                                Negara
                                            </label>
                                            <select class="form-control @error('country') is-invalid @enderror"
                                                name="country" id="country">
                                                <option value="Indonesia"
                                                    {{ old('country', 'Indonesia') == 'Indonesia' ? 'selected' : '' }}>
                                                    Indonesia</option>
                                                <option value="Malaysia"
                                                    {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                                <option value="Singapura"
                                                    {{ old('country') == 'Singapura' ? 'selected' : '' }}>Singapura
                                                </option>
                                                <option value="Brunei" {{ old('country') == 'Brunei' ? 'selected' : '' }}>
                                                    Brunei</option>
                                            </select>
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Default: Indonesia (untuk UMKM lokal)</small>
                                        </div>
                                    </div>

                                    {{-- Alamat Lengkap --}}
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label for="address" class="form-label small font-weight-semibold text-dark">
                                                Alamat Lengkap <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="address" rows="3"
                                                placeholder="Alamat lengkap customer (minimal 10 karakter)" required>{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Contoh: Jl. Raya Bekasi No. 123, RT 01/RW 02,
                                                Kelurahan, Kecamatan</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Info & Action --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm sticky-top" style="top: 90px;">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="cil-info mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Informasi</h5>
                                        <small class="text-muted">Panduan pengisian data customer</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Info Box --}}
                                <div class="alert alert-info mb-3" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-lightbulb mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <strong>Tips Pengisian:</strong>
                                            <ul class="mb-0 mt-2 pl-3" style="font-size: 0.875rem;">
                                                <li>Pastikan email aktif untuk komunikasi</li>
                                                <li>No. telepon bisa format lokal atau internasional</li>
                                                <li>Alamat harus lengkap minimal 10 karakter</li>
                                                <li>Data customer bisa diupdate kapan saja</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Validation Summary --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="cil-warning mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                            <div>
                                                <strong>Terdapat kesalahan:</strong>
                                                <ul class="mb-0 mt-2 pl-3" style="font-size: 0.875rem;">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <hr>

                                {{-- Submit Buttons --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="cil-save mr-1"></i> Simpan Customer
                                    </button>
                                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                        <i class="cil-x mr-1"></i> Batal
                                    </a>
                                </div>

                                {{-- Additional Info --}}
                                <div class="mt-3 p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-2">
                                        <i class="cil-shield-alt mr-1"></i> <strong>Keamanan Data</strong>
                                    </small>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        Semua data customer akan disimpan dengan aman dan hanya dapat diakses oleh user yang
                                        memiliki izin.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- row --}}
            </form>
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* ========== Animations ========== */
        .animated.fadeIn {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
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
        .form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.15);
        }

        .form-label {
            margin-bottom: 0.5rem;
        }

        /* ========== Alert Styling ========== */
        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-info {
            background-color: #e7f3ff;
            color: #004085;
        }

        .alert-danger {
            background-color: #ffe7e7;
            color: #721c24;
        }

        /* ========== Sticky Sidebar ========== */
        @media (min-width: 992px) {
            .sticky-top {
                position: sticky;
                top: 90px;
                z-index: 10;
            }
        }

        /* ========== Button Styling ========== */
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(72, 52, 223, 0.3);
        }

        /* ========== Responsive ========== */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .btn-lg {
                padding: 0.6rem 1.25rem;
                font-size: 0.95rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Auto-format phone number (optional enhancement)
            $('#customer_phone').on('blur', function() {
                let phone = $(this).val().trim();
                // Basic validation: hanya angka, +, -, (, ), dan spasi
                const regex = /^[0-9+\-\s\(\)]+$/;

                if (phone && !regex.test(phone)) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after(
                            '<div class="invalid-feedback">Format nomor telepon tidak valid</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });

            // Auto-capitalize city name
            $('#city').on('blur', function() {
                let city = $(this).val().trim();
                if (city) {
                    // Capitalize setiap kata
                    city = city.toLowerCase().replace(/\b\w/g, function(char) {
                        return char.toUpperCase();
                    });
                    $(this).val(city);
                }
            });

            // Form validation before submit
            $('form').on('submit', function(e) {
                let valid = true;

                // Check address length
                const address = $('#address').val().trim();
                if (address.length < 10) {
                    e.preventDefault();
                    $('#address').addClass('is-invalid');
                    if (!$('#address').next('.invalid-feedback').length) {
                        $('#address').after(
                            '<div class="invalid-feedback">Alamat minimal 10 karakter untuk memastikan kelengkapan</div>'
                            );
                    }
                    valid = false;
                }

                if (!valid) {
                    // Scroll to first error
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);

                    return false;
                }

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Menyimpan...');

                return true;
            });
        });
    </script>
@endpush
