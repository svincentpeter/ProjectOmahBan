@extends('layouts.app-flowbite')

@section('title', 'Profil Saya')

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
        /* ==========================
           FilePond - Cleaner Styling
        ========================== */
        .filepond--root {
            font-family: inherit;
        }

        .filepond--panel-root {
            background-color: #fff;
            border: 2px dashed #e5e7eb;
            border-radius: 0.75rem;
        }

        .dark .filepond--panel-root {
            background-color: #111827;
            border-color: #374151;
        }

        .filepond--drop-label {
            color: #6b7280;
        }

        .dark .filepond--drop-label {
            color: #9ca3af;
        }

        /* Make FilePond circle layout feel centered */
        .filepond--item-panel {
            border-radius: 9999px;
        }
    </style>
@endsection

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Home', 'url' => route('home')],
        ['text' => 'Profil', 'url' => '#'],
    ]])
@endsection

@section('content')
    @php
        $user = auth()->user();

        $avatar80 = $user->getFirstMediaUrl('avatars')
            ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=80&background=2563EB&color=fff';

        $avatar150 = $user->getFirstMediaUrl('avatars')
            ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=150&background=2563EB&color=fff';

        $roleName = $user->roles->first()->name ?? '-';

        $inputClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ' .
                      'dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';
    @endphp

    {{-- Alerts --}}
    @include('utils.alerts')

    <div class="space-y-6">
        {{-- Header / Summary Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <img src="{{ $avatar80 }}" alt="{{ $user->name }}"
                            class="w-16 h-16 rounded-full object-cover ring-4 ring-blue-50 dark:ring-blue-900 shadow-sm">
                        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                                {{ $user->name }}
                            </h3>

                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                         bg-blue-50 text-blue-700 border border-blue-100
                                         dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-900">
                                <i class="bi bi-person-badge me-1"></i> {{ $roleName }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                            <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Bergabung: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $user->created_at->format('d M Y') }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                                 bg-gray-50 text-gray-700 border border-gray-200
                                 dark:bg-gray-700/50 dark:text-gray-200 dark:border-gray-600">
                        <i class="bi bi-shield-lock me-1"></i> Keamanan Akun
                    </span>
                </div>
            </div>
        </div>

        {{-- Two Columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Profile Info --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="bi bi-person-lines-fill text-blue-600"></i>
                            Informasi Profil
                        </h6>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui nama, email, dan foto profil</p>
                    </div>
                </div>

                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        {{-- Avatar + Upload --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-start">
                            <div class="sm:col-span-1">
                                <label class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    Foto Profil
                                </label>

                                <div class="w-28 h-28">
                                    <img src="{{ $avatar150 }}" alt="{{ $user->name }}" id="profilePreview"
                                        class="w-28 h-28 rounded-full object-cover ring-4 ring-gray-100 dark:ring-gray-700 shadow-sm">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    Upload Foto
                                </label>

                                <input
                                    id="image"
                                    type="file"
                                    name="image"
                                    class="filepond"
                                    data-max-file-size="500KB"
                                >

                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Format: JPG, JPEG, PNG. Maks: 500KB.
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-5"></div>

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                <i class="bi bi-person me-1 text-gray-500"></i> Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                class="{{ $inputClass }} @error('name') border-red-500 @enderror"
                                placeholder="Masukkan nama lengkap"
                                required
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                <i class="bi bi-envelope me-1 text-gray-500"></i> Email <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="{{ $inputClass }} @error('email') border-red-500 @enderror"
                                placeholder="email@example.com"
                                required
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email digunakan untuk login ke sistem.</p>
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2
                                   text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                   font-semibold rounded-xl text-sm px-5 py-2.5
                                   dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800
                                   shadow-sm hover:shadow transition">
                            <i class="bi bi-check-circle"></i>
                            Perbarui Profil
                        </button>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-shield-lock text-blue-600"></i>
                        Ubah Password
                    </h6>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui password untuk meningkatkan keamanan</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('profile.update.password') }}" method="POST" id="password-form" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="p-4 rounded-xl border border-yellow-100 bg-yellow-50 text-yellow-900
                                    dark:bg-gray-800 dark:text-yellow-200 dark:border-yellow-900/50">
                            <div class="flex items-start gap-3">
                                <i class="bi bi-shield-exclamation text-lg mt-0.5"></i>
                                <div class="text-sm">
                                    <div class="font-bold">Keamanan Akun</div>
                                    <div class="text-xs mt-1 opacity-90">Gunakan password yang kuat untuk melindungi akun Anda.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Current --}}
                        <div>
                            <label for="current_password" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                <i class="bi bi-key me-1 text-gray-500"></i> Password Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    placeholder="Masukkan password saat ini"
                                    required
                                    class="{{ $inputClass }} pe-10 @error('current_password') border-red-500 @enderror"
                                >
                                <button type="button"
                                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700
                                           dark:text-gray-400 dark:hover:text-gray-200 toggle-password"
                                    data-target="current_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- New --}}
                        <div>
                            <label for="password" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                <i class="bi bi-lock me-1 text-gray-500"></i> Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Minimal 8 karakter"
                                    required
                                    class="{{ $inputClass }} pe-10 @error('password') border-red-500 @enderror"
                                >
                                <button type="button"
                                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700
                                           dark:text-gray-400 dark:hover:text-gray-200 toggle-password"
                                    data-target="password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm --}}
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                                <i class="bi bi-lock-fill me-1 text-gray-500"></i> Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Ketik ulang password baru"
                                    required
                                    class="{{ $inputClass }} pe-10"
                                >
                                <button type="button"
                                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700
                                           dark:text-gray-400 dark:hover:text-gray-200 toggle-password"
                                    data-target="password_confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="p-4 rounded-xl bg-blue-50 text-blue-900 border border-blue-100
                                    dark:bg-gray-700/40 dark:text-blue-200 dark:border-blue-900/50">
                            <div class="text-xs">
                                <div class="font-bold mb-2"><i class="bi bi-lightbulb me-1"></i> Tips Password Aman</div>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Minimal 8 karakter</li>
                                    <li>Kombinasi huruf besar & kecil</li>
                                    <li>Tambahkan angka dan simbol</li>
                                    <li>Jangan gunakan informasi pribadi</li>
                                </ul>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2
                                   text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300
                                   font-semibold rounded-xl text-sm px-5 py-2.5
                                   dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800
                                   shadow-sm hover:shadow transition">
                            <i class="bi bi-shield-check"></i>
                            Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
            // Toggle Password Visibility
            $('.toggle-password').on('click', function() {
                const targetId = $(this).data('target');
                const input = $('#' + targetId);
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // Initialize FilePond
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType
            );

            const fileElement = document.querySelector('#image');
            FilePond.create(fileElement, {
                acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
                labelIdle: '<div class="text-sm">Drag & Drop atau <span class="filepond--label-action font-semibold text-blue-600 hover:underline">Browse</span></div>',
                labelFileTypeNotAllowed: 'Tipe file tidak valid',
                fileValidateTypeLabelExpectedTypes: 'Format yang diterima: {allTypes}',
                stylePanelLayout: 'compact circle',
                imagePreviewHeight: 150,
                imageCropAspectRatio: '1:1',
                imageResizeTargetWidth: 150,
                imageResizeTargetHeight: 150,
                styleLoadIndicatorPosition: 'center bottom',
                styleProgressIndicatorPosition: 'right bottom',
                styleButtonRemoveItemPosition: 'center bottom',
                styleButtonProcessItemPosition: 'right bottom',
            });

            FilePond.setOptions({
                server: {
                    url: "{{ route('filepond.upload') }}",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                }
            });

            // Update avatar preview based on name (only if no uploaded avatar)
            $('#name').on('input', function() {
                const name = $(this).val() || '{{ $user->name }}';
                @if (!$user->getFirstMediaUrl('avatars'))
                    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=150&background=2563EB&color=fff`;
                    $('#profilePreview').attr('src', avatarUrl);
                @endif
            });

            // Profile Form Validation + Loading
            $('#profile-form').on('submit', function(e) {
                const name = $('#name').val().trim();
                const email = $('#email').val().trim();

                if (!name || !email) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi semua field yang wajib diisi',
                        confirmButtonColor: '#2563EB'
                    });
                    return false;
                }

                Swal.fire({
                    title: 'Memperbarui Profil...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            });

            // Password Form Validation + Confirm
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
                        confirmButtonColor: '#2563EB'
                    });
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Tidak Cocok',
                        text: 'Password baru dan konfirmasi password harus sama',
                        confirmButtonColor: '#2563EB'
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
                        confirmButtonColor: '#2563EB'
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
                        confirmButtonColor: '#2563EB'
                    });
                    $('#password').focus();
                    return false;
                }

                e.preventDefault();
                Swal.fire({
                    title: 'Ubah Password?',
                    text: 'Anda akan logout setelah password berhasil diubah',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="bi bi-shield-check me-1"></i> Ya, Ubah!',
                    cancelButtonText: '<i class="bi bi-x me-1"></i> Batal',
                    reverseButtons: true,
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mengubah Password...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        e.target.submit();
                    }
                });
            });
        });
    </script>
@endpush
