@extends('layouts.app-flowbite')

@section('title', 'Tambah Mata Uang')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Tambah Mata Uang',
        'items' => [
            ['text' => 'Home', 'url' => route('home')],
            ['text' => 'Mata Uang', 'url' => route('currencies.index')],
            ['text' => 'Tambah', 'url' => '#'],
        ],
    ])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    <form action="{{ route('currencies.store') }}" method="POST" autocomplete="off" id="currency-form">
        @csrf

        {{-- Form Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Column: Form Fields --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Card --}}
                <div class="bg-white border border-zinc-200 shadow-sm rounded-2xl overflow-hidden p-6">
                    <div class="border-b border-zinc-100 pb-4 mb-6">
                        <h2 class="text-lg font-bold text-zinc-800 flex items-center gap-2">
                            <i class="bi bi-plus-circle text-blue-600"></i>
                            Informasi Mata Uang
                        </h2>
                        <p class="text-sm text-zinc-500 mt-1">Isi detail mata uang baru yang akan ditambahkan.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Name --}}
                        <div class="col-span-2">
                            <label for="currency_name" class="block text-sm font-medium text-zinc-700 mb-2">
                                Nama Mata Uang <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-cursor-text"></i>
                                </span>
                                <input type="text" id="currency_name" name="currency_name"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block @error('currency_name') border-red-500 @enderror"
                                    placeholder="Contoh: Rupiah Indonesia" value="{{ old('currency_name') }}" required>
                            </div>
                            @error('currency_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Code --}}
                        <div>
                            <label for="code" class="block text-sm font-medium text-zinc-700 mb-2">
                                Kode (ISO 4217) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-upc-scan"></i>
                                </span>
                                <input type="text" id="code" name="code"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block uppercase @error('code') border-red-500 @enderror"
                                    placeholder="IDR" value="{{ old('code') }}" maxlength="3" required>
                            </div>
                            <p class="mt-1 text-xs text-zinc-500">Maksimal 3 karakter (ex: IDR, USD)</p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Symbol --}}
                        <div>
                            <label for="symbol" class="block text-sm font-medium text-zinc-700 mb-2">
                                Simbol <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-currency-exchange"></i>
                                </span>
                                <input type="text" id="symbol" name="symbol"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block @error('symbol') border-red-500 @enderror"
                                    placeholder="Rp" value="{{ old('symbol', 'Rp') }}" required>
                            </div>
                            @error('symbol')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Thousand Separator --}}
                        <div>
                            <label for="thousand_separator" class="block text-sm font-medium text-zinc-700 mb-2">
                                Pemisah Ribuan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-grid-3x3"></i>
                                </span>
                                <select id="thousand_separator" name="thousand_separator"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block @error('thousand_separator') border-red-500 @enderror"
                                    required>
                                    <option value="." {{ old('thousand_separator', '.') == '.' ? 'selected' : '' }}>Titik (.)</option>
                                    <option value="," {{ old('thousand_separator') == ',' ? 'selected' : '' }}>Koma (,)</option>
                                    <option value=" " {{ old('thousand_separator') == ' ' ? 'selected' : '' }}>Spasi ( )</option>
                                </select>
                            </div>
                            @error('thousand_separator')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Decimal Separator --}}
                        <div>
                            <label for="decimal_separator" class="block text-sm font-medium text-zinc-700 mb-2">
                                Pemisah Desimal <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-dot"></i>
                                </span>
                                <select id="decimal_separator" name="decimal_separator"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block @error('decimal_separator') border-red-500 @enderror"
                                    required>
                                    <option value="," {{ old('decimal_separator', ',') == ',' ? 'selected' : '' }}>Koma (,)</option>
                                    <option value="." {{ old('decimal_separator') == '.' ? 'selected' : '' }}>Titik (.)</option>
                                </select>
                            </div>
                            @error('decimal_separator')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                     {{-- Format Info Alert --}}
                     <div class="mt-6 p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 flex items-start gap-3" role="alert">
                        <i class="bi bi-info-circle flex-shrink-0 text-lg"></i>
                        <div>
                            <span class="font-medium block">Format Tampilan Sistem</span>
                            Sistem menggunakan format standar: <span class="font-bold">Rp 100.000</span> (Tanpa desimal, dengan pemisah ribuan sesuai konfigurasi).
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Preview & Actions --}}
            <div class="lg:col-span-1 space-y-6">
                 {{-- Actions --}}
                 <div class="bg-white border border-zinc-200 shadow-sm rounded-2xl overflow-hidden p-6 sticky top-24">
                    <div class="border-b border-zinc-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-zinc-800">Aksi</h2>
                    </div>
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all shadow-sm">
                            <i class="bi bi-check-lg mr-2"></i> Simpan Mata Uang
                        </button>
                        <a href="{{ route('currencies.index') }}" class="w-full px-4 py-2.5 bg-zinc-100 text-zinc-700 font-medium rounded-xl hover:bg-zinc-200 focus:ring-4 focus:ring-zinc-100 transition-all text-center">
                            Batal
                        </a>
                    </div>

                    <hr class="my-6 border-zinc-100">

                    {{-- Preview Section --}}
                    <div>
                        <h3 class="text-sm font-bold text-zinc-700 mb-3 uppercase tracking-wider">Preview Format</h3>
                        <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-6 text-center mb-4">
                            <span class="block text-xs text-zinc-500 mb-1 uppercase tracking-wide font-bold">Contoh Tampilan</span>
                            <div class="text-2xl font-black text-blue-600" id="formatPreview">
                                Rp 100.000
                            </div>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b border-zinc-100">
                                <span class="text-zinc-500">Mata Uang</span>
                                <span class="font-semibold text-zinc-800 text-right" id="previewName">-</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-zinc-100">
                                <span class="text-zinc-500">Kode</span>
                                <span class="font-semibold text-zinc-800 font-mono" id="previewCode">-</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-zinc-100">
                                <span class="text-zinc-500">Simbol</span>
                                <span class="font-semibold text-zinc-800" id="previewSymbol">-</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-zinc-100">
                                <span class="text-zinc-500">Pemisah Ribuan</span>
                                <span class="font-semibold text-zinc-800" id="previewThousand">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Update preview live
            function updatePreview() {
                const name = $('#currency_name').val() || '-';
                const code = $('#code').val().toUpperCase() || '-';
                const symbol = $('#symbol').val() || '-';
                const thousand = $('#thousand_separator').val() || '.';

                // Update preview details
                $('#previewName').text(name);
                $('#previewCode').text(code);
                $('#previewSymbol').text(symbol);

                // Format thousand separator name
                let thousandName = '';
                switch (thousand) {
                    case '.':
                        thousandName = 'Titik (.)';
                        break;
                    case ',':
                        thousandName = 'Koma (,)';
                        break;
                    case ' ':
                        thousandName = 'Spasi ( )';
                        break;
                    default:
                        thousandName = thousand;
                }
                $('#previewThousand').text(thousandName);

                // Format preview display
                const formattedNumber = formatCurrency(100000, symbol, thousand);
                $('#formatPreview').text(formattedNumber);
            }

            // Format currency helper
            function formatCurrency(amount, symbol, separator) {
                const formatted = amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator);
                return symbol + ' ' + formatted;
            }

            // Auto uppercase code
            $('#code').on('input', function() {
                $(this).val($(this).val().toUpperCase());
                updatePreview();
            });

            // Update preview on input
            $('#currency_name, #symbol, #thousand_separator, #decimal_separator').on('input change', updatePreview);

            // Initialize preview
            updatePreview();

            // Form Validation and Submission
            $('#currency-form').on('submit', function(e) {
                const name = $('#currency_name').val().trim();
                const code = $('#code').val().trim();
                const symbol = $('#symbol').val().trim();

                if (!name || !code || !symbol) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi semua field yang wajib diisi',
                        confirmButtonColor: '#2563EB',
                        customClass: {
                            confirmButton: 'swal2-confirm-btn-blue',
                            popup: 'swal2-popup-custom'
                        }
                    });
                    return false;
                }

                if (code.length !== 3) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Kode Tidak Valid',
                        text: 'Kode mata uang harus 3 karakter (ISO 4217)',
                        confirmButtonColor: '#2563EB',
                        customClass: {
                            confirmButton: 'swal2-confirm-btn-blue',
                            popup: 'swal2-popup-custom'
                        }
                    });
                    $('#code').focus();
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

            // Auto-focus first input
            $('#currency_name').focus();
        });
    </script>
@endpush
