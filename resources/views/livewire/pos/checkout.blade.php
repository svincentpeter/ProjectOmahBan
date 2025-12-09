<div>
    {{-- =======================================
         KANAN (1 kolom): KERANJANG & CHECKOUT
    ======================================== --}}
    
    {{-- KERANJANG RINGKAS --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm mb-4">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-semibold text-slate-900">
                    <i class="bi bi-cart3 mr-1"></i> Keranjang
                </h2>
                <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 text-xs text-slate-600">
                    {{ Cart::instance($cart_instance)->count() }} item
                </span>
            </div>
            @if(Cart::instance($cart_instance)->count() > 0)
            <button onclick="confirmReset()" type="button"
                class="text-xs text-slate-500 hover:text-red-600 inline-flex items-center gap-1 transition-colors">
                <i class="bi bi-trash"></i> Kosongkan
            </button>
            @endif
        </div>

        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
            @if(Cart::instance($cart_instance)->count() > 0)
                <ul class="divide-y divide-slate-100 text-sm">
                    @foreach($cart_items as $cart_item)
                    <li class="px-4 py-3 flex items-start justify-between gap-2 hover:bg-slate-50/50 transition-colors group">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-900 leading-snug truncate" title="{{ $cart_item->name }}">
                                {{ $cart_item->name }}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                {{ format_currency($cart_item->price) }} 
                                <span class="text-slate-300">•</span> 
                                <span class="font-semibold text-slate-700">Total: {{ format_currency($cart_item->subtotal) }}</span>
                                
                                @if(in_array($cart_item->options->source_type ?? 'new', ['new', 'second']))
                                    @hasanyrole('Admin|Super Admin|Owner|Supervisor|Kasir')
                                        <button wire:click="openEditPriceModal('{{ $cart_item->rowId }}')" class="ml-2 text-[10px] text-amber-600 hover:underline">
                                            Edit Harga
                                        </button>
                                    @endhasanyrole
                                @endif
                            </div>
                            
                            <div class="mt-2 inline-flex items-center rounded-lg border border-slate-200 bg-white shadow-sm">
                                <button wire:click="decrementQuantity('{{ $cart_item->rowId }}')" class="px-2 py-1 text-slate-500 hover:bg-slate-100 text-xs rounded-l-lg transition-colors">−</button>
                                <input type="text" value="{{ $cart_item->qty }}" class="w-8 text-center text-xs border-0 py-1 px-0 focus:ring-0 text-slate-700" readonly>
                                <button wire:click="incrementQuantity('{{ $cart_item->rowId }}')" class="px-2 py-1 text-slate-500 hover:bg-slate-100 text-xs rounded-r-lg transition-colors">+</button>
                            </div>
                        </div>
                        <button wire:click="removeItem('{{ $cart_item->rowId }}')" class="text-slate-400 hover:text-red-500 p-1 opacity-0 group-hover:opacity-100 transition-all">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                    <i class="bi bi-cart-x text-3xl mb-2 opacity-50"></i>
                    <p class="text-xs">Keranjang kosong</p>
                </div>
            @endif
        </div>

        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/30">
            <label class="block text-xs font-medium text-slate-600 mb-1">
                Catatan Transaksi
            </label>
            <textarea wire:model.defer="note"
                rows="1"
                class="w-full rounded-xl border-slate-200 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-ob-primary focus:ring-ob-primary resize-none bg-white"
                placeholder="Catatan untuk struk..."></textarea>
        </div>
        
        {{-- Customer Selection --}}
        <div class="px-4 py-3 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2">
                 <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Customer</div>
                 <div class="flex bg-slate-100 rounded-lg p-0.5 border border-slate-200">
                    <button wire:click="setCustomerMode('select')" class="px-2 py-0.5 text-[10px] rounded leading-none transition-all {{ $customer_mode === 'select' ? 'bg-white text-ob-primary shadow-sm font-semibold' : 'text-slate-500 hover:text-slate-700' }}">Pilih</button>
                    <button wire:click="setCustomerMode('manual')" class="px-2 py-0.5 text-[10px] rounded leading-none transition-all {{ $customer_mode === 'manual' ? 'bg-white text-ob-primary shadow-sm font-semibold' : 'text-slate-500 hover:text-slate-700' }}">Baru</button>
                 </div>
            </div>

            <div class="{{ $customer_mode !== 'select' ? 'hidden' : '' }}">
                <div wire:ignore>
                    <select class="select2-customer w-full" id="select-customer">
                        <option value="">Cari member...</option>
                    </select>
                </div>
                
                {{-- Selected Info --}}
                @if($customer_id)
                <div id="selected-customer-info" class="mt-2 p-2 bg-emerald-50 border border-emerald-100 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2 overflow-hidden">
                         <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 text-xs">
                           <i class="bi bi-person-fill"></i>
                         </div>
                         <div class="min-w-0">
                             <div class="text-xs font-bold text-slate-800 truncate" id="display-customer-name">{{ $customer_name }}</div>
                         </div>
                    </div>
                    <button type="button" wire:click="$set('customer_id', null)" class="text-emerald-400 hover:text-red-500 text-xs"><i class="bi bi-x-circle-fill"></i></button>
                </div>
                @endif
            </div>

            <div class="{{ $customer_mode !== 'manual' ? 'hidden' : '' }} space-y-2">
                <input type="text" wire:model.defer="customer_name" class="w-full text-xs px-3 py-2 rounded-lg border-slate-200 focus:border-ob-primary focus:ring-ob-primary" placeholder="Nama Lengkap">
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" wire:model.defer="customer_phone" class="w-full text-xs px-3 py-2 rounded-lg border-slate-200 focus:border-ob-primary focus:ring-ob-primary" placeholder="No HP/WA">
                    <input type="email" wire:model.defer="customer_email" class="w-full text-xs px-3 py-2 rounded-lg border-slate-200 focus:border-ob-primary focus:ring-ob-primary" placeholder="Email">
                </div>
            </div>
             
             {{-- Hidden syncing fields --}}
             <input type="hidden" id="selected-customer-id" wire:model.defer="customer_id">
        </div>
    </div>

    {{-- RINGKASAN & PEMBAYARAN --}}
    @if(!$sale)
    {{-- ========== STATE 1: KERANJANG AKTIF - BELUM ADA INVOICE ========== --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-4 space-y-4">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900">Ringkasan Pembayaran</h2>
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Siap Checkout
            </span>
        </div>

        {{-- Price Breakdown --}}
        <dl class="space-y-2 text-sm">
            {{-- Sub Total --}}
            <div class="flex justify-between">
                <dt class="text-slate-500">Sub Total</dt>
                <dd class="font-semibold text-slate-800">{{ format_currency(Cart::instance($cart_instance)->subtotal()) }}</dd>
            </div>
            
            {{-- Diskon Transaksi --}}
            <div class="flex justify-between items-center">
                <dt class="text-slate-500 flex items-center gap-1">
                    Diskon Transaksi
                    <button type="button" wire:click="openDiscountModal" 
                        class="text-indigo-600 hover:text-indigo-700 transition-colors" 
                        title="Edit Diskon">
                        <i class="bi bi-pencil text-xs"></i>
                    </button>
                </dt>
                <dd class="font-semibold {{ $global_discount > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                    - {{ format_currency($global_discount ?? 0) }}
                </dd>
            </div>
        </dl>

        {{-- Total Bayar (Prominent) --}}
        <div class="border-t border-dashed border-slate-200 pt-3 flex items-baseline justify-between">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Bayar</span>
            <span class="text-2xl font-bold text-slate-900">{{ format_currency($total_amount) }}</span>
        </div>

        {{-- Tombol Cetak Invoice --}}
        <button wire:click="createInvoice" 
            wire:loading.attr="disabled"
            @if(Cart::instance($cart_instance)->count() === 0) disabled @endif
            class="w-full inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-emerald-200 hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 disabled:bg-slate-300 disabled:cursor-not-allowed transition-all gap-2">
            <span wire:loading wire:target="createInvoice" class="animate-spin"><i class="bi bi-arrow-repeat"></i></span>
            <i class="bi bi-receipt" wire:loading.remove wire:target="createInvoice"></i>
            <span>Cetak Invoice</span>
        </button>
    </div>
    @else
    {{-- ========== STATE 2: INVOICE SUDAH DIBUAT ========== --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        {{-- Header Success --}}
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="bi bi-check-lg text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Invoice Berhasil Dibuat!</h3>
                    <p class="text-emerald-100 text-sm">No. {{ $sale->reference }}</p>
                </div>
            </div>
        </div>

        {{-- Detail Invoice --}}
        <div class="p-4 space-y-3">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500">Tanggal</dt>
                    <dd class="font-medium text-slate-800">{{ $sale->date ? \Carbon\Carbon::parse($sale->date)->format('d M Y') : now()->format('d M Y') }}</dd>
                </div>
                @if($sale->customer_name)
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500">Customer</dt>
                    <dd class="font-medium text-slate-800">{{ $sale->customer_name }}</dd>
                </div>
                @endif
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500">Kasir</dt>
                    <dd class="font-medium text-slate-800">{{ auth()->user()->name }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500">Jumlah Item</dt>
                    <dd class="font-medium text-slate-800">{{ $sale->saleDetails->count() ?? 0 }} item</dd>
                </div>
                <div class="flex justify-between py-2 border-t border-dashed">
                    <dt class="text-slate-700 font-semibold">Total</dt>
                    <dd class="font-bold text-lg text-slate-900">{{ format_currency($sale->total_amount) }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500">Status Bayar</dt>
                    <dd>
                        @if($sale->payment_status === 'Paid')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">
                                <i class="bi bi-check-circle-fill"></i> Lunas
                            </span>
                        @elseif($sale->payment_status === 'Partial')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                                <i class="bi bi-clock-fill"></i> Sebagian
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                <i class="bi bi-x-circle-fill"></i> Belum Bayar
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>

            {{-- Tombol Cetak PDF --}}
            <a href="{{ route('sales.pos.pdf', $sale) }}" 
               target="_blank"
               class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                <i class="bi bi-printer-fill"></i> 
                Cetak Nota (PDF)
            </a>

            {{-- Form Pembayaran (jika belum lunas) --}}
            @if($sale->payment_status !== 'Paid')
            <div class="border-t border-slate-200 pt-4 mt-4 space-y-3">
                <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i class="bi bi-credit-card"></i> Proses Pembayaran
                </h4>

                {{-- Metode Pembayaran --}}
                <div class="space-y-2">
                    <p class="text-xs font-medium text-slate-600">Metode Bayar</p>
                    <div class="grid grid-cols-4 gap-2 text-xs">
                        @foreach(['Tunai', 'Transfer', 'QRIS', 'Midtrans'] as $pm)
                            <button wire:click="$set('payment_method', '{{ $pm }}')"
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border px-1 py-2 font-semibold transition-all
                                {{ $payment_method === $pm || ($payment_method === 'Cash' && $pm === 'Tunai')
                                    ? 'border-ob-primary bg-ob-primary text-white shadow-md' 
                                    : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' 
                                }}">
                                {{ $pm }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Input Bank Name untuk Transfer --}}
                @if($payment_method === 'Transfer')
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-600">Nama Bank</label>
                    <input type="text" wire:model.defer="bank_name" 
                        class="w-full rounded-xl border-slate-200 px-3 py-2 text-sm focus:border-ob-primary focus:ring-ob-primary"
                        placeholder="Contoh: BCA, Mandiri, BRI...">
                    @error('bank_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Input Nominal --}}
                @if($payment_method !== 'Midtrans')
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Dibayar (Rp)</label>
                    <div x-data="paidBox(@entangle('paid_amount').live, 'paid_amount')">
                        <input type="text" x-ref="input" 
                            class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm text-right font-bold focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="0">
                    </div>

                    {{-- Quick Amount Buttons --}}
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <button type="button" wire:click="$set('paid_amount', {{ (int)($sale->total_amount ?? 0) }})"
                            class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                            Pas
                        </button>
                        <button type="button" wire:click="$set('paid_amount', {{ (int)($paid_amount ?? 0) + 50000 }})"
                            class="rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                            +50K
                        </button>
                        <button type="button" wire:click="$set('paid_amount', {{ (int)($paid_amount ?? 0) + 100000 }})"
                            class="rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                            +100K
                        </button>
                        <button type="button" wire:click="$set('paid_amount', {{ (int)($paid_amount ?? 0) + 500000 }})"
                            class="rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                            +500K
                        </button>
                    </div>
                </div>

                @php
                    $salePaid = (int) ($paid_amount ?? 0);
                    $saleTotal = (int) ($sale->total_amount ?? 0);
                    $saleDiff = $salePaid - $saleTotal;
                @endphp
                
                <div class="flex justify-between text-sm py-2 px-3 bg-slate-50 rounded-xl">
                    <span class="{{ $saleDiff >= 0 ? 'text-slate-500' : 'text-red-500 font-bold' }}">{{ $saleDiff >= 0 ? 'Kembalian' : 'Kurang' }}</span>
                    <span class="font-bold {{ $saleDiff >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ format_currency(abs($saleDiff)) }}
                    </span>
                </div>
                @endif

                <button wire:click="markAsPaid" 
                    wire:loading.attr="disabled"
                    class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 disabled:opacity-50">
                    <span wire:loading wire:target="markAsPaid" class="animate-spin"><i class="bi bi-arrow-repeat"></i></span>
                    <i class="bi bi-check-circle" wire:loading.remove wire:target="markAsPaid"></i>
                    <span>Tandai Lunas</span>
                </button>
            </div>
            @else
            {{-- Sudah Lunas --}}
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-center">
                <i class="bi bi-check-circle-fill text-emerald-600 text-3xl mb-2"></i>
                <p class="font-bold text-emerald-800">Transaksi Lunas!</p>
                <p class="text-emerald-600 text-sm">Pembayaran telah diterima</p>
            </div>
            @endif

            {{-- Tombol Transaksi Baru --}}
            <button wire:click="resetForNewTransaction" 
                class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 text-slate-700 py-3 rounded-xl font-semibold hover:bg-slate-200 transition-all border border-slate-200">
                <i class="bi bi-plus-circle"></i>
                <span>Transaksi Baru</span>
            </button>
        </div>
    </div>
    @endif

    {{-- ========================================= --}}
    {{-- MODAL EDIT HARGA                          --}}
    {{-- ========================================= --}}
    @if($showEditPriceModal)
    <div class="fixed inset-0 z-[999] flex items-center justify-center w-full h-full bg-black/50 backdrop-blur-sm p-4">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            {{-- Header --}}
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Edit Harga Item</h3>
                    <p class="text-xs text-slate-500">{{ $editingProductName }}</p>
                </div>
                <button wire:click="closeEditPriceModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="p-4 space-y-4">

                {{-- Harga Asli --}}
                <div class="rounded-lg bg-slate-50 p-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Harga Asli</label>
                    <p class="text-lg font-bold text-slate-900">{{ format_currency($editingOriginalPrice) }}</p>
                </div>

                {{-- Input Potongan --}}
                <div x-data="paidBox(@entangle('discountAmount').live, 'discountAmount')">
                    <label class="block mb-2 text-sm font-semibold text-slate-700">
                        Potongan Harga <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-sm">Rp</span>
                        <input type="text" x-ref="input"
                            class="w-full rounded-xl border-slate-200 pl-10 pr-3 py-2.5 text-sm text-right font-semibold focus:border-amber-500 focus:ring-amber-500"
                            placeholder="0">
                    </div>
                    <p class="mt-1 text-xs text-slate-500">
                        <i class="bi bi-info-circle"></i> Masukkan nominal potongan (bukan harga akhir)
                    </p>
                    @error('discountAmount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                {{-- Harga Akhir (Auto Calculate) --}}
                @php
                    $finalPrice = max(0, ($editingOriginalPrice ?? 0) - ($discountAmount ?? 0));
                    $discountPercent = $editingOriginalPrice > 0 ? round(($discountAmount / $editingOriginalPrice) * 100, 1) : 0;
                @endphp
                <div class="rounded-lg border-2 border-emerald-200 bg-emerald-50 p-3">
                    <label class="block text-xs font-medium text-emerald-900 mb-1">Harga Akhir</label>
                    <p class="text-xl font-bold {{ $discountAmount > $editingOriginalPrice * 0.5 ? 'text-red-600' : 'text-emerald-700' }}">
                        {{ format_currency($finalPrice) }}
                    </p>
                    @if($discountAmount > 0)
                    <p class="text-xs text-emerald-600 mt-1">Diskon {{ $discountPercent }}% dari harga asli</p>
                    @endif
                </div>

                {{-- Alasan Edit (Wajib) --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold text-slate-700">
                        Alasan Edit Harga <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model.defer="priceNote" rows="2"
                        class="w-full rounded-xl border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"
                        placeholder="Contoh: Diskon member loyal, barang display, promosi khusus, dll"></textarea>
                    @error('priceNote') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    <p class="mt-1 text-xs text-amber-700">
                        <i class="bi bi-shield-exclamation"></i> Alasan wajib diisi untuk audit & laporan owner
                    </p>
                </div>

                {{-- Warning Box --}}
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                    <p class="font-semibold flex items-center gap-1">
                        <i class="bi bi-exclamation-triangle"></i> Perhatian
                    </p>
                    <ul class="mt-1 space-y-0.5 list-disc list-inside text-amber-800">
                        <li>Perubahan harga akan tercatat dalam sistem audit</li>
                        <li>Owner akan menerima notifikasi untuk transaksi ini</li>
                    </ul>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-4 py-3 border-t border-slate-100 flex gap-2">
                <button wire:click="closeEditPriceModal"
                    class="flex-1 inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button wire:click="saveEditedPrice"
                    class="flex-1 inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700">
                    <i class="bi bi-check-circle mr-1.5"></i> Terapkan
                </button>
            </div>

        </div>
    </div>
    @endif

    {{-- ========================================= --}}
    {{-- MODAL DISKON TRANSAKSI                    --}}
    {{-- ========================================= --}}
    @if($showDiscountModal)
    <div class="fixed inset-0 z-[999] flex items-center justify-center w-full h-full bg-black/50 backdrop-blur-sm p-4">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            
            {{-- Header --}}
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Diskon Transaksi</h3>
                    <p class="text-xs text-slate-500">Diskon untuk keseluruhan transaksi</p>
                </div>
                <button wire:click="closeDiscountModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="p-4 space-y-4">
                
                {{-- Input Diskon --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold text-slate-700">
                        Nilai Diskon (Rp)
                    </label>
                    <div x-data="paidBox(@entangle('tempDiscountAmount').live, 'tempDiscountAmount')">
                        <input type="text" x-ref="input"
                            class="w-full rounded-xl border-slate-200 px-3 py-2.5 text-sm text-right font-bold focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="0">
                    </div>
                </div>
                
                {{-- Alasan --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold text-slate-700">
                        Alasan Diskon <span class="text-xs text-slate-500">(Wajib jika ada diskon)</span>
                    </label>
                    <textarea wire:model.defer="discountNote"
                        class="w-full rounded-xl border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        rows="2"
                        placeholder="Contoh: Member loyal, promosi, dll"></textarea>
                </div>

                {{-- Preview Kalkulasi --}}
                @php
                    // Fix: gunakan cart()->total() dengan format yang benar
                    $cartSubtotal = (int) Cart::instance($cart_instance)->total(0, '', '');
                    $discPreview = (int) ($tempDiscountAmount ?? 0);
                    $totalPreview = max(0, $cartSubtotal - $discPreview);
                @endphp
                <div class="bg-slate-50 rounded-xl p-3 space-y-1.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Sub Total</span>
                        <span class="font-medium text-slate-800">{{ format_currency($cartSubtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Diskon</span>
                        <span class="font-medium text-emerald-600">- {{ format_currency($discPreview) }}</span>
                    </div>
                    <div class="flex justify-between text-base border-t border-dashed border-slate-200 pt-2 mt-2">
                        <span class="font-bold text-slate-700">Total Bayar</span>
                        <span class="font-bold text-slate-900">{{ format_currency($totalPreview) }}</span>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-4 py-3 border-t border-slate-100 flex gap-2">
                <button wire:click="closeDiscountModal"
                    class="flex-1 inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button wire:click="saveDiscount"
                    class="flex-1 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Terapkan
                </button>
            </div>

        </div>
    </div>
    @endif
    {{-- MOVED STYLES INSIDE ROOT --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { bg: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        
        /* Select2 Customization */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-color: #e2e8f0 !important;
            border-radius: 0.5rem !important;
            padding-top: 5px;
            font-size: 0.875rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { top: 6px !important; }
        .select2-dropdown { border-color: #e2e8f0 !important; border-radius: 0.5rem !important; font-size: 0.875rem; }
    </style>

    {{-- MOVED SCRIPTS INSIDE ROOT --}}
    <script src="https://unpkg.com/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            // Alpine Component for AutoNumeric (multi-field)
            Alpine.data('paidBox', (modelVal, livewireField) => ({
                val: modelVal,
                field: livewireField,
                an: null,
                init() {
                    if (typeof AutoNumeric === 'undefined') {
                        console.error('AutoNumeric not loaded');
                        return;
                    }
                    this.an = new AutoNumeric(this.$refs.input, {
                        digitGroupSeparator: '.',
                        decimalCharacter: ',',
                        decimalPlaces: 0,
                        unformatOnSubmit: true,
                        modifyValueOnWheel: false,
                        minimumValue: 0,
                        allowDecimalPadding: false
                    });

                    // Set initial value
                    this.an.set(this.val || 0);

                    // Sync from Input -> Livewire
                    this.$refs.input.addEventListener('autoNumeric:rawValueModified', (e) => {
                        this.val = e.detail.newRawValue;

                        // biar Alpine tetap "bangun"
                        this.$refs.input.dispatchEvent(new Event('input'));

                        // Kirim ke properti Livewire yang benar
                        if (this.field) {
                            @this.set(this.field, this.val);
                        }
                    });

                    // Watch dari Alpine ke input (kalau ada perubahan dari sisi Livewire)
                    this.$watch('val', (value) => {
                        if (this.an && this.an.getNumber() != value) {
                            this.an.set(value || 0);
                        }
                    });
                }
            }));
        });

        // Select2 Logic
        document.addEventListener('livewire:initialized', () => {
             const initSelect2 = () => {
                 $('#select-customer').select2({
                    placeholder: 'Cari customer...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '{{ route('customers.list') }}',
                        dataType: 'json',
                        delay: 250,
                        data: (params) => ({ search: params.term, page: params.page || 1 }),
                        processResults: (data) => ({
                            results: (data.results || []).map(c => ({ 
                                id: c.id, 
                                text: c.text || c.name || c.customer_name,
                                customer_name: c.name || c.customer_name,
                                phone: c.phone || c.customer_phone
                            })),
                            pagination: { more: data.pagination?.more || false }
                        }),
                        cache: true
                    }
                }).on('select2:select', function (e) {
                    const data = e.params.data;
                    @this.set('customer_id', data.id);
                    @this.set('customer_name', data.customer_name);
                }).on('select2:clear', function () {
                    @this.set('customer_id', null);
                });
            };
    
            initSelect2();
        });
    </script>
</div>
