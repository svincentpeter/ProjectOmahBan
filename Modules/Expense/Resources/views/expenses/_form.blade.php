@csrf

<div class="card-body">
    {{-- Row 1: Date, Category, Amount --}}
    <div class="form-row">
        {{-- Tanggal --}}
        <div class="col-lg-4">
            <div class="form-group">
                <label class="font-weight-bold mb-1">
                    <i class="cil-calendar mr-1 text-primary"></i>
                    Tanggal <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light">
                            <i class="cil-calendar"></i>
                        </span>
                    </div>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                        value="{{ old('date', optional($expense->date ?? null)->toDateString() ?? now()->toDateString()) }}"
                        required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Kategori --}}
        <div class="col-lg-4">
            <div class="form-group">
                <label class="font-weight-bold mb-1">
                    <i class="cil-tag mr-1 text-success"></i>
                    Kategori <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light">
                            <i class="cil-tag"></i>
                        </span>
                    </div>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="" disabled
                            {{ old('category_id', $expense->category_id ?? null) ? '' : 'selected' }}>
                            Pilih Kategori
                        </option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $expense->category_id ?? null) == $cat->id)>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if (($categories ?? collect())->isEmpty())
                    <small class="form-text text-muted">
                        <i class="cil-warning mr-1"></i>
                        Belum ada kategori.
                        @if (Route::has('expense-categories.create'))
                            <a href="{{ route('expense-categories.create') }}" target="_blank">Tambah di sini</a>
                        @endif
                    </small>
                @endif
            </div>
        </div>

        {{-- Nominal --}}
        <div class="col-lg-4">
            <div class="form-group">
                <label class="font-weight-bold mb-1">
                    <i class="cil-dollar mr-1 text-warning"></i>
                    Nominal (Rp) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light">Rp</span>
                    </div>
                    <input type="text" id="amount" name="amount"
                        class="form-control js-money @error('amount') is-invalid @enderror"
                        value="{{ number_format((int) old('amount', $expense->amount ?? 0), 0, ',', '.') }}"
                        placeholder="0" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <small class="form-text text-muted">
                    <i class="cil-info mr-1"></i>
                    Format: 1.000.000 (otomatis)
                </small>
            </div>
        </div>
    </div>

    {{-- Row 2: Description --}}
    <div class="form-row">
        <div class="col-12">
            <div class="form-group">
                <label class="font-weight-bold mb-1">
                    <i class="cil-notes mr-1 text-info"></i>
                    Deskripsi <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light">
                            <i class="cil-notes"></i>
                        </span>
                    </div>
                    <input type="text" name="details" class="form-control @error('details') is-invalid @enderror"
                        placeholder="Contoh: Beli Bensin Motor Operasional"
                        value="{{ old('details', $expense->details ?? null) }}" required>
                    @error('details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Payment Method & Bank --}}
    <div class="form-row">
        {{-- Payment Method --}}
        <div class="col-lg-6">
            <div class="form-group">
                <label class="font-weight-bold mb-2 d-block">
                    <i class="cil-credit-card mr-1"></i>
                    Metode Pembayaran <span class="text-danger">*</span>
                </label>
                @php $pm = old('payment_method', $expense->payment_method ?? 'Tunai'); @endphp

                {{-- Radio Button Group dengan Card Style --}}
                <div class="payment-method-group d-flex">
                    {{-- Option: Tunai --}}
                    <label class="payment-option {{ $pm === 'Tunai' ? 'active' : '' }}" for="pm_cash">
                        <input class="payment-radio" type="radio" name="payment_method" id="pm_cash" value="Tunai"
                            @checked($pm === 'Tunai')>
                        <div class="payment-content">
                            <i class="cil-wallet payment-icon"></i>
                            <span class="payment-label">Tunai</span>
                        </div>
                    </label>

                    {{-- Option: Transfer --}}
                    <label class="payment-option {{ $pm === 'Transfer' ? 'active' : '' }}" for="pm_transfer">
                        <input class="payment-radio" type="radio" name="payment_method" id="pm_transfer"
                            value="Transfer" @checked($pm === 'Transfer')>
                        <div class="payment-content">
                            <i class="cil-bank payment-icon"></i>
                            <span class="payment-label">Transfer</span>
                        </div>
                    </label>
                </div>

                @error('payment_method')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Bank Name --}}
        <div class="col-lg-6">
            <div class="form-group">
                <label class="font-weight-bold mb-2">
                    <i class="cil-bank mr-1"></i>
                    Bank
                    <span class="badge badge-secondary badge-sm">Jika Transfer</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light">
                            <i class="cil-institution"></i>
                        </span>
                    </div>
                    <input type="text" name="bank_name"
                        class="form-control @error('bank_name') is-invalid @enderror"
                        placeholder="Mandiri / BCA / BRI / BNI"
                        value="{{ old('bank_name', $expense->bank_name ?? null) }}">
                    @error('bank_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <small class="form-text text-muted">
                    <i class="cil-info mr-1"></i>
                    Otomatis disabled jika pilih Tunai
                </small>
            </div>
        </div>
    </div>


    {{-- Row 4: Attachment --}}
    <div class="form-row">
        <div class="col-lg-6">
            <div class="form-group">
                <label class="font-weight-bold mb-1">
                    <i class="cil-paperclip mr-1 text-dark"></i>
                    Lampiran Nota
                    <span class="badge badge-secondary">Opsional</span>
                </label>
                <div class="custom-file">
                    <input type="file" name="attachment"
                        class="custom-file-input @error('attachment') is-invalid @enderror" id="attachment"
                        accept="image/*,application/pdf">
                    <label class="custom-file-label" for="attachment">Pilih file...</label>
                    @error('attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @isset($expense->attachment_path)
                    <div class="mt-2 p-2 bg-light rounded">
                        <small class="text-muted">
                            <i class="cil-file mr-1"></i>
                            File saat ini: <strong>{{ basename($expense->attachment_path) }}</strong>
                        </small>
                    </div>
                @endisset

                <small class="form-text text-muted">
                    <i class="cil-info mr-1"></i>
                    Format: JPG, PNG, atau PDF (max 2MB)
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Card Footer: Actions --}}
<div class="card-footer bg-light d-flex justify-content-between">
    <a href="{{ url()->previous() ?: route('expenses.index') }}" class="btn btn-secondary">
        <i class="cil-arrow-left mr-1"></i> Batal
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="cil-save mr-1"></i> Simpan Pengeluaran
    </button>
</div>

{{-- Styles --}}
@push('page_styles')
    <style>
        /* ========== Input Group Styling ========== */
        .input-group-text {
            background-color: #f0f3f5;
            border-right: 0;
            min-width: 45px;
            justify-content: center;
        }

        .input-group .form-control {
            border-left: 0;
        }

        .input-group .form-control:focus {
            border-color: #8ad4ee;
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #8ad4ee;
            background-color: #e7f6fc;
        }

        /* ========== Payment Method Radio Buttons ========== */
        .payment-method-group {
            gap: 12px;
        }

        .payment-option {
            flex: 1;
            position: relative;
            cursor: pointer;
            margin: 0;
            transition: all 0.3s ease;
        }

        .payment-radio {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .payment-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px 12px;
            border: 2px solid #d8dbe0;
            border-radius: 8px;
            background-color: #fff;
            transition: all 0.3s ease;
            min-height: 80px;
        }

        .payment-icon {
            font-size: 24px;
            margin-bottom: 8px;
            color: #768192;
            transition: all 0.3s ease;
        }

        .payment-label {
            font-size: 14px;
            font-weight: 500;
            color: #4f5d73;
            transition: all 0.3s ease;
        }

        /* Hover State */
        .payment-option:hover .payment-content {
            border-color: #8ad4ee;
            background-color: #f8fcff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .payment-option:hover .payment-icon {
            color: #2eb85c;
            transform: scale(1.1);
        }

        /* Active/Checked State */
        .payment-option.active .payment-content,
        .payment-radio:checked~.payment-content {
            border-color: #321fdb;
            background-color: #e7e9fd;
            box-shadow: 0 4px 12px rgba(50, 31, 219, 0.15);
        }

        .payment-option.active .payment-icon,
        .payment-radio:checked~.payment-content .payment-icon {
            color: #321fdb;
            transform: scale(1.15);
        }

        .payment-option.active .payment-label,
        .payment-radio:checked~.payment-content .payment-label {
            color: #321fdb;
            font-weight: 600;
        }

        /* Focus State (Accessibility) */
        .payment-radio:focus~.payment-content {
            outline: 2px solid #321fdb;
            outline-offset: 2px;
        }

        /* ========== Custom File Input ========== */
        .custom-file-label::after {
            content: "Browse";
        }

        /* ========== Badge Adjustment ========== */
        .badge-sm {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            vertical-align: middle;
        }

        /* ========== Responsive ========== */
        @media (max-width: 768px) {
            .payment-method-group {
                flex-direction: column;
            }

            .payment-content {
                flex-direction: row;
                justify-content: flex-start;
                padding: 12px 16px;
                min-height: auto;
            }

            .payment-icon {
                margin-bottom: 0;
                margin-right: 12px;
                font-size: 20px;
            }
        }
    </style>
@endpush


{{-- Scripts: AutoNumeric & Form Logic --}}
@once
    @push('page_scripts')
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4"></script>
        <script>
            (function() {
                'use strict';

                // ========== AutoNumeric Configuration ==========
                const AN_OPTS = {
                    decimalPlaces: 0,
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    modifyValueOnWheel: false,
                    emptyInputBehavior: 'zero',
                    currencySymbol: '',
                    currencySymbolPlacement: 'p'
                };

                function stripMoney(value) {
                    return parseInt(String(value || '').replace(/[^\d\-]/g, '')) || 0;
                }

                function initAutoNumeric(scope) {
                    (scope || document).querySelectorAll('input.js-money').forEach(function(el) {
                        if (el._an) return; // Already initialized
                        el._an = new AutoNumeric(el, AN_OPTS);
                    });
                }

                function bindUnmaskOnSubmit(scope) {
                    (scope || document).querySelectorAll('form').forEach(function(form) {
                        if (form._anBound || !form.querySelector('input.js-money')) return;
                        form._anBound = true;

                        form.addEventListener('submit', function() {
                            form.querySelectorAll('input.js-money').forEach(function(input) {
                                input.value = input._an ? input._an.getNumber() : stripMoney(input
                                    .value);
                            });
                        });
                    });
                }

                // ========== Bank Input Toggle Logic ==========
                function toggleBankInput(form) {
                    const bankInput = form.querySelector('input[name="bank_name"]');
                    const cashRadio = form.querySelector('#pm_cash');
                    const transferRadio = form.querySelector('#pm_transfer');

                    // Payment option labels (untuk styling)
                    const cashOption = form.querySelector('label[for="pm_cash"]');
                    const transferOption = form.querySelector('label[for="pm_transfer"]');

                    if (!bankInput) return;

                    function updateBankState() {
                        const isTransfer = transferRadio && transferRadio.checked;

                        // Disable/Enable bank input
                        bankInput.disabled = !isTransfer;

                        if (!isTransfer) {
                            bankInput.value = '';
                            bankInput.classList.remove('is-invalid');
                        }

                        // Update active class for payment options
                        if (cashOption) {
                            cashOption.classList.toggle('active', !isTransfer);
                        }
                        if (transferOption) {
                            transferOption.classList.toggle('active', isTransfer);
                        }

                        // Visual feedback for bank input
                        const inputGroup = bankInput.closest('.input-group');
                        if (inputGroup) {
                            const prepend = inputGroup.querySelector('.input-group-prepend');
                            if (prepend) {
                                prepend.style.opacity = isTransfer ? '1' : '0.5';
                            }
                        }
                    }

                    [cashRadio, transferRadio].forEach(function(radio) {
                        if (radio) radio.addEventListener('change', updateBankState);
                    });

                    updateBankState(); // Initial state
                }

                // ========== Custom File Input Label ==========
                function initFileInputLabel() {
                    document.querySelectorAll('.custom-file-input').forEach(function(input) {
                        input.addEventListener('change', function(e) {
                            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
                            const label = input.nextElementSibling;
                            if (label) label.textContent = fileName;
                        });
                    });
                }

                // ========== Initialize All ==========
                document.addEventListener('DOMContentLoaded', function() {
                    initAutoNumeric();
                    bindUnmaskOnSubmit();
                    document.querySelectorAll('form').forEach(toggleBankInput);
                    initFileInputLabel();
                });

            })
            ();
        </script>
    @endpush
@endonce
