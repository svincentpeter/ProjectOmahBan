@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Supplier</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- LEFT COLUMN: Form Input --}}
                    <div class="col-lg-8">
                        {{-- Informasi Supplier --}}
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="cil-people mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Edit Informasi Supplier</h5>
                                        <small class="text-muted">Perbarui data supplier untuk pembelian stok</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Nama Supplier --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="supplier_name"
                                                class="form-label small font-weight-semibold text-dark">
                                                Nama Supplier <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('supplier_name') is-invalid @enderror"
                                                name="supplier_name" id="supplier_name"
                                                value="{{ old('supplier_name', $supplier->supplier_name) }}"
                                                placeholder="Contoh: Toko Ban Sejahtera" required>
                                            @error('supplier_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="supplier_email"
                                                class="form-label small font-weight-semibold text-dark">
                                                Email <span class="text-danger">*</span>
                                            </label>
                                            <input type="email"
                                                class="form-control @error('supplier_email') is-invalid @enderror"
                                                name="supplier_email" id="supplier_email"
                                                value="{{ old('supplier_email', $supplier->supplier_email) }}"
                                                placeholder="email@supplier.com" required>
                                            @error('supplier_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- No. Telepon --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="supplier_phone"
                                                class="form-label small font-weight-semibold text-dark">
                                                No. Telepon <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('supplier_phone') is-invalid @enderror"
                                                name="supplier_phone" id="supplier_phone"
                                                value="{{ old('supplier_phone', $supplier->supplier_phone) }}"
                                                placeholder="08123456789 atau (021) 1234567" required>
                                            @error('supplier_phone')
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
                                                name="city" id="city" value="{{ old('city', $supplier->city) }}"
                                                placeholder="Contoh: Jakarta, Bandung, Surabaya" required>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Negara --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="country" class="form-label small font-weight-semibold text-dark">
                                                Negara
                                            </label>
                                            <select class="form-control @error('country') is-invalid @enderror"
                                                name="country" id="country">
                                                <option value="Indonesia"
                                                    {{ old('country', $supplier->country) == 'Indonesia' ? 'selected' : '' }}>
                                                    Indonesia</option>
                                                <option value="Malaysia"
                                                    {{ old('country', $supplier->country) == 'Malaysia' ? 'selected' : '' }}>
                                                    Malaysia</option>
                                                <option value="Singapura"
                                                    {{ old('country', $supplier->country) == 'Singapura' ? 'selected' : '' }}>
                                                    Singapura</option>
                                                <option value="Brunei"
                                                    {{ old('country', $supplier->country) == 'Brunei' ? 'selected' : '' }}>
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
                                                placeholder="Alamat lengkap supplier (minimal 10 karakter)" required>{{ old('address', $supplier->address) }}</textarea>
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
                                        <h5 class="mb-0 font-weight-bold">Informasi Supplier</h5>
                                        <small class="text-muted">Data riwayat dan status</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Supplier Stats --}}
                                <div class="mb-3 p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">
                                            <i class="cil-cart mr-1"></i> Total Pembelian
                                        </small>
                                        <strong class="text-primary">{{ $supplier->purchases->count() }}
                                            transaksi</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">
                                            <i class="cil-credit-card mr-1"></i> Total Nilai
                                        </small>
                                        <strong
                                            class="text-success">{{ format_currency($supplier->purchases->sum('total_amount')) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="cil-calendar mr-1"></i> Terdaftar
                                        </small>
                                        <strong>{{ $supplier->created_at->format('d M Y') }}</strong>
                                    </div>
                                </div>

                                {{-- Warning jika ada purchase history --}}
                                @if ($hasPurchases)
                                    <div class="alert alert-warning mb-3" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="cil-warning mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                            <div>
                                                <strong>Perhatian!</strong>
                                                <p class="mb-0 mt-1" style="font-size: 0.875rem;">
                                                    Supplier ini memiliki
                                                    <strong>{{ $supplier->purchases->count() }}</strong> riwayat pembelian.
                                                    Jika dihapus, data hanya akan di-arsipkan (soft delete) untuk menjaga
                                                    integritas data.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Validation Summary --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger mb-3" role="alert">
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
                                        <i class="cil-save mr-1"></i> Update Supplier
                                    </button>
                                    <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-outline-info">
                                        <i class="cil-eye mr-1"></i> Lihat Detail
                                    </a>
                                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                                        <i class="cil-x mr-1"></i> Batal
                                    </a>
                                </div>

                                {{-- Last Updated Info --}}
                                <div class="mt-3 p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-2">
                                        <i class="cil-clock mr-1"></i> <strong>Terakhir Diupdate</strong>
                                    </small>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $supplier->updated_at->diffForHumans() }}
                                        <br>
                                        ({{ $supplier->updated_at->format('d M Y, H:i') }})
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

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .alert-danger {
            background-color: #ffe7e7;
            color: #721c24;
        }

        /* ========== Stats Box ========== */
        .bg-light {
            background-color: #f8f9fa !important;
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

        .btn-outline-info:hover {
            background-color: #39f;
            color: white;
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
            // Track if form has changes
            let formChanged = false;
            const originalFormData = $('form').serialize();

            $('form input, form select, form textarea').on('change', function() {
                formChanged = ($('form').serialize() !== originalFormData);
            });

            // Warn user before leaving if form changed
            $(window).on('beforeunload', function() {
                if (formChanged) {
                    return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
                }
            });

            // Auto-format phone number
            $('#supplier_phone').on('blur', function() {
                let phone = $(this).val().trim();
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

                // Disable warning when submitting
                formChanged = false;

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Memperbarui...');

                return true;
            });

            // Disable warning when clicking cancel
            $('.btn-outline-secondary, .btn-outline-info').on('click', function() {
                formChanged = false;
            });
        });
    </script>
@endpush
