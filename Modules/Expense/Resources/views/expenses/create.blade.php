@extends('layouts.app')

@section('title', 'Tambah Pengeluaran')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Pengeluaran</a></li>
        <li class="breadcrumb-item active">Tambah Pengeluaran</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form id="expense-form" action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Action Bar --}}
                <div class="action-bar shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-plus mr-2 text-primary"></i>
                                Tambah Pengeluaran Baru
                            </h5>
                            <small class="text-muted">Catat pengeluaran operasional bisnis</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-save mr-1"></i> Simpan Pengeluaran
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Card --}}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-wallet mr-2 text-primary"></i>
                                    Detail Pengeluaran
                                </h6>
                            </div>

                            {{-- Include Form --}}
                            @include('expense::expenses._form', ['expense' => null])
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

        /* ========== Action Bar ========== */
        .action-bar {
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
        }

        /* ========== Cards ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }

        /* ========== Button Gaps ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        /* ========== Responsive ========== */
        @media (max-width: 768px) {
            .action-bar .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .action-bar .d-flex>div {
                width: 100%;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Form submission with SweetAlert2
            $('#expense-form').on('submit', function(e) {
                e.preventDefault();

                // Validate amount
                const amountInput = $('#amount');
                const amount = parseInt(amountInput.val().replace(/[^\d]/g, '')) || 0;

                if (amount <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Nominal Tidak Valid',
                        text: 'Nominal pengeluaran harus lebih besar dari 0',
                        confirmButtonColor: '#4834DF'
                    });
                    amountInput.focus();
                    return false;
                }

                // Validate bank name if transfer
                const paymentMethod = $('input[name="payment_method"]:checked').val();
                const bankName = $('input[name="bank_name"]').val();

                if (paymentMethod === 'Transfer' && !bankName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nama Bank Diperlukan',
                        text: 'Harap isi nama bank untuk metode pembayaran Transfer',
                        confirmButtonColor: '#4834DF'
                    });
                    $('input[name="bank_name"]').focus();
                    return false;
                }

                // Show loading and submit
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                this.submit();
            });
        });
    </script>
@endpush
