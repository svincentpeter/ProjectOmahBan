@extends('layouts.app-flowbite')

@section('title', 'Edit Pengguna')

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        /* FilePond Custom Styling */
        .filepond--root {
            font-family: inherit;
        }
        .filepond--panel-root {
            background-color: #fff;
            border: 2px dashed #e5e7eb;
            border-radius: 0.5rem;
        }
        .dark .filepond--panel-root {
            background-color: #1f2937;
            border-color: #374151;
        }
        .filepond--drop-label {
            color: #6b7280;
        }
        .dark .filepond--drop-label {
            color: #9ca3af;
        }
    </style>
@endsection

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Pengguna', 'url' => route('users.index')],
        ['text' => 'Edit Pengguna: ' . $user->name, 'url' => '#']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" id="user-form">
        @csrf
        @method('PATCH')

        {{-- Sticky Action Bar --}}
        <div class="sticky top-[72px] z-50 mb-6 p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 transition-transform duration-300" id="actionBar">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h5 class="flex items-center text-lg font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-pencil-square me-2 text-blue-600"></i>
                         Edit Pengguna: {{ $user->name }}
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Perbarui informasi pengguna</p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('users.index') }}" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition-all font-medium text-sm">
                        <i class="bi bi-x me-1"></i> Batal
                    </a>
                    <button type="submit" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-all font-medium text-sm shadow-md hover:shadow-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Account Info Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-info-circle me-2 text-blue-600"></i>
                            Informasi Akun
                        </h6>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-person me-1 text-gray-500"></i> Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                                    placeholder="Masukkan nama lengkap" required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-envelope me-1 text-gray-500"></i> Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('email') border-red-500 @enderror"
                                    placeholder="email@example.com" required>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email digunakan untuk login</p>
                            </div>
                        </div>

                        {{-- Password Alert --}}
                        <div class="mt-6 p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 border border-yellow-100 dark:border-yellow-900" role="alert">
                            <div class="flex items-center">
                                <i class="bi bi-exclamation-triangle flex-shrink-0 w-4 h-4 me-2"></i>
                                <span class="font-medium me-2">Ganti Password:</span>
                                <span>Kosongkan kolom password jika tidak ingin mengubah password.</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Password --}}
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-lock me-1 text-gray-500"></i> Password Baru <span class="text-gray-400 font-normal">(Opsional)</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" placeholder="Minimal 8 karakter"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pe-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('password') border-red-500 @enderror">
                                    <button type="button" id="togglePassword" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-lock me-1 text-gray-500"></i> Konfirmasi Password <span class="text-gray-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password baru"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Role & Status Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-gear me-2 text-blue-600"></i>
                            Peran & Status
                        </h6>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Role --}}
                            <div>
                                <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-person-badge me-1 text-gray-500"></i> Peran <span class="text-red-500">*</span>
                                </label>
                                <select id="role" name="role" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('role') border-red-500 @enderror">
                                    @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->name }}"
                                            {{ $user->hasRole($role->name) ? 'selected' : '' }}
                                            {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Peran saat ini: <strong class="text-blue-600 dark:text-blue-400">{{ $user->roles->first()->name ?? 'Tidak ada' }}</strong></p>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="is_active" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-toggle-on me-1 text-gray-500"></i> Status Akun <span class="text-red-500">*</span>
                                </label>
                                <select id="is_active" name="is_active" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('is_active') border-red-500 @enderror">
                                    <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('is_active')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Status saat ini: 
                                    <strong class="{{ $user->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </strong>
                                </p>
                            </div>
                        </div>

                        @if ($user->is_active == 0)
                            <div class="mt-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-100 dark:border-red-900" role="alert">
                                <div class="flex items-center">
                                    <i class="bi bi-slash-circle flex-shrink-0 w-4 h-4 me-2"></i>
                                    <div>
                                        <span class="font-bold">Akun Nonaktif</span>
                                        <p class="mt-1">Pengguna ini saat ini tidak dapat login ke sistem.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column: Profile Picture --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 sticky top-24">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-image me-2 text-blue-600"></i>
                            Foto Profil
                        </h6>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <div class="relative w-32 h-32 mx-auto mb-4">
                                @if ($user->getFirstMediaUrl('avatars'))
                                    <img src="{{ $user->getFirstMediaUrl('avatars') }}"
                                        alt="{{ $user->name }}" id="imagePreview"
                                        class="w-full h-full rounded-full object-cover border-4 border-gray-100 dark:border-gray-700 shadow-md">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=2563EB&color=fff"
                                        alt="{{ $user->name }}" id="imagePreview"
                                        class="w-full h-full rounded-full object-cover border-4 border-gray-100 dark:border-gray-700 shadow-md">
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <p>Format: JPG, JPEG, PNG</p>
                                <p>Max: 500KB</p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <input id="image" type="file" name="image" data-max-file-size="500KB" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        </div>

                         @if ($user->getFirstMediaUrl('avatars'))
                            <button type="button" class="w-full text-red-600 hover:text-white border border-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900 transition-all mb-2" id="removePhoto">
                                <i class="bi bi-trash me-1"></i> Hapus Foto
                            </button>
                        @endif
                    </div>

                    {{-- User Info Summary --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 dark:bg-gray-700/50 dark:border-gray-600 rounded-b-xl">
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start">
                                <i class="bi bi-calendar-event text-gray-400 me-3 mt-0.5"></i>
                                <div>
                                    <span class="block text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide font-semibold">Bergabung</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="bi bi-clock-history text-gray-400 me-3 mt-0.5"></i>
                                <div>
                                    <span class="block text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide font-semibold">Terakhir Diubah</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $user->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

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
                labelIdle: '<div class="text-sm">Drag & Drop atau <span class="filepond--label-action font-semibold text-blue-600 hover:underline">Browse</span></div>',
                labelFileTypeNotAllowed: 'Tipe file tidak valid',
                fileValidateTypeLabelExpectedTypes: 'Format yang diterima: {allTypes}',
                stylePanelLayout: 'compact',
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
                    eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    eyeIcon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // Update avatar preview based on name
            $('#name').on('input', function() {
                const name = $(this).val() || '{{ $user->name }}';
                @if (!$user->getFirstMediaUrl('avatars'))
                    const avatarUrl =
                        `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=200&background=2563EB&color=fff`;
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
                    confirmButtonColor: '#E02424', // Red-600
                    cancelButtonColor: '#6B7280', // Gray-500
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
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
                            `https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=2563EB&color=fff`;
                        $('#imagePreview').attr('src', avatarUrl);
                        $(this).remove();

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Foto akan dihapus saat menyimpan'
                        })
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
                            confirmButtonColor: '#2563EB'
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
                            confirmButtonColor: '#2563EB'
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
                    confirmButtonColor: '#2563EB',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="bi bi-save me-1"></i> Ya, Simpan!',
                    cancelButtonText: '<i class="bi bi-x me-1"></i> Batal',
                    reverseButtons: true,
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
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
