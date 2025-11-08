{{-- Modal: Input Jasa Manual (seragam gaya "Jasa") --}}
<div class="modal fade" id="manualServiceModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-sm">

            {{-- Header --}}
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title mb-0">
                    <i class="bi bi-briefcase-fill text-primary mr-2"></i> Input Jasa Manual
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                <form wire:submit.prevent="addService">

                    {{-- 1) Pilih jasa dari master --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Jasa <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="selected_service" wire:change="updatedSelectedService">
                            <option value="">-- Pilih Jasa Dari Master --</option>
                            @foreach ($services as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selected_service')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- 2) Info harga standar (hindari query di view) --}}
                    @if ($selected_service)
                        <div class="pos-callout pos-callout--info mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle mr-2"></i>
                                <div>
                                    <small class="text-muted d-block">Harga Standar (Master Data)</small>
                                    <strong class="text-primary h5 mb-0 d-block">
                                        {{ format_currency($selected_service_price ?? 0) }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        {{-- 3) Harga jual --}}
                        <div class="col-md-6 mb-3" x-data="posMoneyBox('service_price')" x-init="init()">
                            <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <div wire:ignore>
                                <input type="text" class="form-control @error('service_price') is-invalid @enderror"
                                    inputmode="numeric" autocomplete="off" placeholder="0">
                            </div>
                            @error('service_price')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">Gunakan angka tanpa desimal.</small>
                        </div>

                        {{-- 4) Jumlah --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" class="form-control" wire:model="service_qty" min="1"
                                step="1" value="1">
                        </div>
                    </div>

                    {{-- 5) Alasan (deviasi 30–50%) --}}
                    @if ($show_reason_field)
                        <div class="pos-callout pos-callout--warning mb-3">
                            <label class="form-label fw-bold text-warning mb-1">⚠️ Alasan Perubahan Harga</label>
                            <small class="text-muted d-block mb-2">Harga berbeda dari standar. Pilih atau jelaskan
                                alasannya.</small>

                            <select class="form-select mb-2" wire:model="reason_key">
                                <option value="">-- Pilih Alasan --</option>
                                @foreach ($reason_presets as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                                <option value="other">Lainnya (input manual)</option>
                            </select>

                            @if ($reason_key === 'other')
                                <textarea class="form-control" rows="2" placeholder="Jelaskan alasan perubahan harga..." wire:model="reason_note"></textarea>
                            @endif

                            @error('reason_key')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                            @error('reason_note')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    @endif

                    {{-- 6) PIN Supervisor (deviasi > 50%) --}}
                    @if ($show_supervisor_pin)
                        <div class="pos-callout pos-callout--danger mb-3">
                            <label class="form-label fw-bold text-danger mb-1">🔐 PIN Supervisor (Deviasi &gt;
                                50%)</label>
                            <small class="text-muted d-block mb-2">Approval supervisor diperlukan.</small>
                            <input type="password" class="form-control" wire:model="supervisor_pin"
                                placeholder="Masukkan PIN supervisor">
                            @error('supervisor_pin')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    @endif

                    {{-- 7) Submit --}}
                    <button type="submit" class="btn btn-primary w-100">
                        ✅ Tambah ke Keranjang
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- Trigger dari luar (checkout page) --}}
@push('page_scripts')
    <script>
        document.addEventListener('openManualServiceModal', function() {
            const el = document.getElementById('manualServiceModal');
            (new bootstrap.Modal(el)).show();
        });
        document.addEventListener('closeManualServiceModal', function() {
            const el = document.getElementById('manualServiceModal');
            const m = bootstrap.Modal.getInstance(el);
            if (m) m.hide();
        });
    </script>
@endpush

@push('page_styles')
    <style>
        /* Callout seragam */
        .pos-callout {
            border-radius: 10px;
            padding: 12px 14px;
            border-left: 4px solid;
            background: #f8f9fa
        }

        .pos-callout--info {
            border-color: #39f;
            background: #f1f7ff
        }

        .pos-callout--warning {
            border-color: #f9b115;
            background: #fff7e6
        }

        .pos-callout--danger {
            border-color: #e55353;
            background: #ffecec
        }
    </style>
@endpush

@once
    @push('page_scripts')
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
        <script>
            // Binder uang IDR <-> Livewire (dipakai juga di komponen lain)
            window.posMoneyBox = function(prop) {
                return {
                    prop,
                    an: null,
                    init() {
                        const input = this.$root.querySelector('input[type="text"]');
                        if (!input) return;

                        const pushToWire = () => {
                            let v = 0;
                            if (this.an) {
                                const raw = this.an.getNumber();
                                v = (raw !== null && raw !== undefined) ? parseInt(raw, 10) : 0;
                            } else {
                                v = parseInt((input.value || '').replace(/[^\d-]/g, ''), 10) || 0;
                            }
                            if (this.$wire) {
                                this.$wire.set(this.prop, v);
                            } else {
                                this.$wire[this.prop] = v;
                            }
                        };

                        if (window.AutoNumeric) {
                            this.an = new AutoNumeric(input, {
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                decimalPlaces: 0,
                                unformatOnSubmit: true,
                                minimumValue: '0',
                                maximumValue: '999999999',
                                modifyValueOnWheel: false,
                                allowDecimalPadding: false,
                            });
                            input.addEventListener('autoNumeric:rawValueModified', pushToWire);
                            input.addEventListener('change', pushToWire);
                            input.addEventListener('blur', pushToWire);
                        } else {
                            input.addEventListener('input', pushToWire);
                            input.addEventListener('change', pushToWire);
                        }
                    }
                }
            };
        </script>
    @endpush
@endonce
