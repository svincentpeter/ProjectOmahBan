@extends('layouts.app')

@section('title', 'Ubah Mata Uang')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('currencies.index') }}">Mata Uang</a></li>
        <li class="breadcrumb-item active">Ubah: {{ $currency->currency_name }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form action="{{ route('currencies.update', $currency) }}" method="POST" autocomplete="off" id="currency-form">
                @csrf
                @method('PUT')

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-pencil mr-2 text-primary"></i>
                                Ubah Mata Uang: {{ $currency->currency_name }}
                            </h5>
                            <small class="text-muted">Perbarui konfigurasi mata uang</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('currencies.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-x mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    {{-- Left Column: Form --}}
                    <div class="col-lg-7">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-info mr-2 text-primary"></i>
                                    Informasi Mata Uang
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    {{-- Currency Name --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="currency_name" class="form-label font-weight-semibold">
                                                <i class="cil-money mr-1 text-muted"></i> Nama Mata Uang
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="currency_name" name="currency_name"
                                                class="form-control form-control-lg @error('currency_name') is-invalid @enderror"
                                                value="{{ old('currency_name', $currency->currency_name) }}"
                                                placeholder="Contoh: Rupiah Indonesia" required>
                                            @error('currency_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-lightbulb mr-1"></i>
                                                Nama lengkap mata uang
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Code --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code" class="form-label font-weight-semibold">
                                                <i class="cil-code mr-1 text-muted"></i> Kode (ISO 4217)
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="code" name="code"
                                                class="form-control form-control-lg text-uppercase @error('code') is-invalid @enderror"
                                                value="{{ old('code', $currency->code) }}" placeholder="IDR" maxlength="3"
                                                required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                3 karakter (IDR, USD, EUR)
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Symbol --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="symbol" class="form-label font-weight-semibold">
                                                <i class="cil-badge mr-1 text-muted"></i> Simbol
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="symbol" name="symbol"
                                                class="form-control form-control-lg @error('symbol') is-invalid @enderror"
                                                value="{{ old('symbol', $currency->symbol) }}" placeholder="Rp" required>
                                            @error('symbol')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Simbol tampilan (Rp, $, €)
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Thousand Separator --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="thousand_separator" class="form-label font-weight-semibold">
                                                <i class="cil-options mr-1 text-muted"></i> Pemisah Ribuan
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select id="thousand_separator" name="thousand_separator"
                                                class="form-control form-control-lg @error('thousand_separator') is-invalid @enderror"
                                                required>
                                                <option value="."
                                                    {{ old('thousand_separator', $currency->thousand_separator) == '.' ? 'selected' : '' }}>
                                                    Titik (.)</option>
                                                <option value=","
                                                    {{ old('thousand_separator', $currency->thousand_separator) == ',' ? 'selected' : '' }}>
                                                    Koma (,)</option>
                                                <option value=" "
                                                    {{ old('thousand_separator', $currency->thousand_separator) == ' ' ? 'selected' : '' }}>
                                                    Spasi ( )</option>
                                            </select>
                                            @error('thousand_separator')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Format: 1.000 atau 1,000
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Decimal Separator --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="decimal_separator" class="form-label font-weight-semibold">
                                                <i class="cil-options mr-1 text-muted"></i> Pemisah Desimal
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select id="decimal_separator" name="decimal_separator"
                                                class="form-control form-control-lg @error('decimal_separator') is-invalid @enderror"
                                                required>
                                                <option value=","
                                                    {{ old('decimal_separator', $currency->decimal_separator) == ',' ? 'selected' : '' }}>
                                                    Koma (,)</option>
                                                <option value="."
                                                    {{ old('decimal_separator', $currency->decimal_separator) == '.' ? 'selected' : '' }}>
                                                    Titik (.)</option>
                                            </select>
                                            @error('decimal_separator')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-ban mr-1"></i>
                                                Tidak ditampilkan (0 desimal)
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Format Info Alert --}}
                                <div class="alert alert-info mt-3" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-info mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <strong>Format Tampilan Sistem</strong>
                                            <p class="mb-0">
                                                Sistem menggunakan format standar: <strong class="text-primary">Rp
                                                    100.000</strong>
                                                <br>
                                                <small>Tanpa desimal, dengan pemisah ribuan sesuai konfigurasi</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Footer with Timestamps --}}
                            <div class="card-footer bg-light">
                                <div class="currency-info-summary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted d-block">Dibuat</small>
                                            <strong>{{ $currency->created_at->format('d M Y H:i') }}</strong>
                                        </div>
                                        <div class="text-right">
                                            <small class="text-muted d-block">Terakhir Diubah</small>
                                            <strong>{{ $currency->updated_at->format('d M Y H:i') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Preview --}}
                    <div class="col-lg-5">
                        <div class="card shadow-sm sticky-sidebar">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-screen-desktop mr-2 text-primary"></i>
                                    Preview Format
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="preview-container">
                                    <div class="preview-label">Contoh Tampilan:</div>
                                    <div class="preview-display" id="formatPreview">
                                        {{ $currency->symbol }} 100{{ $currency->thousand_separator }}000
                                    </div>

                                    <hr class="my-4">

                                    <div class="preview-details">
                                        <div class="detail-item">
                                            <span class="detail-label">Mata Uang:</span>
                                            <span class="detail-value"
                                                id="previewName">{{ $currency->currency_name }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Kode:</span>
                                            <span class="detail-value" id="previewCode">{{ $currency->code }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Simbol:</span>
                                            <span class="detail-value" id="previewSymbol">{{ $currency->symbol }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Pemisah Ribuan:</span>
                                            <span class="detail-value" id="previewThousand">
                                                @if ($currency->thousand_separator == '.')
                                                    Titik (.)
                                                @elseif($currency->thousand_separator == ',')
                                                    Koma (,)
                                                @else
                                                    Spasi ( )
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-4" role="alert">
                                    <small>
                                        <i class="cil-warning mr-1"></i>
                                        <strong>Catatan:</strong> Desimal tidak ditampilkan di UI untuk menjaga konsistensi
                                        format mata uang Indonesia.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* ========== Same styles as Create page ========== */
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

        .action-bar {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
            margin-bottom: 0;
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        .form-control-lg {
            height: 50px;
            font-size: 1rem;
        }

        .form-control:focus,
        select.form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

        .preview-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
        }

        .preview-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .preview-display {
            font-size: 2.5rem;
            font-weight: 700;
            color: #4834DF;
            padding: 1.5rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.1);
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .preview-display:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(72, 52, 223, 0.15);
        }

        .preview-details {
            margin-top: 1rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.875rem;
        }

        .detail-value {
            font-weight: 600;
            color: #212529;
            font-size: 0.875rem;
        }

        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        .alert-info {
            background-color: #e7f6fc;
            border-color: #8ad4ee;
            color: #115293;
            border-radius: 8px;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
            border-radius: 8px;
        }

        .currency-info-summary {
            font-size: 0.875rem;
        }

        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        @media (max-width: 992px) {
            .sticky-sidebar {
                position: relative;
                top: 0;
                margin-top: 1rem;
            }

            .action-bar {
                position: relative;
            }

            .action-bar .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .action-bar .d-flex>div {
                width: 100%;
            }

            .preview-display {
                font-size: 2rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Update preview live
            function updatePreview() {
                const name = $('#currency_name').val() || '{{ $currency->currency_name }}';
                const code = $('#code').val().toUpperCase() || '{{ $currency->code }}';
                const symbol = $('#symbol').val() || '{{ $currency->symbol }}';
                const thousand = $('#thousand_separator').val() || '{{ $currency->thousand_separator }}';

                // Update preview details
                $('#previewName').text(name);
                $('#previewCode').text(code);
                $('#previewSymbol').text(symbol);

                // Format thousand separator name
                let thousandName = '';
                switch (thousand) {
                    case '.':
                        thousandName = 'Titik (.)';
                        break;
                    case ',':
                        thousandName = 'Koma (,)';
                        break;
                    case ' ':
                        thousandName = 'Spasi ( )';
                        break;
                    default:
                        thousandName = thousand;
                }
                $('#previewThousand').text(thousandName);

                // Format preview display
                const formattedNumber = formatCurrency(100000, symbol, thousand);
                $('#formatPreview').text(formattedNumber);
            }

            // Format currency helper
            function formatCurrency(amount, symbol, separator) {
                const formatted = amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator);
                return symbol + ' ' + formatted;
            }

            // Auto uppercase code
            $('#code').on('input', function() {
                $(this).val($(this).val().toUpperCase());
                updatePreview();
            });

            // Update preview on input
            $('#currency_name, #symbol, #thousand_separator, #decimal_separator').on('input change', updatePreview);

            // Initialize preview
            updatePreview();

            // Form Validation
            $('#currency-form').on('submit', function(e) {
                const name = $('#currency_name').val().trim();
                const code = $('#code').val().trim();
                const symbol = $('#symbol').val().trim();

                if (!name || !code || !symbol) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi semua field yang wajib diisi',
                        confirmButtonColor: '#4834DF'
                    });
                    return false;
                }

                if (code.length !== 3) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Kode Tidak Valid',
                        text: 'Kode mata uang harus 3 karakter (ISO 4217)',
                        confirmButtonColor: '#4834DF'
                    });
                    $('#code').focus();
                    return false;
                }

                // Confirmation dialog
                e.preventDefault();
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    html: `Mata uang <strong>"{{ $currency->currency_name }}"</strong> akan diperbarui`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4834DF',
                    cancelButtonColor: '#768192',
                    confirmButtonText: '<i class="cil-save mr-1"></i> Ya, Simpan!',
                    cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        e.target.submit();
                    }
                });
            });

            // Auto-focus first input
            $('#currency_name').focus();
        });
    </script>
@endpush
