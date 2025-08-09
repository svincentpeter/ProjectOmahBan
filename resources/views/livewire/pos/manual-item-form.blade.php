<div>
    <div class="form-group mb-3">
        <label for="manual_item_name_{{ $this->getId() }}">Nama Jasa / Item</label>
        <input
            wire:model.defer="name"
            type="text"
            class="form-control"
            id="manual_item_name_{{ $this->getId() }}"
            placeholder="cth: Jasa Balancing"
        >
        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group mb-3">
        <label for="manual_item_price_view_{{ $this->getId() }}">Harga</label>

        {{-- Input tampilan (diformat AutoNumeric). Jangan biarkan Livewire menyentuhnya --}}
        <div wire:ignore>
            <input
                type="text"
                class="form-control"
                id="manual_item_price_view_{{ $this->getId() }}"
                inputmode="numeric"
                autocomplete="off"
                placeholder="contoh: 150.000"
            >
        </div>

        {{-- Hidden input: angka mentah untuk Livewire --}}
        <input
            wire:model.defer="price"
            type="hidden"
            id="manual_item_price_{{ $this->getId() }}"
        >

        @error('price') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <button wire:click="addToCart" class="btn btn-primary">
        Tambah ke Keranjang
    </button>

    {{-- Loader & init mandiri, tanpa bergantung ke @stack --}}
    <script>
    (function () {
        const viewId   = 'manual_item_price_view_{{ $this->getId() }}';
        const hiddenId = 'manual_item_price_{{ $this->getId() }}';

        function loadAutoNumeric(callback) {
            if (window.AutoNumeric) { callback(); return; }
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/autonumeric@4.10.6/dist/autoNumeric.min.js';
            s.onload = callback;
            s.onerror = function() {
                // fallback CDN
                const f = document.createElement('script');
                f.src = 'https://unpkg.com/autonumeric@4.10.6/dist/autoNumeric.min.js';
                f.onload = callback;
                document.head.appendChild(f);
            };
            document.head.appendChild(s);
        }

        function initAutoNumeric() {
            const viewEl   = document.getElementById(viewId);
            const hiddenEl = document.getElementById(hiddenId);
            if (!viewEl || !hiddenEl) return;

            try { if (viewEl.autoNumeric) viewEl.autoNumeric.remove(); } catch (e) {}

            const an = new AutoNumeric(viewEl, {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                modifyValueOnWheel: false,
                emptyInputBehavior: 'zero',
                unformatOnSubmit: true,
            });

            // Prefill dari Livewire (kalau ada)
            if (hiddenEl.value !== '' && !isNaN(hiddenEl.value)) {
                an.set(hiddenEl.value);
            }

            const syncToHidden = () => {
                const raw = an.getNumber();           // string angka mentah
                hiddenEl.value = raw;
                hiddenEl.dispatchEvent(new Event('input', { bubbles: true })); // biar Livewire baca
            };

            viewEl.addEventListener('autoNumeric:rawValueModified', syncToHidden);
            viewEl.addEventListener('blur', syncToHidden);
        }

        function boot() {
            loadAutoNumeric(initAutoNumeric);
        }

        // Pertama kali halaman siap
        document.addEventListener('DOMContentLoaded', boot);
        // Saat Livewire selesai render komponen ini
        document.addEventListener('livewire:load', boot);
        // Re-init bila komponen re-render
        document.addEventListener('init-manual-item-autonumeric', boot);
    })();
    </script>
</div>
