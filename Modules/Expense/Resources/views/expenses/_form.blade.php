@csrf

<div class="space-y-8">
    
    {{-- Section: Nominal --}}
    <div class="bg-blue-50/50 dark:bg-blue-900/20 p-6 rounded-xl border border-blue-100 dark:border-blue-800/50">
        <label for="amount" class="block mb-3 text-sm font-semibold text-blue-900 dark:text-blue-100 uppercase tracking-wider">
            Nominal Pengeluaran <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                <span class="text-blue-600 font-bold text-lg">Rp</span>
            </div>
            <input type="text" id="amount" name="amount"
                class="bg-white border border-blue-200 text-blue-800 text-2xl font-bold rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full ps-12 p-4 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:placeholder-zinc-400 placeholder-zinc-400 dark:text-white js-money transition-all"
                value="{{ number_format((int) old('amount', $expense->amount ?? 0), 0, ',', '.') }}"
                placeholder="0" required autofocus>
        </div>
        @error('amount')
            <p class="mt-2 text-sm text-red-600 font-medium"><i class="bi bi-x-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    {{-- Section: Detail Transaksi --}}
    <div>
        <h6 class="text-gray-900 dark:text-gray-100 font-bold mb-4 flex items-center">
            <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-2 text-gray-500">
                <i class="bi bi-receipt"></i>
            </span>
            Detail Transaksi
        </h6>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tanggal --}}
            <div>
                <label for="date" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tanggal Transaksi <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <i class="bi bi-calendar-event text-gray-400"></i>
                    </div>
                    <input type="date" name="date" id="date" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors"
                        value="{{ old('date', optional($expense->date ?? null)->toDateString() ?? now()->toDateString()) }}"
                        required>
                </div>
                @error('date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
    
            {{-- Kategori --}}
            <div>
                <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kategori Pengeluaran <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <i class="bi bi-tag text-gray-400"></i>
                    </div>
                    <select name="category_id" id="category_id" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors" required>
                        <option value="" disabled {{ old('category_id', $expense->category_id ?? null) ? '' : 'selected' }}>Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $expense->category_id ?? null) == $cat->id)>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('category_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi (Full Width) --}}
            <div class="md:col-span-2">
                <label for="details" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Keterangan / Keperluan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <i class="bi bi-fonts text-gray-400"></i>
                    </div>
                    <input type="text" name="details" id="details" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-zinc-400 placeholder-zinc-400 transition-colors"
                        placeholder="Contoh: Pembelian Alat Tulis Kantor"
                        value="{{ old('details', $expense->details ?? null) }}" required>
                </div>
                @error('details')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700">

    {{-- Section: Pembayaran --}}
    <div>
        <h6 class="text-gray-900 dark:text-gray-100 font-bold mb-4 flex items-center">
            <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-2 text-gray-500">
                <i class="bi bi-wallet2"></i>
            </span>
            Metode Pembayaran
        </h6>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Payment Method Selection --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pilih Metode <span class="text-red-500">*</span>
                </label>
                @php $pm = old('payment_method', $expense->payment_method ?? 'Tunai'); @endphp
                
                <div class="grid grid-cols-2 gap-4">
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" id="pm_cash" value="Tunai" class="peer sr-only" @checked($pm === 'Tunai')>
                        <div class="p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50/50 transition-all text-center h-full flex flex-col items-center justify-center gap-2">
                            <i class="bi bi-cash-stack text-2xl text-gray-500 peer-checked:text-blue-600"></i>
                            <span class="text-sm font-medium text-gray-700 peer-checked:text-blue-700">Tunai</span>
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <i class="bi bi-check-circle-fill text-blue-600"></i>
                        </div>
                    </label>
    
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" id="pm_transfer" value="Transfer" class="peer sr-only" @checked($pm === 'Transfer')>
                        <div class="p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50/50 transition-all text-center h-full flex flex-col items-center justify-center gap-2">
                            <i class="bi bi-bank2 text-2xl text-gray-500 peer-checked:text-blue-600"></i>
                            <span class="text-sm font-medium text-gray-700 peer-checked:text-blue-700">Transfer</span>
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <i class="bi bi-check-circle-fill text-blue-600"></i>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Bank Details (Conditional) --}}
            <div>
                <label for="bank_name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Akun Bank <span class="text-xs font-normal text-gray-500">(Jika Transfer)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <i class="bi bi-credit-card-2-front text-gray-400"></i>
                    </div>
                    <input type="text" name="bank_name" id="bank_name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-zinc-400 placeholder-zinc-400 disabled:bg-gray-100 disabled:text-gray-400 transition-colors"
                        placeholder="Nama Bank / E-Wallet"
                        value="{{ old('bank_name', $expense->bank_name ?? null) }}">
                </div>
            </div>
        </div>
    </div>
    
    <hr class="border-gray-200 dark:border-gray-700">

    {{-- Section: Attachment --}}
    <div>
        <h6 class="text-gray-900 dark:text-gray-100 font-bold mb-4 flex items-center">
            <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-2 text-gray-500">
                <i class="bi bi-paperclip"></i>
            </span>
            Lampiran
        </h6>
        
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-dashed border-gray-300 dark:border-gray-600">
            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300" for="attachment">
                Upload Nota / Bukti Valid <span class="text-xs font-normal text-gray-500">(Opsional)</span>
            </label>
            
            <input class="block w-full text-sm text-slate-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100
                border border-slate-200 rounded-xl cursor-pointer bg-white focus:outline-none dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400" 
                id="attachment" name="attachment" type="file" accept="image/*,application/pdf">
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <i class="bi bi-info-circle"></i> Format yang didukung: JPG, PNG, PDF (Maks. 2MB)
            </div>
            
            @error('attachment')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @isset($expense->attachment_path)
                <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center text-blue-600">
                        <i class="bi bi-file-earmark-text text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 block">File Terlampir</span>
                        <a href="{{ Storage::url($expense->attachment_path) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-700 hover:underline truncate block">
                            {{ basename($expense->attachment_path) }}
                        </a>
                    </div>
                    <a href="{{ Storage::url($expense->attachment_path) }}" target="_blank" class="text-gray-400 hover:text-gray-600 p-2">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            @endisset
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ url()->previous() ?: route('expenses.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-all">
            <i class="bi bi-arrow-left mr-2"></i> Batal
        </a>
        <button type="submit" class="w-full sm:w-auto text-center px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl focus:ring-4 focus:ring-blue-300 shadow-sm shadow-blue-500/30 transition-all">
            <i class="bi bi-check-lg mr-2"></i> Simpan Pengeluaran
        </button>
    </div>
</div>

{{-- Scripts --}}
@once
    @push('page_scripts')
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ========== AutoNumeric ==========
                const AN_OPTS = {
                    decimalPlaces: 0,
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    modifyValueOnWheel: false,
                    emptyInputBehavior: 'zero',
                    currencySymbol: '',
                    currencySymbolPlacement: 'p'
                };

                document.querySelectorAll('.js-money').forEach(el => {
                    if (!el._an) el._an = new AutoNumeric(el, AN_OPTS);
                });

                // Unmask on submit
                document.querySelectorAll('form').forEach(form => {
                    form.addEventListener('submit', function() {
                        const amountInput = this.querySelector('#amount');
                        if (amountInput && amountInput._an) {
                            amountInput.value = amountInput._an.getNumber();
                        }
                    });
                });

                // ========== Bank Toggle Logic ==========
                function toggleBankInput() {
                    const cashRadio = document.getElementById('pm_cash');
                    const transferRadio = document.getElementById('pm_transfer');
                    const bankInput = document.getElementById('bank_name');

                    if (!bankInput || !cashRadio || !transferRadio) return;

                    function updateState() {
                        const isTransfer = transferRadio.checked;
                        bankInput.disabled = !isTransfer;
                        if (!isTransfer) {
                            bankInput.value = '';
                            bankInput.classList.remove('border-red-500'); // Clean invalid state if exists
                        } else {
                            // Optional: Focus if just switched
                            if (document.activeElement === transferRadio) {
                                bankInput.focus();
                            }
                        }
                    }

                    cashRadio.addEventListener('change', updateState);
                    transferRadio.addEventListener('change', updateState);
                    
                    updateState(); // Initial run
                }

                toggleBankInput();
            });
        </script>
    @endpush
@endonce
