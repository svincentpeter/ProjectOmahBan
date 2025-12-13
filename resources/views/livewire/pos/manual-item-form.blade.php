<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    @php
        $type = $item_type ?? data_get($this ?? null, 'item_type', 'service');
        $nm = $name ?? data_get($this ?? null, 'name', '');
        $sell = (int) ($price ?? (data_get($this ?? null, 'price') ?? 0));
        $cost = (int) ($cost_price ?? (data_get($this ?? null, 'cost_price') ?? 0));
        $qty = (int) ($manual_qty ?? (data_get($this ?? null, 'manual_qty') ?? 1));

        $profit_unit = $type === 'item' ? $sell - $cost : $sell;
        $profit_total = $profit_unit * max(1, $qty);
    @endphp

    <div class="p-6">
        {{-- ===================== TIPE ITEM ===================== --}}
        <div class="mb-6">
            <label class="block mb-3 text-sm font-bold text-slate-800">
                <i class="bi bi-tags-fill mr-1 text-ob-primary"></i> Tipe Item
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Service Option --}}
                <label class="relative cursor-pointer group">
                    <input type="radio" name="item_type_radio" wire:model.live="item_type" value="service" class="peer sr-only">
                    <div class="p-4 rounded-xl border-2 border-slate-200 bg-white transition-all peer-checked:border-ob-primary peer-checked:bg-indigo-50 peer-hover:border-ob-primary/50 hover:bg-slate-50 h-full flex items-center gap-3 shadow-sm group-hover:shadow-md">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center peer-checked:bg-ob-primary peer-checked:text-white transition-colors">
                            <i class="bi bi-tools text-lg text-slate-400 peer-checked:text-white"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-slate-700 peer-checked:text-ob-primary">Jasa / Service</span>
                            <span class="text-xs text-slate-500 peer-checked:text-indigo-600/80">Pasang ban, spooring, dll</span>
                        </div>
                    </div>
                </label>

                {{-- Item Option --}}
                <label class="relative cursor-pointer group">
                    <input type="radio" name="item_type_radio" wire:model.live="item_type" value="item" class="peer sr-only">
                    <div class="p-4 rounded-xl border-2 border-slate-200 bg-white transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-hover:border-orange-400 hover:bg-slate-50 h-full flex items-center gap-3 shadow-sm group-hover:shadow-md">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center peer-checked:bg-orange-500 peer-checked:text-white transition-colors">
                            <i class="bi bi-box-seam text-lg text-slate-400 peer-checked:text-white"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-slate-700 peer-checked:text-orange-700">Item Bekas / Lain</span>
                            <span class="text-xs text-slate-500 peer-checked:text-orange-600/80">Ban/velg second non-master</span>
                        </div>
                    </div>
                </label>
            </div>
            @error('item_type') <p class="mt-2 text-xs text-red-600 font-bold flex items-center"><i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}</p> @enderror
        </div>

        <hr class="my-6 border-slate-100">

        {{-- ===================== NAMA ===================== --}}
        <div class="mb-5">
            <label for="manualName" class="block mb-2 text-sm font-bold text-slate-800">
                <i class="bi bi-pencil-square mr-1 text-ob-primary"></i>
                {{ $type === 'service' ? 'Nama Jasa' : 'Nama Item' }}
            </label>
            <input type="text" id="manualName" 
                class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full p-3 shadow-sm transition-all placeholder:text-slate-400 font-medium" 
                wire:model.defer="name" value="{{ $nm }}"
                placeholder="{{ $type === 'service' ? 'Contoh: Pasang Ban, Spooring, Balancing' : 'Contoh: Ban GT Radial Second 265/65 R17' }}"
                maxlength="255">
            @error('name') <p class="mt-1 text-xs text-red-600 font-bold flex items-center"><i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-5">
            {{-- QTY --}}
            <div class="md:col-span-3">
                <label class="block mb-2 text-sm font-bold text-slate-800">
                    <i class="bi bi-hash mr-1 text-ob-primary"></i> Qty
                </label>
                <div class="relative">
                    <input type="number" min="1" step="1"
                        class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full p-3 text-center font-bold shadow-sm"
                        wire:model.live.debounce.300ms="manual_qty" value="{{ $qty }}">
                </div>
                @error('manual_qty') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
            </div>

            {{-- HARGA JUAL / JASA --}}
            <div class="{{ $type === 'item' ? 'md:col-span-4' : 'md:col-span-9' }}" x-data="moneyBox('price')">
                <label class="block mb-2 text-sm font-bold text-slate-800">
                    <i class="bi bi-currency-dollar mr-1 text-emerald-500"></i>
                    {{ $type === 'service' ? 'Harga Jasa' : 'Harga Jual' }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500 font-bold bg-slate-50 rounded-l-xl border-r border-slate-200 px-3">Rp</div>
                    <input type="text" x-ref="input"
                        class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full pl-14 p-3 font-bold text-right shadow-sm placeholder:text-slate-300"
                        inputmode="numeric" 
                        placeholder="0">
                </div>
                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1"><i class="bi bi-info-circle"></i> Boleh 0 untuk gratis</p>
                @error('price') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
            </div>

            {{-- HPP (ITEM SAJA) --}}
            @if($type === 'item')
            <div class="md:col-span-5" x-data="moneyBox('cost_price')">
                <label class="block mb-2 text-sm font-bold text-slate-800">
                    <i class="bi bi-cash mr-1 text-red-500"></i> Harga Beli (HPP)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500 font-bold bg-red-50 rounded-l-xl border-r border-red-100 px-3 text-red-700">Rp</div>
                    <input type="text" x-ref="input"
                        class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block w-full pl-14 p-3 font-bold text-right shadow-sm placeholder:text-slate-300"
                        inputmode="numeric" 
                        placeholder="0">
                </div>
                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1"><i class="bi bi-info-circle"></i> Wajib diisi (boleh 0)</p>
                @error('cost_price') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
            </div>
            @endif
        </div>

        {{-- ALASAN INPUT MANUAL (WAJIB) --}}
        <div class="mb-6">
            <label class="block mb-2 text-sm font-bold text-slate-800">
                <i class="bi bi-chat-left-text mr-1"></i> Alasan Input Manual <span class="text-red-500">*</span>
            </label>
            <textarea wire:model.defer="manualReason" 
                class="block p-3 w-full text-sm text-slate-900 bg-white rounded-xl border border-slate-200 focus:ring-ob-primary focus:border-ob-primary shadow-sm resize-none"
                rows="3"
                placeholder="Contoh: Oli sponsor dari Pertamina, barang custom request customer, item tidak ada di master data, dll..."
                required></textarea>
            @error('manualReason') <p class="mt-1 text-xs text-red-600 font-bold flex items-center"><i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}</p> @enderror
            <p class="text-xs text-slate-500 mt-2 flex items-start gap-1 p-2 bg-orange-50 border border-orange-100 rounded-lg">
                <i class="bi bi-exclamation-triangle-fill text-orange-500 mt-0.5"></i>
                <span>Minimal 10 karakter. <strong class="text-orange-700">Owner akan menerima notifikasi untuk setiap input manual.</strong></span>
            </p>
        </div>

        {{-- PREVIEW PROFIT --}}
        <div class="rounded-xl border {{ $profit_unit < 0 ? 'bg-red-50/50 border-red-100' : 'bg-slate-50/50 border-slate-100' }} p-5 mb-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-slate-500">
                    <i class="bi bi-graph-up mr-1 opacity-70"></i> Profit per unit
                </span>
                <span class="font-bold font-mono {{ $profit_unit < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                    {{ number_format($profit_unit, 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-slate-200/50">
                <span class="text-sm text-slate-500">Total Profit (x{{ max(1, $qty) }})</span>
                <span class="text-lg font-bold font-mono {{ $profit_unit < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                    {{ number_format($profit_total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- TOMBOL TAMBAH --}}
        <button type="button" wire:click="add" wire:loading.attr="disabled"
            class="w-full text-white bg-ob-primary hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-bold rounded-xl text-lg px-5 py-4 shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-1 flex items-center justify-center gap-2Disabled:opacity-50 disabled:cursor-not-allowed group">
            <span wire:loading wire:target="add" class="animate-spin"><i class="bi bi-arrow-repeat"></i></span>
            <i class="bi bi-plus-circle-fill text-xl group-hover:scale-110 transition-transform" wire:loading.remove wire:target="add"></i>
            <span>{{ $type === 'service' ? 'Tambah Jasa Manual' : 'Tambah Item Manual' }}</span>
        </button>
    </div>

    {{-- Script inside root to avoid multi-root error --}}
    <script src="https://unpkg.com/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
             Alpine.data('moneyBox', (modelName) => ({
                model: modelName,
                an: null,
                init() {
                    if (typeof AutoNumeric === 'undefined') {
                         console.error('AutoNumeric not found'); 
                         return; 
                    }
                    
                    this.an = new AutoNumeric(this.$refs.input, {
                        digitGroupSeparator: '.', 
                        decimalCharacter: ',', 
                        decimalPlaces: 0,
                        unformatOnSubmit: true, 
                        modifyValueOnWheel: false, 
                        minimumValue: '-999999999'
                    });

                    let initialVal = @this.get(this.model);
                    this.an.set(initialVal || 0);

                    this.$refs.input.addEventListener('autoNumeric:rawValueModified', (e) => {
                         @this.set(this.model, e.detail.newRawValue);
                    });

                    this.$watch('$wire.' + this.model, (val) => {
                         if (this.an.getNumber() != val) {
                             this.an.set(val);
                         }
                    });
                }
             }));
        });
    </script>
</div>
