@extends('layouts.app')

@section('title', 'Buat Peran Baru')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Peran & Hak Akses</a></li>
        <li class="breadcrumb-item active">Buat Peran</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form action="{{ route('roles.store') }}" method="POST" id="role-form">
                @csrf

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-shield-alt mr-2 text-primary"></i>
                                Buat Peran Baru
                            </h5>
                            <small class="text-muted">Tentukan nama dan hak akses untuk peran baru</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-x mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-check-circle mr-1"></i> Simpan Peran
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Main Form Card --}}
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="cil-info mr-2 text-primary"></i>
                            Informasi Peran
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="form-label font-weight-semibold">
                                <i class="cil-tag mr-1 text-muted"></i> Nama Peran
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}"
                                placeholder="Contoh: Manager Toko, Kasir, Admin Gudang" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="cil-lightbulb mr-1"></i>
                                Gunakan nama yang jelas dan mudah dipahami untuk peran ini
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Permissions Card --}}
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="mb-2 mb-md-0">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-lock-locked mr-2 text-primary"></i>
                                    Hak Akses (Permissions)
                                </h6>
                                <small class="text-muted">Pilih fitur yang dapat diakses oleh peran ini</small>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="select-all">
                                <label class="custom-control-label font-weight-semibold" for="select-all">
                                    Pilih Semua Hak Akses
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row">
                            {{-- Dashboard --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-speedometer text-primary mr-2"></i>
                                        Dashboard
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'dashboard',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- User Management --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-people text-primary mr-2"></i>
                                        Manajemen Pengguna
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'user_management',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Products --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-tags text-primary mr-2"></i>
                                        Produk
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'products',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Adjustments --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-balance-scale text-primary mr-2"></i>
                                        Penyesuaian Stok
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'adjustments',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Quotations --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-description text-primary mr-2"></i>
                                        Penawaran Harga
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'quotations',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Expenses --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-wallet text-primary mr-2"></i>
                                        Pengeluaran
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'expenses',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Customers --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-user text-primary mr-2"></i>
                                        Pelanggan
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'customers',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Suppliers --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-truck text-primary mr-2"></i>
                                        Pemasok
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'suppliers',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Sales --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-cart text-primary mr-2"></i>
                                        Penjualan
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'sales',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Sale Returns --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-loop-circular text-primary mr-2"></i>
                                        Retur Penjualan
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'sale_returns',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Purchases --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-basket text-primary mr-2"></i>
                                        Pembelian
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'purchases',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Purchase Returns --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-loop-circular text-primary mr-2"></i>
                                        Retur Pembelian
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'purchase_returns',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Reports --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-chart-line text-primary mr-2"></i>
                                        Laporan
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'reports',
                                        ])
                                    </div>
                                </div>
                            </div>

                            {{-- Settings --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="permission-card h-100">
                                    <div class="permission-card-header">
                                        <i class="cil-settings text-primary mr-2"></i>
                                        Pengaturan
                                    </div>
                                    <div class="permission-card-body">
                                        @include('user::roles.partials.permissions-list', [
                                            'group' => 'settings',
                                        ])
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

        .form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

        /* ========== Permission Cards ========== */
        .permission-card {
            background: white;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .permission-card:hover {
            border-color: #4834DF;
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.15);
            transform: translateY(-2px);
        }

        .permission-card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 1rem 1.25rem;
            font-weight: 600;
            font-size: 0.9375rem;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            align-items: center;
        }

        .permission-card-body {
            padding: 1rem 1.25rem;
        }

        /* ========== Custom Checkbox Styling ========== */
        .custom-control-label {
            cursor: pointer;
            user-select: none;
        }

        .custom-control-input:checked~.custom-control-label::before {
            background-color: #4834DF;
            border-color: #4834DF;
        }

        .custom-control-input:focus~.custom-control-label::before {
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

        /* ========== Select All Switch ========== */
        .custom-switch .custom-control-label::before {
            left: -2.25rem;
            width: 1.75rem;
            pointer-events: all;
            border-radius: 0.5rem;
        }

        .custom-switch .custom-control-label::after {
            top: calc(0.25rem + 2px);
            left: calc(-2.25rem + 2px);
            width: calc(1rem - 4px);
            height: calc(1rem - 4px);
            background-color: #adb5bd;
            border-radius: 0.5rem;
            transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }

        .custom-switch .custom-control-input:checked~.custom-control-label::after {
            background-color: #fff;
            transform: translateX(0.75rem);
        }

        /* ========== Button Gap ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        /* ========== Responsive ========== */
        @media (max-width: 768px) {
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

            .permission-card-header {
                font-size: 0.875rem;
                padding: 0.875rem 1rem;
            }

            .permission-card-body {
                padding: 0.875rem 1rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Select/Deselect All
            $('#select-all').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.permission-card-body input[type="checkbox"]').prop('checked', isChecked);

                // Visual feedback
                if (isChecked) {
                    $('.permission-card').addClass('border-success');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Semua hak akses dipilih',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    $('.permission-card').removeClass('border-success');
                }
            });

            // Update "Select All" when individual checkboxes change
            $('.permission-card-body input[type="checkbox"]').on('change', function() {
                const total = $('.permission-card-body input[type="checkbox"]').length;
                const checked = $('.permission-card-body input[type="checkbox"]:checked').length;

                $('#select-all').prop('checked', total === checked);
            });

            // Form Validation
            $('#role-form').on('submit', function(e) {
                const roleName = $('#name').val().trim();
                const checkedPermissions = $('.permission-card-body input[type="checkbox"]:checked').length;

                if (!roleName) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Nama Peran Wajib Diisi',
                        text: 'Mohon masukkan nama untuk peran ini',
                        confirmButtonColor: '#4834DF'
                    });
                    $('#name').focus();
                    return false;
                }

                if (checkedPermissions === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Ada Hak Akses',
                        text: 'Mohon pilih minimal satu hak akses untuk peran ini',
                        confirmButtonColor: '#4834DF'
                    });
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

            // Permission Card Click Effect
            $('.permission-card-header').on('click', function() {
                const card = $(this).closest('.permission-card');
                const checkboxes = card.find('.permission-card-body input[type="checkbox"]');
                const allChecked = checkboxes.length === checkboxes.filter(':checked').length;

                // Toggle all checkboxes in this card
                checkboxes.prop('checked', !allChecked);

                // Update select all
                const total = $('.permission-card-body input[type="checkbox"]').length;
                const checked = $('.permission-card-body input[type="checkbox"]:checked').length;
                $('#select-all').prop('checked', total === checked);
            });
        });
    </script>
@endpush
