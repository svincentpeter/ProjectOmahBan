@extends('layouts.app')

@section('title', 'Edit Peran')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Peran & Hak Akses</a></li>
        <li class="breadcrumb-item active">Edit: {{ $role->name }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form action="{{ route('roles.update', $role->id) }}" method="POST" id="role-form">
                @csrf
                @method('PATCH')

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-pencil mr-2 text-primary"></i>
                                Edit Peran: {{ $role->name }}
                            </h5>
                            <small class="text-muted">Perbarui nama dan hak akses untuk peran ini</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-x mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-save mr-1"></i> Simpan Perubahan
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
                                id="name" name="name" value="{{ old('name', $role->name) }}"
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
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge badge-info" id="selected-count">
                                    <i class="cil-check-circle mr-1"></i>
                                    <span id="count-text">0</span> dipilih
                                </span>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="select-all">
                                    <label class="custom-control-label font-weight-semibold" for="select-all">
                                        Pilih Semua
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row">
                            @php
                                $groups = [
                                    'dashboard' => ['title' => 'Dashboard', 'icon' => 'cil-speedometer'],
                                    'user_management' => ['title' => 'Manajemen Pengguna', 'icon' => 'cil-people'],
                                    'products' => ['title' => 'Produk', 'icon' => 'cil-tags'],
                                    'adjustments' => ['title' => 'Penyesuaian Stok', 'icon' => 'cil-balance-scale'],
                                    'quotations' => ['title' => 'Penawaran Harga', 'icon' => 'cil-description'],
                                    'expenses' => ['title' => 'Pengeluaran', 'icon' => 'cil-wallet'],
                                    'customers' => ['title' => 'Pelanggan', 'icon' => 'cil-user'],
                                    'suppliers' => ['title' => 'Pemasok', 'icon' => 'cil-truck'],
                                    'sales' => ['title' => 'Penjualan', 'icon' => 'cil-cart'],
                                    'sale_returns' => ['title' => 'Retur Penjualan', 'icon' => 'cil-loop-circular'],
                                    'purchases' => ['title' => 'Pembelian', 'icon' => 'cil-basket'],
                                    'purchase_returns' => ['title' => 'Retur Pembelian', 'icon' => 'cil-loop-circular'],
                                    'reports' => ['title' => 'Laporan', 'icon' => 'cil-chart-line'],
                                    'settings' => ['title' => 'Pengaturan', 'icon' => 'cil-settings'],
                                ];
                            @endphp

                            @foreach ($groups as $key => $info)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="permission-card h-100">
                                        <div class="permission-card-header">
                                            <i class="{{ $info['icon'] }} text-primary mr-2"></i>
                                            {{ $info['title'] }}
                                        </div>
                                        <div class="permission-card-body">
                                            @include('user::roles.partials.permissions-list', [
                                                'group' => $key,
                                                'rolePermissions' => $rolePermissions,
                                            ])
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
            cursor: pointer;
            user-select: none;
        }

        .permission-card-header:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
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

        /* ========== Selected Count Badge ========== */
        .badge-info {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        /* ========== Button Gap ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        .d-flex.gap-3>* {
            margin-left: 1rem;
        }

        .d-flex.gap-3>*:first-child {
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

            .d-flex.gap-3 {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .d-flex.gap-3>* {
                margin-left: 0;
                margin-top: 0.5rem;
            }

            .d-flex.gap-3>*:first-child {
                margin-top: 0;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Update permission count
            function updateCount() {
                const total = $('.permission-card-body input[type="checkbox"]').length;
                const checked = $('.permission-card-body input[type="checkbox"]:checked').length;
                $('#count-text').text(checked + ' / ' + total);

                // Update select-all state
                $('#select-all').prop('checked', total === checked);
            }

            // Initialize count
            updateCount();

            // Select/Deselect All
            $('#select-all').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.permission-card-body input[type="checkbox"]').prop('checked', isChecked);
                updateCount();

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

            // Update count when individual checkbox changes
            $('.permission-card-body').on('change', 'input[type="checkbox"]', function() {
                updateCount();
            });

            // Click header to toggle all in card
            $('.permission-card-header').on('click', function() {
                const card = $(this).closest('.permission-card');
                const checkboxes = card.find('.permission-card-body input[type="checkbox"]');
                const allChecked = checkboxes.length === checkboxes.filter(':checked').length;

                // Toggle all checkboxes in this card
                checkboxes.prop('checked', !allChecked);
                updateCount();
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

                // Confirm changes
                e.preventDefault();
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    html: `Peran <strong>"${roleName}"</strong> akan diperbarui.<br>` +
                        `<small class="text-muted">${checkedPermissions} hak akses dipilih</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4834DF',
                    cancelButtonColor: '#768192',
                    confirmButtonText: '<i class="cil-save mr-1"></i> Ya, Simpan!',
                    cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menyimpan...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        e.target.submit();
                    }
                });
            });
        });
    </script>
@endpush
