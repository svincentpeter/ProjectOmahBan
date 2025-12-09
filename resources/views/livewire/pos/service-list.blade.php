<div>
    {{-- MAIN CONTENT --}}
    <div class="space-y-4">
        
        {{-- Quick Service Templates (Dynamic from Database) --}}
        @if($services && $services->count() > 0)
        <div>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">
                    <i class="bi bi-tools mr-1"></i>
                    Jasa Tersedia
                </h3>
                <p class="text-xs text-slate-500">
                    {{ $services->count() }} jasa • Klik untuk tambah ke keranjang
                </p>
            </div>
            
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                
                @php
                    // Define color scheme for dynamic styling
                    $colors = ['indigo', 'blue', 'emerald', 'amber', 'cyan', 'purple', 'pink', 'rose'];
                    $icons = ['bi-tools', 'bi-gear', 'bi-wrench', 'bi-arrow-repeat', 'bi-droplet', 'bi-bandaid', 'bi-align-center', 'bi-rulers'];
                @endphp
                
                @foreach($services as $index => $service)
                    @php
                        // Cycle through colors and icons
                        $colorIndex = $index % count($colors);
                        $color = $colors[$colorIndex];
                        $icon = $icons[$colorIndex];
                        
                        // Service details
                        $serviceName = $service->service_name;
                        $servicePrice = $service->standard_price;
                        $serviceCategory = $service->category ?? 'Layanan';
                    @endphp
                    
                    <button type="button"
                            wire:click="addServiceToCart({{ $service->id }})"
                            wire:loading.attr="disabled"
                            wire:key="service-{{ $service->id }}"
                            class="relative group flex flex-col items-center gap-3 rounded-2xl border-2 border-{{ $color }}-200 bg-gradient-to-br from-white to-{{ $color }}-50/30 p-4 text-center shadow-sm transition-all hover:border-{{ $color }}-400 hover:shadow-md active:scale-95">
                        
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-{{ $color }}-100 text-{{ $color }}-600 transition-colors group-hover:bg-{{ $color }}-600 group-hover:text-white">
                            <i class="bi {{ $icon }} text-2xl"></i>
                        </div>
                        
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $serviceName }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $serviceCategory }}</p>
                            <p class="mt-1 text-base font-bold text-{{ $color }}-600">{{ format_currency($servicePrice) }}</p>
                        </div>
                        
                        {{-- Loading Indicator --}}
                        <div wire:loading wire:target="addServiceToCart({{ $service->id }})" class="absolute inset-0 bg-white/80 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="animate-spin h-6 w-6 text-{{ $color }}-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                @endforeach
                
                {{-- Custom Service Button --}}
                <button type="button"
                        onclick="openCustomServiceModal()"
                        class="group flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-slate-300 bg-white p-4 text-center transition-all hover:border-indigo-400 hover:bg-indigo-50/50 active:scale-95">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 transition-colors group-hover:bg-indigo-100 group-hover:text-indigo-600">
                        <i class="bi bi-plus-circle text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700 group-hover:text-slate-900">Jasa Lainnya</p>
                        <p class="mt-0.5 text-xs text-slate-500">Input manual</p>
                    </div>
                </button>
                
            </div>
        </div>
        @else
            {{-- Empty State --}}
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-12">
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                        <i class="bi bi-inbox text-3xl text-slate-400"></i>
                    </div>
                    <h3 class="mb-1 text-base font-semibold text-slate-900">
                        Belum Ada Jasa
                    </h3>
                    <p class="text-sm text-slate-500 mb-4">
                        Silakan tambah jasa di master data terlebih dahulu.
                    </p>
                    <a href="{{ route('service-masters.index') }}" 
                       class="inline-flex items-center gap-2 text-sm text-ob-primary hover:text-indigo-700 font-semibold transition-colors">
                        <i class="bi bi-plus-circle"></i>
                        Tambah Jasa Baru
                    </a>
                </div>
            </div>
        @endif
        
        {{-- Info Banner --}}
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100">
                    <i class="bi bi-info-circle text-indigo-600"></i>
                </div>
                <div class="flex-1 text-sm">
                    <p class="font-semibold text-indigo-900">Tips Input Jasa</p>
                    <ul class="mt-1 space-y-0.5 text-xs text-indigo-700">
                        <li>• Klik <strong>Jasa</strong> untuk tambah langsung ke keranjang</li>
                        <li>• Klik <strong>Jasa Lainnya</strong> untuk input jasa custom dengan harga manual</li>
                        <li>• Anda bisa edit quantity jasa di keranjang sebelum checkout</li>
                    </ul>
                </div>
            </div>
        </div>
        
        {{-- Link to Manage Services --}}
        <div class="flex justify-center">
            <a href="{{ route('service-masters.index') }}" 
               class="inline-flex items-center gap-2 text-xs text-ob-primary hover:text-indigo-700 font-semibold transition-colors">
                <i class="bi bi-gear"></i>
                Kelola Master Jasa
            </a>
        </div>
        
    </div>

    {{-- MODAL: Custom Service Input --}}
    <div id="custom-service-modal" tabindex="-1" aria-hidden="true" 
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full bg-slate-900/50">
        <div class="relative p-4 w-full max-w-md">
            <div class="relative bg-white rounded-2xl shadow-xl border border-slate-200">
                
                <div class="flex items-center justify-between p-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Input Jasa Custom</h3>
                        <p class="text-xs text-slate-500">Untuk jasa yang tidak ada di template</p>
                    </div>
                    <button type="button" 
                            onclick="closeCustomServiceModal()"
                            class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                
                <form id="custom-service-form" class="p-4 space-y-4" onsubmit="submitCustomService(event)">
                    
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-slate-700">
                            Nama Jasa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="service-name"
                               class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Contoh: Ganti Oli Mesin, Cuci Velg, dll"
                               required>
                        <p class="mt-1 text-xs text-slate-500">
                            <i class="bi bi-info-circle"></i>
                            Nama jasa akan muncul di struk
                        </p>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-slate-700">
                            Harga Jasa <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-sm">
                                Rp
                            </span>
                            <input type="number" 
                                   id="service-price"
                                   class="w-full rounded-xl border-slate-200 pl-10 pr-3 py-2.5 text-sm text-right font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="0"
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-slate-700">
                            Quantity
                        </label>
                        <div class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50">
                            <button type="button"
                                    onclick="changeServiceQty(-1)"
                                    class="px-3 py-2 text-slate-500 hover:bg-slate-100 rounded-l-xl transition-colors">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" 
                                   id="service-qty"
                                   value="1"
                                   min="1"
                                   class="w-16 border-x border-slate-200 bg-white text-center text-sm focus:outline-none">
                            <button type="button"
                                    onclick="changeServiceQty(1)"
                                    class="px-3 py-2 text-slate-500 hover:bg-slate-100 rounded-r-xl transition-colors">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-slate-700">
                            Catatan / Alasan <span class="text-xs text-slate-500">(Opsional)</span>
                        </label>
                        <textarea id="service-reason"
                                  rows="2"
                                  class="w-full rounded-xl border-slate-200 px-3 py-2 text-xs focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Contoh: Oli 10W-40 fully synthetic, cuci velg + chrome, dll"></textarea>
                        <p class="mt-1 text-xs text-slate-500">
                            Catatan untuk audit/laporan, tidak muncul di struk
                        </p>
                    </div>
                    
                </form>
                
                <div class="px-4 py-3 border-t border-slate-100 flex gap-2">
                    <button type="button"
                            onclick="closeCustomServiceModal()"
                            class="flex-1 inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            onclick="submitCustomService(event)"
                            class="flex-1 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                        <i class="bi bi-plus-circle mr-1.5"></i>
                        Tambahkan
                    </button>
                </div>
                
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
    function changeServiceQty(delta) {
        const input = document.getElementById('service-qty');
        const currentValue = parseInt(input.value) || 1;
        const newValue = Math.max(1, currentValue + delta);
        input.value = newValue;
    }

    function submitCustomService(event) {
        if (event) event.preventDefault();
        
        const name = document.getElementById('service-name').value.trim();
        const price = parseInt(document.getElementById('service-price').value) || 0;
        const qty = parseInt(document.getElementById('service-qty').value) || 1;
        const reason = document.getElementById('service-reason').value.trim();
        
        if (!name) {
            alert('Nama jasa harus diisi!');
            return;
        }
        
        if (price <= 0) {
            alert('Harga jasa harus lebih dari 0!');
            return;
        }
        
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('addCustomService', { name, price, qty, reason });
            document.getElementById('custom-service-form').reset();
            document.getElementById('service-qty').value = 1;
            closeCustomServiceModal();
        } else if (typeof addToCart === 'function') {
            addToCart(null, name, price, 'service', qty);
            document.getElementById('custom-service-form').reset();
            document.getElementById('service-qty').value = 1;
            closeCustomServiceModal();
        } else {
            alert('Error: Cart function not available');
        }
    }

    function closeCustomServiceModal() {
        const modal = document.getElementById('custom-service-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    function openCustomServiceModal() {
        const modal = document.getElementById('custom-service-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('custom-service-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeCustomServiceModal();
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('custom-service-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeCustomServiceModal();
            }
        }
    });
    </script>
</div>
