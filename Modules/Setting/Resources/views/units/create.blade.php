@extends('layouts.app')

@section('title', 'Tambah Satuan')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('units.index') }}">Satuan</a></li>
        <li class="breadcrumb-item active">Tambah Satuan</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form action="{{ route('units.store') }}" method="POST" autocomplete="off" id="unit-form">
                @csrf

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-plus mr-2 text-primary"></i>
                                Tambah Satuan Baru
                            </h5>
                            <small class="text-muted">Buat satuan unit baru untuk produk</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-x mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-check-circle mr-1"></i> Simpan Satuan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Main Card --}}
                <div class="row mt-4">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-info mr-2 text-primary"></i>
                                    Detail Satuan
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    {{-- Unit Name --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label font-weight-semibold">
                                                <i class="cil-tag mr-1 text-muted"></i> Nama Satuan
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="name" name="name"
                                                class="form-control form-control-lg @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" placeholder="Contoh: Buah, Pcs, Set" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-lightbulb mr-1"></i>
                                                Nama lengkap satuan
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Short Name --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="short_name" class="form-label font-weight-semibold">
                                                <i class="cil-text-size mr-1 text-muted"></i> Singkatan
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="short_name" name="short_name"
                                                class="form-control form-control-lg @error('short_name') is-invalid @enderror"
                                                value="{{ old('short_name') }}" placeholder="Contoh: pcs, set, kg"
                                                maxlength="10" required>
                                            @error('short_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Maksimal 10 karakter
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Note --}}
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label for="note" class="form-label font-weight-semibold">
                                                <i class="cil-notes mr-1 text-muted"></i> Keterangan
                                                <span class="text-muted">(Opsional)</span>
                                            </label>
                                            <textarea id="note" name="note" rows="3" class="form-control @error('note') is-invalid @enderror"
                                                placeholder="Tambahkan keterangan atau deskripsi satuan...">{{ old('note') }}</textarea>
                                            @error('note')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Informasi tambahan tentang satuan ini
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Footer with Examples --}}
                            <div class="card-footer bg-light">
                                <div class="examples-section">
                                    <strong class="d-block mb-2">
                                        <i class="cil-lightbulb text-warning mr-1"></i>
                                        Contoh Satuan:
                                    </strong>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="example-item">
                                                <strong>Pcs</strong>
                                                <small class="text-muted d-block">Pieces (untuk barang satuan)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="example-item">
                                                <strong>Set</strong>
                                                <small class="text-muted d-block">Set (untuk paket produk)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="example-item">
                                                <strong>Kg</strong>
                                                <small class="text-muted d-block">Kilogram (untuk berat)</small>
                                            </div>
                                        </div>
                                    </div>
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

        /* ========== Sticky Action Bar ========== */
        .action-bar {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
            margin-bottom: 0;
        }

        /* ========== Card Shadow ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Form Enhancements ========== */
        .form-control-lg {
            height: 50px;
            font-size: 1rem;
        }

        .form-control:focus,
        textarea.form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* ========== Examples Section ========== */
        .examples-section {
            font-size: 0.875rem;
        }

        .example-item {
            padding: 0.75rem;
            background: white;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            margin-bottom: 0.5rem;
        }

        .example-item strong {
            color: #4834DF;
            font-size: 0.9375rem;
        }

        /* ========== Button Gap ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        /* ========== Character Counter ========== */
        .char-counter {
            font-size: 0.75rem;
            color: #6c757d;
            text-align: right;
            margin-top: 0.25rem;
        }

        .char-counter.warning {
            color: #ffc107;
        }

        .char-counter.danger {
            color: #dc3545;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {
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

            .col-lg-8.offset-lg-2 {
                margin-left: 0;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Character counter for short_name
            const shortNameInput = $('#short_name');
            const maxLength = 10;

            // Create counter element
            $('<div class="char-counter"></div>').insertAfter(shortNameInput.next('.form-text'));
            const counter = shortNameInput.parent().find('.char-counter');

            function updateCounter() {
                const length = shortNameInput.val().length;
                const remaining = maxLength - length;
                counter.text(`${length}/${maxLength} karakter`);

                if (remaining <= 2) {
                    counter.addClass('danger').removeClass('warning');
                } else if (remaining <= 5) {
                    counter.addClass('warning').removeClass('danger');
                } else {
                    counter.removeClass('warning danger');
                }
            }

            shortNameInput.on('input', updateCounter);
            updateCounter();

            // Auto uppercase short_name
            shortNameInput.on('input', function() {
                // Optional: Auto lowercase for consistency
                // $(this).val($(this).val().toLowerCase());
            });

            // Form Validation
            $('#unit-form').on('submit', function(e) {
                const name = $('#name').val().trim();
                const shortName = $('#short_name').val().trim();

                if (!name || !shortName) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi nama satuan dan singkatan',
                        confirmButtonColor: '#4834DF'
                    });
                    return false;
                }

                if (shortName.length > 10) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Singkatan Terlalu Panjang',
                        text: 'Singkatan maksimal 10 karakter',
                        confirmButtonColor: '#4834DF'
                    });
                    $('#short_name').focus();
                    return false;
                }

                // Show loading
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });

            // Auto-focus first input
            $('#name').focus();
        });
    </script>
@endpush
