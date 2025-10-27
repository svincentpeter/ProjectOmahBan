@extends('layouts.app')

@section('title', 'Buat Pengguna Baru')

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
        <li class="breadcrumb-item active">Buat Pengguna</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="user-form">
                @csrf

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-user-plus mr-2 text-primary"></i>
                                Buat Pengguna Baru
                            </h5>
                            <small class="text-muted">Tambahkan pengguna baru ke sistem</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-x mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-check-circle mr-1"></i> Simpan Pengguna
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    {{-- Left Column: Main Info --}}
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-info mr-2 text-primary"></i>
                                    Informasi Akun
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                {{-- Name & Email --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label font-weight-semibold">
                                                <i class="cil-user mr-1 text-muted"></i> Nama Lengkap
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control form-control-lg @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}"
                                                placeholder="Masukkan nama lengkap" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label font-weight-semibold">
                                                <i class="cil-envelope-closed mr-1 text-muted"></i> Email
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email"
                                                class="form-control form-control-lg @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email') }}"
                                                placeholder="email@example.com" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Email akan digunakan untuk login
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="form-label font-weight-semibold">
                                                <i class="cil-lock-locked mr-1 text-muted"></i> Password
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Minimal 8 karakter"
                                                    required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        id="togglePassword">
                                                        <i class="cil-eye" id="eyeIcon"></i>
                                                    </button>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation" class="form-label font-weight-semibold">
                                                <i class="cil-lock-locked mr-1 text-muted"></i> Konfirmasi Password
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" class="form-control form-control-lg"
                                                id="password_confirmation" name="password_confirmation"
                                                placeholder="Ketik ulang password" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-shield-alt mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <strong>Tips Password Aman:</strong>
                                            <ul class="mb-0 pl-3 mt-2" style="font-size: 0.875rem;">
                                                <li>Minimal 8 karakter</li>
                                                <li>Kombinasi huruf besar & kecil</li>
                                                <li>Tambahkan angka dan simbol</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Role & Status Card --}}
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-settings mr-2 text-primary"></i>
                                    Peran & Status
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role" class="form-label font-weight-semibold">
                                                <i class="cil-badge mr-1 text-muted"></i> Peran
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control form-control-lg @error('role') is-invalid @enderror"
                                                name="role" id="role" required>
                                                <option value="" selected disabled>-- Pilih Peran --</option>
                                                @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ old('role') == $role->name ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Peran menentukan hak akses pengguna
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="is_active" class="form-label font-weight-semibold">
                                                <i class="cil-check-circle mr-1 text-muted"></i> Status Akun
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control form-control-lg @error('is_active') is-invalid @enderror"
                                                name="is_active" id="is_active" required>
                                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>
                                                    <i class="cil-check"></i> Aktif
                                                </option>
                                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>
                                                    <i class="cil-x"></i> Nonaktif
                                                </option>
                                            </select>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Pengguna nonaktif tidak dapat login
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Profile Picture --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm sticky-sidebar">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-image mr-2 text-primary"></i>
                                    Foto Profil
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-3">
                                    <div class="avatar-preview-lg mx-auto mb-3">
                                        <img src="https://ui-avatars.com/api/?name=User&size=200&background=4834DF&color=fff"
                                            alt="Preview" id="imagePreview" class="img-fluid rounded-circle">
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="cil-info mr-1"></i>
                                        Ukuran maksimal: 500KB
                                    </small>
                                    <small class="text-muted d-block">
                                        Format: JPG, JPEG, PNG
                                    </small>
                                </div>
                                <div class="form-group">
                                    <input id="image" type="file" name="image" data-max-file-size="500KB">
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

        /* ========== Avatar Preview ========== */
        .avatar-preview-lg {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #f8f9fa;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .avatar-preview-lg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ========== Sticky Sidebar ========== */
        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        /* ========== Password Toggle ========== */
        .input-group-append .btn {
            height: 50px;
            border-color: #ced4da;
        }

        .input-group-append .btn:hover {
            background-color: #f8f9fa;
            border-color: #4834DF;
            color: #4834DF;
        }

        /* ========== Alert Styling ========== */
        .alert-info {
            background-color: #e7f6fc;
            border-color: #8ad4ee;
            color: #115293;
            border-radius: 8px;
        }

        .alert-info ul {
            list-style-type: disc;
        }

        /* ========== Button Gap ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        /* ========== FilePond Custom Styling ========== */
        .filepond--root {
            font-family: inherit;
        }

        .filepond--drop-label {
            color: #4834DF;
        }

        .filepond--panel-root {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
        }

        .filepond--panel-root:hover {
            border-color: #4834DF;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {
            .sticky-sidebar {
                position: relative;
                top: 0;
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
        }
    </style>
@endpush

@section('third_party_scripts')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Initialize FilePond
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType
            );

            const fileElement = document.querySelector('input[id="image"]');
            const pond = FilePond.create(fileElement, {
                acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
                labelIdle: '<i class="cil-cloud-upload" style="font-size: 2rem; margin-bottom: 0.5rem;"></i><br>Drag & Drop atau <span class="filepond--label-action">Browse</span>',
                labelFileTypeNotAllowed: 'Tipe file tidak valid',
                fileValidateTypeLabelExpectedTypes: 'Format yang diterima: {allTypes}',
            });

            FilePond.setOptions({
                server: {
                    url: "{{ route('filepond.upload') }}",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                }
            });

            // Password Toggle
            $('#togglePassword').on('click', function() {
                const passwordInput = $('#password');
                const eyeIcon = $('#eyeIcon');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    eyeIcon.removeClass('cil-eye').addClass('cil-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    eyeIcon.removeClass('cil-eye-slash').addClass('cil-eye');
                }
            });

            // Update avatar preview based on name
            $('#name').on('input', function() {
                const name = $(this).val() || 'User';
                const avatarUrl =
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=200&background=4834DF&color=fff`;
                $('#imagePreview').attr('src', avatarUrl);
            });

            // Form Validation
            $('#user-form').on('submit', function(e) {
                const password = $('#password').val();
                const passwordConfirm = $('#password_confirmation').val();

                if (password !== passwordConfirm) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Tidak Cocok',
                        text: 'Password dan konfirmasi password harus sama',
                        confirmButtonColor: '#4834DF'
                    });
                    $('#password_confirmation').focus();
                    return false;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Terlalu Pendek',
                        text: 'Password minimal 8 karakter',
                        confirmButtonColor: '#4834DF'
                    });
                    $('#password').focus();
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
        });
    </script>
@endpush
