<div class="card shadow-sm border-0 rounded-lg overflow-hidden">
    @php
        $type = $item_type ?? data_get($this ?? null, 'item_type', 'service');
        $nm = $name ?? data_get($this ?? null, 'name', '');
        $sell = (int) ($price ?? (data_get($this ?? null, 'price') ?? 0));
        $cost = (int) ($cost_price ?? (data_get($this ?? null, 'cost_price') ?? 0));
        $qty = (int) ($manual_qty ?? (data_get($this ?? null, 'manual_qty') ?? 1));

        $profit_unit = $type === 'item' ? $sell - $cost : $sell;
        $profit_total = $profit_unit * max(1, $qty);
    @endphp

    <div class="card-body p-3 p-md-4">

        {{-- ===================== TIPE ITEM ===================== --}}
        <div class="form-group mb-4">
            <label class="d-block font-weight-bold mb-3 text-dark" style="font-size:.95rem;">
                <i class="bi bi-tags-fill mr-1 text-primary"></i> Tipe Item
            </label>

            <fieldset>
                <legend class="sr-only">Pilih tipe input manual</legend>
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="typeService"
                            class="pos-radio-tile w-100 {{ $type === 'service' ? 'pos-radio-tile--active-primary' : '' }}">
                            <input type="radio" id="typeService" name="item_type_radio" class="d-none"
                                wire:model.live="item_type" value="service">
                            <span class="d-flex align-items-center">
                                <i
                                    class="bi bi-tools mr-2 {{ $type === 'service' ? 'text-primary' : 'text-muted' }}"></i>
                                <span>
                                    <span
                                        class="d-block font-weight-semibold {{ $type === 'service' ? 'text-primary' : 'text-dark' }}">
                                        Jasa / Service
                                    </span>
                                    <small class="text-muted">Pasang ban, spooring, dll</small>
                                </span>
                            </span>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label for="typeItem"
                            class="pos-radio-tile w-100 {{ $type === 'item' ? 'pos-radio-tile--active-warning' : '' }}">
                            <input type="radio" id="typeItem" name="item_type_radio" class="d-none"
                                wire:model.live="item_type" value="item">
                            <span class="d-flex align-items-center">
                                <i
                                    class="bi bi-box-seam mr-2 {{ $type === 'item' ? 'text-warning' : 'text-muted' }}"></i>
                                <span>
                                    <span
                                        class="d-block font-weight-semibold {{ $type === 'item' ? 'text-warning' : 'text-dark' }}">
                                        Item Bekas
                                    </span>
                                    <small class="text-muted">Ban/velg second</small>
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
            </fieldset>

            @error('item_type')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror
        </div>

        <hr class="my-4">

        {{-- ===================== NAMA ===================== --}}
        <div class="form-group mb-3">
            <label for="manualName" class="font-weight-bold mb-2 text-dark" style="font-size:.875rem;">
                <i class="bi bi-pencil-square mr-1 text-primary"></i>
                {{ $type === 'service' ? 'Nama Jasa' : 'Nama Item' }}
            </label>
            <input type="text" id="manualName" class="form-control @error('name') is-invalid @enderror"
                wire:model.defer="name" value="{{ $nm }}"
                placeholder="{{ $type === 'service' ? 'Contoh: Pasang Ban, Spooring, Balancing' : 'Contoh: Ban GT Radial Second 265/65 R17' }}"
                maxlength="255">
            @error('name')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>

        <div class="row">
            {{-- QTY --}}
            <div class="col-md-3 mb-3">
                <label class="font-weight-bold mb-2 text-dark" style="font-size:.875rem;">
                    <i class="bi bi-hash mr-1 text-primary"></i> Qty
                </label>
                <input type="number" min="1" step="1"
                    class="form-control text-center @error('manual_qty') is-invalid @enderror"
                    wire:model.live.debounce.300ms="manual_qty" value="{{ $qty }}">
                @error('manual_qty')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- HARGA JUAL / JASA --}}
            <div class="col-md-{{ $type === 'item' ? '4' : '9' }} mb-3" x-data="obMoneyBox('price')"
                x-init="init()">
                <label class="font-weight-bold mb-2 text-dark" style="font-size:.875rem;">
                    <i class="bi bi-currency-dollar mr-1 text-success"></i>
                    {{ $type === 'service' ? 'Harga Jasa' : 'Harga Jual' }}
                </label>
                <div wire:ignore>
                    <input type="text" class="form-control @error('price') is-invalid @enderror" inputmode="numeric"
                        autocomplete="off" value="{{ number_format($sell, 0, ',', '.') }}"
                        placeholder="Masukkan harga (boleh 0)">
                </div>
                <small class="text-muted d-block mt-1" style="font-size:.75rem;">
                    <i class="bi bi-info-circle"></i> Boleh diisi 0 untuk item/jasa gratis
                </small>
                @error('price')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>

            {{-- HPP (ITEM SAJA) --}}
            <div class="col-md-5 mb-3 {{ $type === 'item' ? '' : 'd-none' }}" x-data="obMoneyBox('cost_price')"
                x-init="init()">
                <label class="font-weight-bold mb-2 text-dark" style="font-size:.875rem;">
                    <i class="bi bi-cash mr-1 text-danger"></i> Harga Beli (HPP)
                </label>
                <div wire:ignore>
                    <input type="text" class="form-control @error('cost_price') is-invalid @enderror"
                        inputmode="numeric" autocomplete="off" value="{{ number_format($cost, 0, ',', '.') }}"
                        placeholder="Masukkan HPP (boleh 0)">
                </div>
                <small class="text-muted d-block mt-1" style="font-size:.75rem;">
                    <i class="bi bi-info-circle"></i> Wajib diisi untuk item bekas (boleh 0)
                </small>
                @error('cost_price')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- ALASAN INPUT MANUAL (WAJIB) --}}
        <div class="form-group mb-3">
            <label class="form-label font-weight-semibold">
                <i class="bi bi-chat-left-text mr-1"></i> Alasan Input Manual <span class="text-danger">*</span>
            </label>
            <textarea wire:model.defer="manualReason" class="form-control @error('manualReason') is-invalid @enderror"
                rows="3"
                placeholder="Contoh: Oli sponsor dari Pertamina, barang custom request customer, item tidak ada di master data, dll..."
                required></textarea>
            @error('manualReason')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">
                <i class="bi bi-exclamation-circle"></i>
                Minimal 10 karakter. <strong class="text-danger">Owner akan menerima notifikasi untuk setiap input
                    manual.</strong>
            </small>
        </div>

        {{-- PREVIEW PROFIT --}}
        <div class="profit-box alert {{ $profit_unit < 0 ? 'alert-warning' : 'alert-light' }} border mb-3"
            role="alert" aria-live="polite">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-semibold text-muted">
                    <i class="bi bi-graph-up mr-1"></i> Profit per unit
                </span>
                <span class="font-weight-bold {{ $profit_unit < 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($profit_unit, 0, ',', '.') }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <small class="text-muted">Total (x{{ max(1, $qty) }})</small>
                <span class="font-weight-bold {{ $profit_unit < 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($profit_total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- TOMBOL TAMBAH --}}
        <button type="button" wire:click="add" wire:loading.attr="disabled"
            class="btn btn-primary btn-lg btn-block btn-add-manual">
            <span wire:loading wire:target="add" class="spinner-border spinner-border-sm mr-2" role="status"></span>
            <i class="bi bi-plus-circle mr-2" wire:loading.remove wire:target="add"></i>
            {{ $type === 'service' ? 'Tambah Jasa' : 'Tambah Item' }}
        </button>
    </div>
</div>

@push('page_css')
    <style>
        /* === Radio tile seragam (dipakai juga di halaman lain) === */
        .pos-radio-tile {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: .8rem 1rem;
            cursor: pointer;
            transition: .2s;
            background: #fff;
            display: inline-flex;
            align-items: center;
            gap: .5rem
        }

        .pos-radio-tile:hover {
            border-color: #4834DF;
            background: #f8f7ff;
            box-shadow: 0 4px 12px rgba(72, 52, 223, .08)
        }

        .pos-radio-tile--active-primary {
            border-color: #4834DF;
            background: #f0efff
        }

        .pos-radio-tile--active-warning {
            border-color: #f9b115;
            background: #fff7e6
        }

        .form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 3px rgba(72, 52, 223, .12)
        }

        .btn-add-manual {
            font-weight: 600
        }

        .btn-add-manual:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(72, 52, 223, .25)
        }

        .btn-add-manual:disabled {
            opacity: .6;
            cursor: not-allowed
        }

        .profit-box {
            border-radius: .5rem
        }

        .font-weight-semibold {
            font-weight: 600
        }
    </style>
@endpush

@once
    @push('page_scripts')
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
        <script>
            // Util uang: binding AutoNumeric <-> Livewire (reusable)
            window.obMoneyBox = function(prop) {
                return {
                    prop,
                    an: null,
                    init() {
                        const input = this.$root.querySelector('input[type="text"]');
                        if (!input) return;

                        // Jika sudah pernah di-init, ambil instance
                        if (input.dataset.anInit === '1' && window.AutoNumeric) {
                            try {
                                this.an = AutoNumeric.getAutoNumericElement(input);
                            } catch (e) {}
                            return;
                        }

                        const pushToWire = () => {
                            let v = 0;
                            if (this.an) {
                                const raw = this.an.getNumber();
                                v = (raw !== null && raw !== undefined && !isNaN(raw)) ? parseInt(raw, 10) : 0;
                            } else {
                                v = parseInt((input.value || '').replace(/[^\d-]/g, ''), 10) || 0;
                            }
                            if (this.$wire && typeof this.$wire.set === 'function') {
                                this.$wire.set(this.prop, v);
                            }
                        };

                        if (window.AutoNumeric) {
                            this.an = new AutoNumeric(input, {
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                decimalPlaces: 0,
                                unformatOnSubmit: true,
                                modifyValueOnWheel: false,
                                minimumValue: '0',
                                maximumValue: '9999999999999',
                                // ✅ FIX: opsi valid (bukan 'never')
                                allowDecimalPadding: false,
                                // Izinkan input sementara di luar range (supaya bisa clear field)
                                overrideMinMaxLimits: 'invalid'
                            });
                            input.dataset.anInit = '1';

                            input.addEventListener('autoNumeric:rawValueModified', pushToWire);
                            input.addEventListener('input', pushToWire);
                            input.addEventListener('change', pushToWire);
                            input.addEventListener('blur', pushToWire);

                            // Set nilai awal dari Livewire (aman ketika 0)
                            let initialValue = 0;
                            try {
                                if (this.$wire && typeof this.$wire.get === 'function') {
                                    initialValue = this.$wire.get(this.prop) || 0;
                                }
                            } catch (e) {}
                            if (this.an) this.an.set(initialValue);
                        } else {
                            input.addEventListener('input', pushToWire);
                            input.addEventListener('change', pushToWire);
                            const n = parseInt((input.value || '').replace(/[^\d-]/g, ''), 10) || 0;
                            input.value = n ? n.toLocaleString('id-ID') : '0';
                        }
                    }
                }
            };
        </script>
    @endpush
@endonce
