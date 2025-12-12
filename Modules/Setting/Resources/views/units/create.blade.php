@extends('layouts.app-flowbite')

@section('title', 'Tambah Satuan')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <i class="bi bi-house-door-fill mr-2"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400"></i>
                    <a href="{{ route('units.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Satuan</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Tambah</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="px-4 pt-6">
    @include('utils.alerts')

    <form action="{{ route('units.store') }}" method="POST" autocomplete="off" id="unit-form">
        @csrf
        
        {{-- Sticky Action Bar --}}
        <div class="sticky top-0 z-40 mb-4 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-plus-circle mr-2 text-blue-600"></i>Tambah Satuan Baru
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Buat satuan unit baru untuk produk</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('units.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                        Batal
                    </a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        <i class="bi bi-check-circle mr-1"></i> Simpan Satuan
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2">
                <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center mb-4 border-b pb-4 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            <i class="bi bi-file-text mr-2 text-primary"></i>Detail Satuan
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Unit Name --}}
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                <i class="bi bi-tag mr-1 text-gray-500"></i> Nama Satuan <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                placeholder="Contoh: Buah, Pcs, Set"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror" required>
                             @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nama lengkap satuan</p>
                        </div>

                        {{-- Short Name --}}
                        <div>
                            <label for="short_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                <i class="bi bi-fonts mr-1 text-gray-500"></i> Singkatan <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="short_name" name="short_name" value="{{ old('short_name') }}" 
                                placeholder="Contoh: pcs, set, kg" maxlength="10"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('short_name') border-red-500 @enderror" required>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Maksimal 10 karakter</p>
                                <span id="char-counter" class="text-xs text-gray-500 dark:text-gray-400">0/10</span>
                            </div>
                             @error('short_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Note --}}
                        <div class="md:col-span-2">
                            <label for="note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                <i class="bi bi-sticky mr-1 text-gray-500"></i> Keterangan <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <textarea id="note" name="note" rows="3" 
                                placeholder="Tambahkan keterangan atau deskripsi satuan..."
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('note') border-red-500 @enderror">{{ old('note') }}</textarea>
                             @error('note')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                {{-- Examples Card --}}
                <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="bi bi-lightbulb text-yellow-400 mr-2"></i> Contoh Satuan
                    </h4>
                    <ul class="space-y-4">
                         <li class="p-3 bg-gray-50 rounded-lg border border-gray-100 dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-blue-600 dark:text-blue-400">Pcs</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pieces (untuk barang satuan)</p>
                        </li>
                         <li class="p-3 bg-gray-50 rounded-lg border border-gray-100 dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-blue-600 dark:text-blue-400">Set</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Set (untuk paket produk)</p>
                        </li>
                         <li class="p-3 bg-gray-50 rounded-lg border border-gray-100 dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-blue-600 dark:text-blue-400">Kg</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kilogram (untuk berat)</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shortNameInput = document.getElementById('short_name');
        const charCounter = document.getElementById('char-counter');
        const maxLength = 10;

        // Auto-focus first input
        document.getElementById('name').focus();

        // Char counter
        shortNameInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCounter.textContent = `${currentLength}/${maxLength}`;
            
            if (currentLength >= maxLength) {
                charCounter.classList.add('text-red-600', 'font-bold');
            } else {
                charCounter.classList.remove('text-red-600', 'font-bold');
            }
        });

        // Form Submission with SweetAlert2
        const form = document.getElementById('unit-form');
         form.addEventListener('submit', function(e) {
            e.preventDefault();

             const name = document.getElementById('name').value.trim();
             const shortName = shortNameInput.value.trim();

             if (!name || !shortName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Mohon lengkapi nama satuan dan singkatan!',
                    confirmButtonColor: '#1A56DB', // Blue-700
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                });
                return;
            }

            Swal.fire({
                title: 'Simpan Satuan?',
                text: "Pastikan data yang Anda masukkan sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1A56DB',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
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
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
