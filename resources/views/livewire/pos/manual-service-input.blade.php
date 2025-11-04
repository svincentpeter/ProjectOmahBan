{{-- Modal untuk input jasa manual dengan validasi deviasi harga --}}
<div class="modal fade" id="manualServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">💼 Input Jasa Manual</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form wire:submit="addService">
                    {{-- 1. PILIH JASA DARI MASTER DATA --}}
                    <div class="mb-3">
                        <label class="form-label">Jasa <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="selected_service"
                            wire:change="updatedSelectedService()">
                            <option value="">-- Pilih Jasa Dari Master --</option>
                            @foreach ($services as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selected_service')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 2. INFO HARGA STANDAR --}}
                    @if ($selected_service)
                        @php
                            $svc = \Modules\Product\Entities\ServiceMaster::find($selected_service);
                        @endphp
                        <div class="mb-3 bg-light p-3 rounded">
                            <small class="text-muted d-block mb-1">📌 Harga Standar Master Data</small>
                            <h5 class="text-primary mb-0">{{ format_currency($svc->standard_price ?? 0) }}</h5>
                        </div>
                    @endif

                    {{-- 3. INPUT HARGA JUAL --}}
                    <div class="mb-3">
                        <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" wire:model.debounce="service_price" min="0"
                            step="1" placeholder="Masukkan harga jual">
                        @error('service_price')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 4. JUMLAH JASA --}}
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" class="form-control" wire:model="service_qty" min="1"
                            step="1" value="1">
                    </div>

                    {{-- 5. CONDITIONAL: FIELD ALASAN (jika deviasi 30-50%) --}}
                    @if ($show_reason_field)
                        <div
                            class="mb-3 border-start border-4 border-warning ps-3 bg-warning bg-opacity-10 p-3 rounded">
                            <label class="form-label fw-bold text-warning">
                                ⚠️ Alasan Perubahan Harga
                            </label>
                            <small class="d-block text-muted mb-2">
                                Harga berbeda dari standar. Silakan pilih atau jelaskan alasannya.
                            </small>

                            <select class="form-select mb-2" wire:model="reason">
                                <option value="">-- Pilih Alasan --</option>
                                @foreach ($reason_presets as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                                <option value="other">Lainnya (input custom)</option>
                            </select>

                            @if ($reason === 'other')
                                <textarea class="form-control" placeholder="Jelaskan alasan perubahan harga..." rows="2" wire:model="reason"></textarea>
                            @endif

                            @error('reason')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- 6. CONDITIONAL: FIELD PIN SUPERVISOR (jika deviasi >50%) --}}
                    @if ($show_supervisor_pin)
                        <div class="mb-3 border-start border-4 border-danger ps-3 bg-danger bg-opacity-10 p-3 rounded">
                            <label class="form-label fw-bold text-danger">
                                🔐 PIN Supervisor (Deviasi >50%)
                            </label>
                            <small class="d-block text-muted mb-2">
                                Deviasi harga melebihi 50%. Approval supervisor diperlukan.
                            </small>

                            <input type="password" class="form-control" wire:model="supervisor_pin"
                                placeholder="Masukkan PIN supervisor">

                            @error('supervisor_pin')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- 7. TOMBOL SUBMIT --}}
                    <button type="submit" class="btn btn-primary w-100">
                        ✅ Tambah ke Keranjang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script untuk trigger modal dari menu --}}
@push('scripts')
    <script>
        // Bisa dipanggil dari tombol di checkout page
        document.addEventListener('openManualServiceModal', function() {
            const modal = new bootstrap.Modal(document.getElementById('manualServiceModal'));
            modal.show();
        });

        document.addEventListener('closeManualServiceModal', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('manualServiceModal'));
            if (modal) modal.hide();
        });
    </script>
@endpush
