@extends('layouts.app-flowbite')

@section('title', 'Ubah Mata Uang')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Mata Uang', 'url' => route('currencies.index')],
            ['text' => 'Ubah: ' . $currency->currency_name, 'url' => '#'],
        ]
    ])
@endsection

@section('content')

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
        <form action="{{ route('currencies.update', $currency) }}" method="POST" autocomplete="off" id="currency-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Form Inputs --}}
                <div class="lg:col-span-2">
                    <div class="bg-white border border-zinc-200 shadow-sm rounded-2xl overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 bg-zinc-50/50">
                            <h2 class="text-lg font-semibold text-zinc-900">
                                <i class="bi bi-pencil-square mr-2 text-blue-600"></i>
                                Informasi Mata Uang
                            </h2>
                            <p class="mt-1 text-sm text-zinc-500">Perbarui detail mata uang di bawah ini.</p>
                        </div>

                        <div class="p-6 space-y-6">
                            {{-- Currency Name --}}
                            <div>
                                <label for="currency_name" class="block mb-2 text-sm font-medium text-zinc-900">
                                    Nama Mata Uang <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="bi bi-cash text-zinc-400"></i>
                                    </div>
                                    <input type="text" name="currency_name" id="currency_name" 
                                        class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" 
                                        placeholder="Contoh: Rupiah Indonesia" 
                                        value="{{ old('currency_name', $currency->currency_name) }}" required>
                                </div>
                                <p class="mt-1 text-xs text-zinc-500">Nama resmi mata uang.</p>
                            </div>

                            {{-- Code & Symbol --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="code" class="block mb-2 text-sm font-medium text-zinc-900">
                                        Kode Mata Uang <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="bi bi-tag text-zinc-400"></i>
                                        </div>
                                        <input type="text" name="code" id="code" 
                                            class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" 
                                            placeholder="Contoh: IDR" 
                                            value="{{ old('code', $currency->code) }}" required>
                                    </div>
                                </div>
                                <div>
                                    <label for="symbol" class="block mb-2 text-sm font-medium text-zinc-900">
                                        Simbol <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="bi bi-currency-exchange text-zinc-400"></i>
                                        </div>
                                        <input type="text" name="symbol" id="symbol" 
                                            class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" 
                                            placeholder="Contoh: Rp" 
                                            value="{{ old('symbol', $currency->symbol) }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Separators --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="thousand_separator" class="block mb-2 text-sm font-medium text-zinc-900">
                                        Pemisah Ribuan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="thousand_separator" id="thousand_separator" 
                                        class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                                        placeholder="Contoh: ." 
                                        value="{{ old('thousand_separator', $currency->thousand_separator) }}" required>
                                </div>
                                <div>
                                    <label for="decimal_separator" class="block mb-2 text-sm font-medium text-zinc-900">
                                        Pemisah Desimal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="decimal_separator" id="decimal_separator" 
                                        class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                                        placeholder="Contoh: ," 
                                        value="{{ old('decimal_separator', $currency->decimal_separator) }}" required>
                                </div>
                            </div>

                            {{-- Exchange Rate --}}
                            <div>
                                <label for="exchange_rate" class="block mb-2 text-sm font-medium text-zinc-900">
                                    Nilai Tukar <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="bi bi-graph-up-arrow text-zinc-400"></i>
                                    </div>
                                    <input type="number" step="any" name="exchange_rate" id="exchange_rate" 
                                        class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" 
                                        placeholder="Nilai tukar terhadap mata uang default" 
                                        value="{{ old('exchange_rate', $currency->exchange_rate) }}" required>
                                </div>
                                <p class="mt-1 text-xs text-zinc-500">Masukkan 1 jika ini adalah mata uang default sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Preview & Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Live Preview Card --}}
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20">
                            <i class="bi bi-currency-exchange text-9xl"></i>
                        </div>
                        
                        <h3 class="text-lg font-semibold mb-4 relative z-10 border-b border-white/20 pb-2">
                            <i class="bi bi-eye mr-2"></i> Pratinjau Format
                        </h3>

                        <div class="space-y-4 relative z-10">
                            <div>
                                <label class="text-blue-100 text-xs uppercase tracking-wider font-semibold">Format Uang</label>
                                <div class="text-2xl font-bold mt-1" id="money-preview">
                                    {{ $currency->symbol }} 1.000,00
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-blue-100 text-xs uppercase tracking-wider font-semibold">Kode ISO</label>
                                <div class="text-lg font-medium mt-1" id="code-preview">
                                    {{ $currency->code }}
                                </div>
                            </div>

                            <div class="bg-white/10 rounded-lg p-3 mt-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-blue-100">Nilai Tukar:</span>
                                    <span class="font-bold" id="rate-preview">{{ $currency->exchange_rate }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Card --}}
                    <div class="bg-white border border-zinc-200 shadow-sm rounded-2xl overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 mb-4">Aksi</h3>
                            <div class="flex flex-col gap-3">
                                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all">
                                    <i class="bi bi-check-lg mr-2"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('currencies.index') }}" class="w-full text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 focus:ring-4 focus:ring-zinc-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all">
                                    <i class="bi bi-arrow-left mr-2"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('page_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live Preview Logic
            const inputs = {
                symbol: document.getElementById('symbol'),
                thousand_separator: document.getElementById('thousand_separator'),
                decimal_separator: document.getElementById('decimal_separator'),
                code: document.getElementById('code'),
                exchange_rate: document.getElementById('exchange_rate')
            };

            const previews = {
                money: document.getElementById('money-preview'),
                code: document.getElementById('code-preview'),
                rate: document.getElementById('rate-preview')
            };

            function updatePreview() {
                const symbol = inputs.symbol.value || '';
                const thousand = inputs.thousand_separator.value || '.';
                const decimal = inputs.decimal_separator.value || ',';
                const code = inputs.code.value || 'CODE';
                const rate = inputs.exchange_rate.value || '1';

                // Format number 1000 for preview
                const formattedNumber = "1" + thousand + "000" + decimal + "00";
                
                previews.money.textContent = `${symbol} ${formattedNumber}`;
                previews.code.textContent = code;
                previews.rate.textContent = rate;
            }

            // Add event listeners
            Object.values(inputs).forEach(input => {
                input.addEventListener('input', updatePreview);
            });

            // Initial update
            updatePreview();

            // Form Submit with SweetAlert
            const form = document.getElementById('currency-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Perubahan data mata uang akan disimpan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'bg-blue-600 text-white px-4 py-2 rounded-lg mr-2 hover:bg-blue-700',
                        cancelButton: 'bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700',
                        popup: 'rounded-xl'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
