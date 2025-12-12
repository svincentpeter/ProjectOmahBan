@extends('layouts.app-flowbite')

@section('title', 'Buat Pengguna Baru')

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        /* FilePond Custom Styling for Flowbite Theme */
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
        ['text' => 'Buat Pengguna', 'url' => '#']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="user-form">
        @csrf

        {{-- Sticky Action Bar --}}
        <div class="sticky top-[72px] z-50 mb-6 p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 transition-transform duration-300" id="actionBar">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h5 class="flex items-center text-lg font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-person-plus me-2 text-blue-600"></i>
                         Buat Pengguna Baru
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Tambahkan pengguna baru ke sistem</p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('users.index') }}" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition-all font-medium text-sm">
                        <i class="bi bi-x me-1"></i> Batal
                    </a>
                    <button type="submit" class="w-1/2 sm:w-auto text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-all font-medium text-sm shadow-md hover:shadow-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan Pengguna
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
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('email') border-red-500 @enderror"
                                    placeholder="email@example.com" required>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email akan digunakan untuk login</p>
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-lock me-1 text-gray-500"></i> Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required
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
                                    <i class="bi bi-lock me-1 text-gray-500"></i> Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            </div>
                        </div>

                        {{-- Password Tips --}}
                        <div class="mt-6 p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 border border-blue-100 dark:border-blue-900" role="alert">
                            <div class="flex items-start">
                                <i class="bi bi-shield-lock flex-shrink-0 w-4 h-4 me-2 mt-0.5"></i>
                                <div>
                                    <span class="font-medium">Tips Password Aman:</span>
                                    <ul class="mt-1.5 ms-4 list-disc list-inside">
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
                                    <option value="" selected disabled>-- Pilih Peran --</option>
                                    @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Peran menentukan hak akses pengguna</p>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="is_active" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-toggle-on me-1 text-gray-500"></i> Status Akun <span class="text-red-500">*</span>
                                </label>
                                <select id="is_active" name="is_active" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('is_active') border-red-500 @enderror">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('is_active')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pengguna nonaktif tidak dapat login</p>
                            </div>
                        </div>
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
                                <img id="imagePreview" 
                                    src="https://ui-avatars.com/api/?name=User&size=200&background=2563EB&color=fff"
                                    alt="Preview" 
                                    class="w-full h-full rounded-full object-cover border-4 border-gray-100 dark:border-gray-700 shadow-md">
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <p>Format: JPG, JPEG, PNG</p>
                                <p>Max: 500KB</p>
                            </div>
                        </div>
                        
                        <div>
                            <input id="image" type="file" name="image" data-max-file-size="500KB" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
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
                const name = $(this).val() || 'User';
                const avatarUrl =
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=200&background=2563EB&color=fff`;
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
                        confirmButtonColor: '#2563EB' // blue-600
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
                        confirmButtonColor: '#2563EB' // blue-600
                    });
                    $('#password').focus();
                    return false;
                }

                // Show loading
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        });
    </script>
@endpush
