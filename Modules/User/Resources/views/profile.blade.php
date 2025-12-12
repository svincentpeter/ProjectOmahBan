@extends('layouts.app-flowbite')

@section('title', 'Profil Saya')

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
        ['text' => 'Home', 'url' => route('home')],
        ['text' => 'Profil', 'url' => '#']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Welcome Banner --}}
    <div class="mb-6 p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row items-center gap-6">
            <div class="relative w-20 h-20">
                @if (auth()->user()->getFirstMediaUrl('avatars'))
                    <img src="{{ auth()->user()->getFirstMediaUrl('avatars') }}"
                        alt="{{ auth()->user()->name }}"
                        class="w-full h-full rounded-full object-cover border-4 border-blue-100 dark:border-blue-900 shadow-md">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=80&background=2563EB&color=fff"
                        alt="{{ auth()->user()->name }}"
                        class="w-full h-full rounded-full object-cover border-4 border-blue-100 dark:border-blue-900 shadow-md">
                @endif
                <div class="absolute bottom-0 right-0 w-5 h-5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
            </div>
            <div class="text-center sm:text-left">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    Halo, <span class="text-blue-600 dark:text-blue-500">{{ auth()->user()->name }}</span>! 👋
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Kelola informasi profil dan keamanan akun Anda di sini
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Profile Info --}}
        <div>
            <div class="h-full bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="bi bi-person-badge me-2 text-blue-600"></i>
                        Informasi Profil
                    </h6>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
                        @csrf
                        @method('PATCH')

                        <div class="text-center mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Foto Profil</label>
                            <div class="relative w-32 h-32 mx-auto mb-4">
                                @if (auth()->user()->getFirstMediaUrl('avatars'))
                                    <img src="{{ auth()->user()->getFirstMediaUrl('avatars') }}"
                                        alt="{{ auth()->user()->name }}" id="profilePreview"
                                        class="w-full h-full rounded-full object-cover border-4 border-gray-100 dark:border-gray-700 shadow-md">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=150&background=2563EB&color=fff"
                                        alt="{{ auth()->user()->name }}" id="profilePreview"
                                        class="w-full h-full rounded-full object-cover border-4 border-gray-100 dark:border-gray-700 shadow-md">
                                @endif
                            </div>
                            
                            <div class="mb-2">
                                <input id="image" type="file" name="image" data-max-file-size="500KB" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            </div>
                            <p class="text-center text-xs text-gray-500 dark:text-gray-400">Format: JPG, JPEG, PNG. Max: 500KB</p>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-person me-1 text-gray-500"></i> Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                                    placeholder="Masukkan nama lengkap" required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-envelope me-1 text-gray-500"></i> Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('email') border-red-500 @enderror"
                                    placeholder="email@example.com" required>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email digunakan untuk login ke sistem</p>
                            </div>

                            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 shadow-md hover:shadow-lg transition-all mt-4">
                                <i class="bi bi-check-circle me-2"></i> Perbarui Profil
                            </button>
                        </div>
                    </form>
                </div>
                
                {{-- Footer Info --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 dark:bg-gray-700/50 dark:border-gray-600 rounded-b-xl">
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Peran</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">{{ auth()->user()->roles->first()->name ?? '-' }}</span>
                        </div>
                        <div class="text-right">
                             <span class="block text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Bergabung</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Change Password --}}
        <div>
            <div class="h-full bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="bi bi-shield-lock me-2 text-blue-600"></i>
                        Ubah Password
                    </h6>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update.password') }}" method="POST" id="password-form">
                        @csrf
                        @method('PATCH')

                        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 border border-yellow-100 dark:border-yellow-900 flex items-center" role="alert">
                            <i class="bi bi-shield-exclamation flex-shrink-0 w-5 h-5 me-2"></i>
                            <div>
                                <span class="font-bold">Keamanan Akun</span>
                                <p class="mt-1 text-xs">Gunakan password yang kuat untuk melindungi akun Anda.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-key me-1 text-gray-500"></i> Password Saat Ini <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="current_password" name="current_password" placeholder="Masukkan password saat ini" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pe-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('current_password') border-red-500 @enderror">
                                    <button type="button" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 toggle-password" data-target="current_password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-lock me-1 text-gray-500"></i> Password Baru <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pe-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('password') border-red-500 @enderror">
                                    <button type="button" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 toggle-password" data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <i class="bi bi-lock-fill me-1 text-gray-500"></i> Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password baru" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pe-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <button type="button" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 toggle-password" data-target="password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-blue-50 text-blue-800 rounded-lg text-xs dark:bg-gray-700/50 dark:text-blue-300">
                                <strong class="block mb-1"><i class="bi bi-lightbulb me-1"></i> Tips Password Aman:</strong>
                                <ul class="list-disc list-inside space-y-0.5 ps-1">
                                    <li>Minimal 8 karakter</li>
                                    <li>Kombinasi huruf besar & kecil</li>
                                    <li>Tambahkan angka dan simbol</li>
                                    <li>Jangan gunakan informasi pribadi</li>
                                </ul>
                            </div>

                            <button type="submit" class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 shadow-md hover:shadow-lg transition-all mt-4">
                                <i class="bi bi-shield-check me-2"></i> Ubah Password
                            </button>
                        </div>
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
            // Password Toggle
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

            const fileElement = document.querySelector('input[id="image"]');
            const pond = FilePond.create(fileElement, {
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
                        confirmButtonColor: '#2563EB'
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

                // Confirmation
                e.preventDefault();
                Swal.fire({
                    title: 'Ubah Password?',
                    text: 'Anda akan logout setelah password berhasil diubah',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a', // Green-600
                    cancelButtonColor: '#6B7280', // Gray-500
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
                        `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=150&background=2563EB&color=fff`;
                    $('#profilePreview').attr('src', avatarUrl);
                @endif
            });
        });
    </script>
@endpush
