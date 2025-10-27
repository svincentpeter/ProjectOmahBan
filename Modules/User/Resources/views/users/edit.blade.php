@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
        <li class="breadcrumb-item active">Edit: {{ $user->name }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                id="user-form">
                @csrf
                @method('PATCH')

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-pencil mr-2 text-primary"></i>
                                Edit Pengguna: {{ $user->name }}
                            </h5>
                            <small class="text-muted">Perbarui informasi pengguna</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-x mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-save mr-1"></i> Simpan Perubahan
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
                                                id="name" name="name" value="{{ old('name', $user->name) }}"
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
                                                id="email" name="email" value="{{ old('email', $user->email) }}"
                                                placeholder="email@example.com" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Email digunakan untuk login
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Password Change (Optional) --}}
                                <div class="alert alert-warning" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-warning mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <strong>Ganti Password</strong>
                                            <p class="mb-0">Kosongkan field password jika tidak ingin mengubah password
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="form-label font-weight-semibold">
                                                <i class="cil-lock-locked mr-1 text-muted"></i> Password Baru
                                                <span class="text-muted">(Opsional)</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Minimal 8 karakter">
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
                                                <span class="text-muted">(Opsional)</span>
                                            </label>
                                            <input type="password" class="form-control form-control-lg"
                                                id="password_confirmation" name="password_confirmation"
                                                placeholder="Ketik ulang password baru">
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
                                                @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ $user->hasRole($role->name) ? 'selected' : '' }}
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
                                                Peran saat ini: <strong
                                                    class="text-primary">{{ $user->roles->first()->name ?? 'Tidak ada' }}</strong>
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
                                                <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>
                                                    ✓ Aktif
                                                </option>
                                                <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>
                                                    ✗ Nonaktif
                                                </option>
                                            </select>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="cil-info mr-1"></i>
                                                Status saat ini:
                                                <strong class="{{ $user->is_active ? 'text-success' : 'text-danger' }}">
                                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                @if ($user->is_active == 0)
                                    <div class="alert alert-danger mt-3" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="cil-warning mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                            <div>
                                                <strong>Akun Nonaktif</strong>
                                                <p class="mb-0">Pengguna ini saat ini tidak dapat login ke sistem</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                                        @if ($user->getFirstMediaUrl('avatars'))
                                            <img src="{{ $user->getFirstMediaUrl('avatars') }}"
                                                alt="{{ $user->name }}" id="imagePreview"
                                                class="img-fluid rounded-circle">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=4834DF&color=fff"
                                                alt="{{ $user->name }}" id="imagePreview"
                                                class="img-fluid rounded-circle">
                                        @endif
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

                                @if ($user->getFirstMediaUrl('avatars'))
                                    <button type="button" class="btn btn-outline-danger btn-block btn-sm"
                                        id="removePhoto">
                                        <i class="cil-trash mr-1"></i> Hapus Foto
                                    </button>
                                @endif
                            </div>

                            {{-- User Info Summary --}}
                            <div class="card-footer bg-light">
                                <div class="user-info-summary">
                                    <div class="info-item">
                                        <i class="cil-calendar text-muted mr-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Bergabung</small>
                                            <strong>{{ $user->created_at->format('d M Y') }}</strong>
                                        </div>
                                    </div>
                                    <div class="info-item mt-2">
                                        <i class="cil-pencil text-muted mr-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Terakhir Diubah</small>
                                            <strong>{{ $user->updated_at->format('d M Y H:i') }}</strong>
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

        .form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

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

        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        .input-group-append .btn {
            height: 50px;
            border-color: #ced4da;
        }

        .input-group-append .btn:hover {
            background-color: #f8f9fa;
            border-color: #4834DF;
            color: #4834DF;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
            border-radius: 8px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-radius: 8px;
        }

        .user-info-summary {
            font-size: 0.875rem;
        }

        .user-info-summary .info-item {
            display: flex;
            align-items: flex-start;
        }

        .user-info-summary .info-item i {
            font-size: 1.25rem;
            margin-top: 2px;
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
                const name = $(this).val() || '{{ $user->name }}';
                @if (!$user->getFirstMediaUrl('avatars'))
                    const avatarUrl =
                        `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=200&background=4834DF&color=fff`;
                    $('#imagePreview').attr('src', avatarUrl);
                @endif
            });

            // Remove Photo
            $('#removePhoto').on('click', function() {
                Swal.fire({
                    title: 'Hapus Foto Profil?',
                    text: 'Foto profil akan dihapus dan diganti dengan avatar default',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e55353',
                    cancelButtonColor: '#768192',
                    confirmButtonText: '<i class="cil-trash mr-1"></i> Ya, Hapus!',
                    cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add hidden input to indicate photo removal
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'remove_photo',
                            value: '1'
                        }).appendTo('#user-form');

                        // Update preview
                        const avatarUrl =
                            `https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=4834DF&color=fff`;
                        $('#imagePreview').attr('src', avatarUrl);
                        $(this).remove();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Foto akan dihapus saat menyimpan',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            });

            // Form Validation
            $('#user-form').on('submit', function(e) {
                const password = $('#password').val();
                const passwordConfirm = $('#password_confirmation').val();

                // Only validate password if it's filled
                if (password || passwordConfirm) {
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
                }

                // Confirmation dialog
                e.preventDefault();
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    html: `Data pengguna <strong>"{{ $user->name }}"</strong> akan diperbarui`,
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
        });
    </script>
@endpush
