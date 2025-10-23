<div class="card border-0 shadow-sm rounded-lg overflow-hidden">
    @php
        $type = $item_type ?? data_get($this ?? null, 'item_type', 'service');
        $nm = $name ?? data_get($this ?? null, 'name', '');
        $sell = (int) ($price ?? (data_get($this ?? null, 'price') ?? 0));
        $cost = (int) ($cost_price ?? (data_get($this ?? null, 'cost_price') ?? 0));
        $qty = (int) ($manual_qty ?? (data_get($this ?? null, 'manual_qty') ?? 1));

        $profit_unit = $type === 'item' ? $sell - $cost : $sell;
        $profit_total = $profit_unit * max(1, $qty);
    @endphp

    <div class="card-body p-4">
        {{-- Tipe Item --}}
        <div class="form-group mb-4">
            <label class="d-block font-weight-bold mb-3" style="font-size: 0.95rem; color: #2d3748;">
                <i class="bi bi-tags-fill mr-1" style="color: #5a67d8;"></i> Tipe Item
            </label>

            <fieldset>
                <legend class="sr-only">Pilih tipe input manual</legend>

                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="typeService" name="item_type_radio" class="custom-control-input"
                                wire:model.live="item_type" value="service">
                            <label class="custom-control-label manual-type-label" for="typeService"
                                style="cursor: pointer; display: block; padding: 1rem; border: 2px solid {{ $type === 'service' ? '#5a67d8' : '#e2e8f0' }}; 
                                          border-radius: 0.5rem; transition: all 0.2s ease; background: {{ $type === 'service' ? '#eef2ff' : '#fff' }};">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-tools mr-2"
                                        style="font-size: 1.25rem; color: {{ $type === 'service' ? '#5a67d8' : '#9ca3af' }};"></i>
                                    <div>
                                        <span class="d-block font-weight-600"
                                            style="font-size: 0.9rem; color: {{ $type === 'service' ? '#5a67d8' : '#4b5563' }};">
                                            Jasa / Service
                                        </span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Pasang ban, spooring,
                                            dll</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="typeItem" name="item_type_radio" class="custom-control-input"
                                wire:model.live="item_type" value="item">
                            <label class="custom-control-label manual-type-label" for="typeItem"
                                style="cursor: pointer; display: block; padding: 1rem; border: 2px solid {{ $type === 'item' ? '#f59e0b' : '#e2e8f0' }}; 
                                          border-radius: 0.5rem; transition: all 0.2s ease; background: {{ $type === 'item' ? '#fffbeb' : '#fff' }};">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-box-seam mr-2"
                                        style="font-size: 1.25rem; color: {{ $type === 'item' ? '#f59e0b' : '#9ca3af' }};"></i>
                                    <div>
                                        <span class="d-block font-weight-600"
                                            style="font-size: 0.9rem; color: {{ $type === 'item' ? '#f59e0b' : '#4b5563' }};">
                                            Item Bekas
                                        </span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Ban/velg second</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>

            @error('item_type')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror
        </div>

        <hr class="my-4" style="border-color: #e2e8f0;">

        {{-- Nama --}}
        <div class="form-group mb-3">
            <label for="manualName" class="font-weight-bold mb-2" style="font-size: 0.875rem; color: #2d3748;">
                <i class="bi bi-pencil-square mr-1" style="color: #5a67d8;"></i>
                {{ $type === 'service' ? 'Nama Jasa' : 'Nama Item' }}
            </label>
            <input type="text" id="manualName" class="form-control @error('name') is-invalid @enderror"
                wire:model.defer="name" value="{{ $nm }}"
                placeholder="{{ $type === 'service' ? 'Contoh: Pasang Ban, Spooring, Balancing' : 'Contoh: Ban GT Radial Second 265/65 R17' }}"
                maxlength="255" style="border-radius: 0.5rem; border: 1px solid #e2e8f0; padding: 0.625rem 0.875rem;">
            @error('name')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>

        <div class="row">
            {{-- Qty --}}
            <div class="col-md-3 mb-3">
                <label class="font-weight-bold mb-2" style="font-size: 0.875rem; color: #2d3748;">
                    <i class="bi bi-hash mr-1" style="color: #5a67d8;"></i> Qty
                </label>
                <input type="number" min="1" step="1"
                    class="form-control text-center @error('manual_qty') is-invalid @enderror"
                    wire:model.live.debounce.300ms="manual_qty" value="{{ $qty }}"
                    style="border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                @error('manual_qty')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Harga Jual / Harga Jasa --}}
            <div class="col-md-{{ $type === 'item' ? '4' : '9' }} mb-3" x-data="obMoneyBox('price')"
                x-init="init()">
                <label class="font-weight-bold mb-2" style="font-size: 0.875rem; color: #2d3748;">
                    <i class="bi bi-currency-dollar mr-1" style="color: #10b981;"></i>
                    {{ $type === 'service' ? 'Harga Jasa' : 'Harga Jual' }}
                </label>
                <div wire:ignore>
                    <input type="text" class="form-control @error('price') is-invalid @enderror" inputmode="numeric"
                        autocomplete="off" value="{{ number_format($sell, 0, ',', '.') }}"
                        placeholder="Masukkan harga (boleh 0 untuk gratis)"
                        style="border-radius: 0.5rem; border: 1px solid #e2e8f0; padding: 0.625rem 0.875rem;">
                </div>
                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                    <i class="bi bi-info-circle"></i> Boleh diisi 0 untuk item/jasa gratis
                </small>
                @error('price')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>


            {{-- HPP (Item saja) --}}
            <div class="col-md-5 mb-3 {{ $type === 'item' ? '' : 'd-none' }}" x-data="obMoneyBox('cost_price')"
                x-init="init()">
                <label class="font-weight-bold mb-2" style="font-size: 0.875rem; color: #2d3748;">
                    <i class="bi bi-cash mr-1" style="color: #ef4444;"></i> Harga Beli (HPP)
                </label>
                <div wire:ignore>
                    <input type="text" class="form-control @error('cost_price') is-invalid @enderror"
                        inputmode="numeric" autocomplete="off" value="{{ number_format($cost, 0, ',', '.') }}"
                        placeholder="Masukkan HPP (boleh 0)"
                        style="border-radius: 0.5rem; border: 1px solid #e2e8f0; padding: 0.625rem 0.875rem;">
                </div>
                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                    <i class="bi bi-info-circle"></i> Wajib diisi untuk item bekas (boleh 0)
                </small>
                @error('cost_price')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>

        </div>

        {{-- Preview Profit --}}
        <div class="alert {{ $profit_unit < 0 ? 'alert-warning' : 'alert-light' }} border mt-3 mb-3"
            style="border-radius: 0.5rem; background: {{ $profit_unit < 0 ? '#fef3c7' : '#f9fafb' }};" role="alert"
            aria-live="polite">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-weight-600" style="font-size: 0.875rem; color: #4b5563;">
                    <i class="bi bi-graph-up mr-1"></i> Profit per unit
                </span>
                <span class="font-weight-bold"
                    style="font-size: 0.95rem; color: {{ $profit_unit < 0 ? '#dc2626' : '#059669' }};">
                    {{ number_format($profit_unit, 0, ',', '.') }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2"
                style="border-top: 1px solid {{ $profit_unit < 0 ? '#fbbf24' : '#e5e7eb' }};">
                <small style="color: #6b7280;">Total (x{{ max(1, $qty) }})</small>
                <span class="font-weight-bold" style="color: {{ $profit_unit < 0 ? '#dc2626' : '#059669' }};">
                    {{ number_format($profit_total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Tambah Button --}}
        <button type="button" wire:click="add" wire:loading.attr="disabled"
            class="btn btn-lg btn-block btn-add-manual"
            style="background: linear-gradient(135deg, #5a67d8 0%, #805ad5 100%); color: #fff; border: none; 
                       border-radius: 0.5rem; font-weight: 600; padding: 0.875rem; transition: all 0.2s ease; 
                       box-shadow: 0 4px 12px rgba(90, 103, 216, 0.3);">
            <span wire:loading wire:target="add" class="spinner-border spinner-border-sm mr-2" role="status"></span>
            <i class="bi bi-plus-circle mr-2" wire:loading.remove wire:target="add"></i>
            {{ $type === 'service' ? 'Tambah Jasa' : 'Tambah Item' }}
        </button>
    </div>
</div>

@push('page_css')
    <style>
        /* === Manual Type Label Hover === */
        .manual-type-label:hover {
            border-color: #cbd5e0 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* === Add Button Hover === */
        .btn-add-manual:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(90, 103, 216, 0.4);
        }

        .btn-add-manual:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* === Form Control Focus === */
        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 3px rgba(90, 103, 216, 0.1);
        }

        /* === Custom Radio Hidden === */
        .custom-control-input {
            position: absolute;
            opacity: 0;
        }
    </style>
@endpush

@once
    @push('page_scripts')
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>

        <script>
            window.obMoneyBox = function(prop) {
                return {
                    prop,
                    an: null,

                    init() {
                        const input = this.$root.querySelector('input[type="text"]');
                        if (!input) return;

                        // Cek apakah sudah di-init sebelumnya
                        if (input.dataset.anInit === '1' && window.AutoNumeric) {
                            try {
                                this.an = AutoNumeric.getAutoNumericElement(input);
                            } catch (e) {
                                console.warn('AutoNumeric belum init:', e);
                            }
                            return;
                        }

                        const pushToWire = () => {
                            let v = 0;
                            if (this.an) {
                                const raw = this.an.getNumber();
                                // ✅ FIXED: Izinkan 0, hanya parse jika ada nilai
                                v = (raw !== null && raw !== undefined) ? parseInt(raw, 10) : 0;
                            } else {
                                v = parseInt((input.value || '').replace(/[^\d-]/g, ''), 10) || 0;
                            }

                            // Gunakan API Livewire yang benar
                            if (this.$wire) {
                                // Livewire 3
                                this.$wire.set(this.prop, v);
                            } else {
                                // Livewire 2 (fallback)
                                this.$wire[this.prop] = v;
                            }
                        };

                        if (window.AutoNumeric) {
                            this.an = new AutoNumeric(input, {
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                decimalPlaces: 0,
                                unformatOnSubmit: true,
                                modifyValueOnWheel: false,
                                minimumValue: '0', // ✅ FIXED: Set minimum 0 (bukan 1)
                                maximumValue: '999999999',
                                allowDecimalPadding: false,
                            });
                            input.dataset.anInit = '1';

                            input.addEventListener('autoNumeric:rawValueModified', pushToWire);
                            input.addEventListener('change', pushToWire);
                            input.addEventListener('blur', pushToWire);
                        } else {
                            // Fallback jika AutoNumeric gagal load
                            input.addEventListener('input', pushToWire);
                            input.addEventListener('change', pushToWire);

                            const n = parseInt((input.value || '').replace(/[^\d-]/g, ''), 10) || 0;
                            input.value = n ? n.toLocaleString('id-ID') : '0';
                        }
                    }
                };
            };
        </script>
    @endpush
@endonce
