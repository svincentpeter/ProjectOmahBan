@extends('layouts.app')

@section('title', 'Profil Saya')

@section('third_party_stylesheets')
    @include('includes.filepond-css')
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Profil</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Page Header --}}
            <div class="row mb-4">
                <div class="col-12">
                    @include('utils.alerts')
                    <div class="welcome-section">
                        <div class="d-flex align-items-center">
                            <div class="avatar-welcome mr-3">
                                @if (auth()->user()->getFirstMediaUrl('avatars'))
                                    <img src="{{ auth()->user()->getFirstMediaUrl('avatars') }}"
                                        alt="{{ auth()->user()->name }}">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=80&background=4834DF&color=fff"
                                        alt="{{ auth()->user()->name }}">
                                @endif
                            </div>
                            <div>
                                <h3 class="mb-1">
                                    Halo, <span class="text-primary font-weight-bold">{{ auth()->user()->name }}</span>! 👋
                                </h3>
                                <p class="text-muted mb-0">
                                    <i class="cil-pencil mr-1"></i>
                                    Kelola informasi profil dan keamanan akun Anda di sini
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Left Column: Profile Info --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-user mr-2 text-primary"></i>
                                Informasi Profil
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
                                id="profile-form">
                                @csrf
                                @method('PATCH')

                                {{-- Profile Image --}}
                                <div class="form-group text-center">
                                    <label class="form-label font-weight-semibold mb-3">
                                        <i class="cil-image mr-1 text-muted"></i> Foto Profil
                                    </label>
                                    <div class="avatar-preview mx-auto mb-3">
                                        @if (auth()->user()->getFirstMediaUrl('avatars'))
                                            <img src="{{ auth()->user()->getFirstMediaUrl('avatars') }}"
                                                alt="{{ auth()->user()->name }}" id="profilePreview"
                                                class="img-fluid rounded-circle">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=150&background=4834DF&color=fff"
                                                alt="{{ auth()->user()->name }}" id="profilePreview"
                                                class="img-fluid rounded-circle">
                                        @endif
                                    </div>
                                    <input id="image" type="file" name="image" data-max-file-size="500KB">
                                    <small class="form-text text-muted mt-2">
                                        <i class="cil-info mr-1"></i>
                                        Ukuran maksimal 500KB, Format: JPG, JPEG, PNG
                                    </small>
                                </div>

                                <hr class="my-4">

                                {{-- Name --}}
                                <div class="form-group">
                                    <label for="name" class="form-label font-weight-semibold">
                                        <i class="cil-user mr-1 text-muted"></i> Nama Lengkap
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                                        placeholder="Masukkan nama lengkap" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="form-group">
                                    <label for="email" class="form-label font-weight-semibold">
                                        <i class="cil-envelope-closed mr-1 text-muted"></i> Email
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                        placeholder="email@example.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="cil-info mr-1"></i>
                                        Email digunakan untuk login ke sistem
                                    </small>
                                </div>

                                {{-- Submit Button --}}
                                <div class="form-group mb-0 mt-4">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                                        <i class="cil-check-circle mr-2"></i>
                                        Perbarui Profil
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Footer Info --}}
                        <div class="card-footer bg-light">
                            <div class="profile-info-summary">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <small class="text-muted d-block">Peran</small>
                                        <strong
                                            class="text-primary">{{ auth()->user()->roles->first()->name ?? '-' }}</strong>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted d-block">Bergabung</small>
                                        <strong>{{ auth()->user()->created_at->format('d M Y') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Change Password --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-lock-locked mr-2 text-primary"></i>
                                Ubah Password
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('profile.update.password') }}" method="POST" id="password-form">
                                @csrf
                                @method('PATCH')

                                {{-- Security Alert --}}
                                <div class="alert alert-warning" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="cil-shield-alt mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <strong>Keamanan Akun</strong>
                                            <p class="mb-0 small">
                                                Gunakan password yang kuat untuk melindungi akun Anda
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Current Password --}}
                                <div class="form-group">
                                    <label for="current_password" class="form-label font-weight-semibold">
                                        <i class="cil-lock-locked mr-1 text-muted"></i> Password Saat Ini
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                                            id="current_password" name="current_password"
                                            placeholder="Masukkan password saat ini" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePassword('current_password', this)">
                                                <i class="cil-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- New Password --}}
                                <div class="form-group">
                                    <label for="password" class="form-label font-weight-semibold">
                                        <i class="cil-lock-locked mr-1 text-muted"></i> Password Baru
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Minimal 8 karakter" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePassword('password', this)">
                                                <i class="cil-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label font-weight-semibold">
                                        <i class="cil-lock-locked mr-1 text-muted"></i> Konfirmasi Password Baru
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                            id="password_confirmation" name="password_confirmation"
                                            placeholder="Ketik ulang password baru" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePassword('password_confirmation', this)">
                                                <i class="cil-eye"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Password Tips --}}
                                <div class="alert alert-info mt-3" role="alert">
                                    <strong class="d-block mb-2">
                                        <i class="cil-lightbulb mr-1"></i>
                                        Tips Password Aman:
                                    </strong>
                                    <ul class="mb-0 pl-3" style="font-size: 0.875rem;">
                                        <li>Minimal 8 karakter</li>
                                        <li>Kombinasi huruf besar & kecil</li>
                                        <li>Tambahkan angka dan simbol</li>
                                        <li>Jangan gunakan informasi pribadi</li>
                                    </ul>
                                </div>

                                {{-- Submit Button --}}
                                <div class="form-group mb-0 mt-4">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                                        <i class="cil-shield-alt mr-2"></i>
                                        Ubah Password
                                    </button>
                                </div>
                            </form>
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

        /* ========== Welcome Section ========== */
        .welcome-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .avatar-welcome {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #4834DF;
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.2);
            flex-shrink: 0;
        }

        .avatar-welcome img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ========== Card Shadow ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Avatar Preview ========== */
        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #f8f9fa;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        /* ========== Input Group ========== */
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
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
            border-radius: 8px;
        }

        .alert-info {
            background-color: #e7f6fc;
            border-color: #8ad4ee;
            color: #115293;
            border-radius: 8px;
        }

        .alert-info ul {
            list-style-type: disc;
        }

        /* ========== Profile Info Summary ========== */
        .profile-info-summary {
            font-size: 0.875rem;
        }

        /* ========== Buttons ========== */
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {
            .welcome-section {
                padding: 1.5rem;
            }

            .avatar-welcome {
                width: 60px;
                height: 60px;
            }

            .avatar-preview {
                width: 120px;
                height: 120px;
            }
        }
    </style>
@endpush

@push('page_scripts')
    @include('includes.filepond-js')

    <script>
        // Toggle Password Visibility
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('cil-eye');
                icon.classList.add('cil-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('cil-eye-slash');
                icon.classList.add('cil-eye');
            }
        }

        $(document).ready(function() {
            // Profile Form Validation
            $('#profile-form').on('submit', function(e) {
                const name = $('#name').val().trim();
                const email = $('#email').val().trim();

                if (!name || !email) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi semua field yang wajib diisi',
                        confirmButtonColor: '#4834DF'
                    });
                    return false;
                }

                // Show loading
                Swal.fire({
                    title: 'Memperbarui Profil...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });

            // Password Form Validation
            $('#password-form').on('submit', function(e) {
                const currentPassword = $('#current_password').val();
                const newPassword = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                if (!currentPassword || !newPassword || !confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi semua field password',
                        confirmButtonColor: '#4834DF'
                    });
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Tidak Cocok',
                        text: 'Password baru dan konfirmasi password harus sama',
                        confirmButtonColor: '#4834DF'
                    });
                    $('#password_confirmation').focus();
                    return false;
                }

                if (newPassword.length < 8) {
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

                if (currentPassword === newPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Password Sama',
                        text: 'Password baru harus berbeda dari password lama',
                        confirmButtonColor: '#4834DF'
                    });
                    $('#password').focus();
                    return false;
                }

                // Confirmation
                e.preventDefault();
                Swal.fire({
                    title: 'Ubah Password?',
                    text: 'Anda akan logout setelah password berhasil diubah',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4834DF',
                    cancelButtonColor: '#768192',
                    confirmButtonText: '<i class="cil-shield-alt mr-1"></i> Ya, Ubah!',
                    cancelButtonText: '<i class="cil-x mr-1"></i> Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mengubah Password...',
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

            // Update avatar preview based on name
            $('#name').on('input', function() {
                const name = $(this).val() || '{{ auth()->user()->name }}';
                @if (!auth()->user()->getFirstMediaUrl('avatars'))
                    const avatarUrl =
                        `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=150&background=4834DF&color=fff`;
                    $('#profilePreview').attr('src', avatarUrl);
                @endif
            });
        });
    </script>
@endpush
