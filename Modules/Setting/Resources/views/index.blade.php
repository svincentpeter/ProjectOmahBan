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
@endsection
