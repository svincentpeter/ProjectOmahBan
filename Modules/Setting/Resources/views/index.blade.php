@extends('layouts.app-flowbite')

@section('title', 'Pengaturan Sistem')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Pengaturan', 'url' => '#'],
        ['text' => 'Umum', 'url' => '#']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Header Banner --}}
    <div class="mb-6 p-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <i class="bi bi-gear-wide-connected text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold tracking-tight">Pengaturan Sistem</h3>
                    <p class="text-blue-100 mt-1 opacity-90">Kelola informasi dasar perusahaan dan konfigurasi utama aplikasi.</p>
                </div>
            </div>
            <div>
                <button type="submit" form="settings-form" class="text-white bg-white/20 hover:bg-white/30 backdrop-blur-sm focus:ring-4 focus:ring-white/20 font-semibold rounded-xl text-sm px-6 py-3 transition-all shadow-lg border border-white/30">
                    <i class="bi bi-check-circle-fill me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    <form id="settings-form" action="{{ route('settings.update') }}" method="POST" autocomplete="off">
        @csrf
        @method('PATCH')
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            {{-- Main Column --}}
            <div class="xl:col-span-2 space-y-6">
                
                {{-- Company Info Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                            <i class="bi bi-building-fill text-blue-600 text-lg"></i>
                        </div>
                        <h5 class="font-bold text-lg text-slate-800 dark:text-white">Informasi Perusahaan</h5>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Company Name --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="company_name" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Nama Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                        <i class="bi bi-building text-slate-500 dark:text-slate-400"></i>
                                    </div>
                                    <input type="text" id="company_name" name="company_name" 
                                        value="{{ old('company_name', $settings->company_name) }}"
                                        class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all shadow-sm" 
                                        placeholder="Contoh: PT. Omah Ban Indonesia" required>
                                </div>
                                @error('company_name')
                                    <p class="mt-2 text-sm text-red-600"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Company Email --}}
                            <div>
                                <label for="company_email" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Email Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                        <i class="bi bi-envelope text-slate-500 dark:text-slate-400"></i>
                                    </div>
                                    <input type="email" id="company_email" name="company_email" 
                                        value="{{ old('company_email', $settings->company_email) }}"
                                        class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all shadow-sm" 
                                        placeholder="name@company.com" required>
                                </div>
                                @error('company_email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Company Phone --}}
                            <div>
                                <label for="company_phone" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Telepon Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                        <i class="bi bi-telephone text-slate-500 dark:text-slate-400"></i>
                                    </div>
                                    <input type="text" id="company_phone" name="company_phone" 
                                        value="{{ old('company_phone', $settings->company_phone) }}"
                                        class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all shadow-sm" 
                                        placeholder="08123456789" required>
                                </div>
                                @error('company_phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="company_address" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                     <div class="absolute top-3 start-0 flex items-start ps-3.5 pointer-events-none">
                                        <i class="bi bi-geo-alt text-slate-500 dark:text-slate-400"></i>
                                    </div>
                                    <textarea id="company_address" name="company_address" rows="3" 
                                        class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all shadow-sm"
                                        placeholder="Alamat lengkap perusahaan..." required>{{ old('company_address', $settings->company_address) }}</textarea>
                                </div>
                                @error('company_address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notification Settings --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                         <div class="p-2 bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300 rounded-lg">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h5 class="font-bold text-lg text-slate-800 dark:text-white">Pengaturan Notifikasi</h5>
                    </div>
                    <div class="p-6">
                        <div>
                            <label for="notification_email" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Email Penerima Notifikasi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <i class="bi bi-envelope-at text-slate-500 dark:text-slate-400"></i>
                                </div>
                                <input type="email" id="notification_email" name="notification_email" 
                                    value="{{ old('notification_email', $settings->notification_email) }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all shadow-sm" 
                                    placeholder="notifikasi@company.com">
                            </div>
                            <p class="mt-2 text-xs text-slate-500"><i class="bi bi-info-circle me-1"></i>Email ini akan digunakan untuk menerima notifikasi sistem penting.</p>
                             @error('notification_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- Side Column --}}
            <div class="xl:col-span-1 space-y-6">

                {{-- Backup & Restore --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                            <div class="p-2 bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300 rounded-lg">
                            <i class="bi bi-database-fill-gear"></i>
                        </div>
                        <h5 class="font-bold text-lg text-slate-800 dark:text-white">Backup & Restore</h5>
                    </div>
                    
                    <div class="p-6 space-y-6">
                            {{-- Backup Section --}}
                            <div>
                            <h6 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Backup Database</h6>
                            <a href="{{ route('settings.backup') }}" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-3 text-center flex items-center justify-center gap-2 transition-all">
                                <i class="bi bi-download text-lg"></i> Download .sql
                            </a>
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                Unduh backup database secara berkala untuk mengamankan data transaksi Anda.
                            </p>
                            </div>

                        <hr class="border-slate-100 dark:border-gray-700">

                        {{-- Restore Section --}}
                        <div>
                            <h6 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Restore Database</h6>
                            <button type="button" data-modal-target="restore-modal" data-modal-toggle="restore-modal" class="w-full text-slate-700 bg-slate-100 hover:bg-slate-200 focus:ring-4 focus:ring-slate-100 font-medium rounded-xl text-sm px-5 py-3 text-center flex items-center justify-center gap-2 transition-all dark:bg-gray-700 dark:text-slate-300 dark:hover:bg-gray-600">
                                <i class="bi bi-arrow-counterclockwise text-lg"></i> Upload & Restore
                            </button>
                            <p class="mt-2 text-xs text-red-500 leading-relaxed">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Hati-hati! Restore akan menimpa seluruh data saat ini.
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- Currency Settings --}}
               <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                         <div class="p-2 bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300 rounded-lg">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h5 class="font-bold text-lg text-slate-800 dark:text-white">Mata Uang</h5>
                    </div>
                    
                    <div class="p-6 space-y-4">
                         {{-- Default Currency --}}
                         <div>
                            <label class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Mata Uang Default
                            </label>
                            <select id="default_currency_id" class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400" disabled>
                                @foreach (\Modules\Currency\Entities\Currency::query()->orderBy('currency_name')->get() as $currency)
                                    <option value="{{ $currency->id }}"
                                        {{ old('default_currency_id', $settings->default_currency_id) == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->currency_name }} ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="default_currency_id" value="{{ old('default_currency_id', $settings->default_currency_id) }}">
                         </div>

                        {{-- Currency Position --}}
                        <div>
                             <label class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Posisi Simbol
                            </label>
                            <select class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400" disabled>
                                <option selected>Prefix (Rp 100.000)</option>
                            </select>
                             <input type="hidden" name="default_currency_position" value="prefix">
                        </div>

                         <div class="p-4 rounded-xl bg-orange-50 border border-orange-100 text-orange-800 dark:bg-gray-700 dark:border-gray-600 dark:text-orange-300 text-sm">
                            <div class="flex items-start gap-2">
                                <i class="bi bi-lock-fill mt-0.5"></i>
                                <div>
                                    <span class="font-bold block">Pengaturan Terkunci</span>
                                    Untuk menjaga konsistensi data finansial, pengaturan mata uang tidak dapat diubah sembarangan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Preview Card --}}
                 <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-xl text-white overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="bi bi-receipt text-9xl"></i>
                    </div>
                    <div class="p-6 relative z-10">
                        <h5 class="text-lg font-bold mb-4 flex items-center gap-2">
                            <i class="bi bi-eye"></i> Preview Format
                        </h5>
                        
                        <div class="space-y-4 font-mono text-sm">
                            <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                                <span class="text-slate-400">Nominal</span>
                                <span class="font-bold text-green-400">Rp 150.000</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                                <span class="text-slate-400">Diskon</span>
                                <span class="font-bold text-red-400">-Rp 10.000</span>
                            </div>
                            <div class="flex justify-between items-center pt-2">
                                <span class="text-slate-300 font-bold">Total</span>
                                <span class="font-bold text-xl text-white">Rp 140.000</span>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-slate-700 text-xs text-slate-400">
                            *Format ini akan diterapkan pada seluruh sistem (Invoice, POS, Laporan).
                        </div>
                    </div>
                 </div>

            </div>
        </div>
    </form>

    {{-- Restore Modal --}}
    <div id="restore-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Restore Database
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="restore-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('settings.restore') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                    @csrf
                    <div class="space-y-4">
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <span class="font-medium">PERINGATAN KERAS!</span>
                            Proses ini akan <b>MENGHAPUS SELURUH DATA</b> yang ada saat ini. Pastikan Anda sudah membackup data sebelum melanjutkan.
                        </div>
                        
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="backup_file">Upload File SQL</label>
                        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="backup_file" name="backup_file" type="file" accept=".sql" required>
                    </div>
                    <div class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b dark:border-gray-600 mt-4">
                        <button data-modal-hide="restore-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Batal</button>
                        <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 ms-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
