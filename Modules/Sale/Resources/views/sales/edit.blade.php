@extends('layouts.app-flowbite')

@section('title', 'Edit Penjualan')

@section('content')
    <div class="px-4 pt-4 mb-4">
        {{-- Breadcrumb --}}
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <i class="bi bi-house-door-fill mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <a href="{{ route('sales.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Penjualan</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Edit #{{ $sale->reference }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Search Product Section (Full Width) --}}
        <div class="mb-6">
            <livewire:search-product />
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @include('utils.alerts')

            <form id="sale-form" action="{{ route('sales.update', $sale) }}" method="POST">
                @csrf
                @method('patch')

                {{-- Reference & Date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="reference" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor Referensi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-upc-scan text-gray-500"></i>
                            </div>
                            <input type="text" name="reference" id="reference" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 cursor-not-allowed" value="{{ $sale->reference }}" readonly required>
                        </div>
                    </div>
                    <div>
                        <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-event text-gray-500"></i>
                            </div>
                            <input type="date" name="date" id="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" value="{{ $sale->date }}" required>
                        </div>
                    </div>
                </div>

                @php
                    use Gloudemans\Shoppingcart\Facades\Cart;
                    $cartItems = Cart::instance('sale')->content();

                    // Subtotal Calculation (Server Side)
                    $subtotalServer = $cartItems->sum(function ($i) {
                        $price = (int) $i->price;
                        $qty = data_get($i->options, 'source_type') === 'second' ? 1 : (int) $i->qty;
                        $disc = (int) data_get($i->options, 'discount', 0);
                        $tax = (int) data_get($i->options, 'tax', 0);
                        return max(0, $price * $qty - $disc + $tax);
                    });
                    
                    // Helper to normalize amount (legacy support)
                    $normalize100 = function ($amount) use ($subtotalServer) {
                        $amount = (int) $amount;
                        if ($amount > 0 && $subtotalServer > 0 && $amount >= $subtotalServer * 10 && $amount % 100 === 0) {
                            return (int) round($amount / 100);
                        }
                        return $amount;
                    };
                @endphp

                {{-- Cart Editor Table --}}
                <div wire:ignore id="cart-editor" class="mb-6">
                   <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-4 border border-gray-200">
                        <table class="w-full text-sm text-left text-gray-500" id="edit-cart-table">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th scope="col" class="px-3 py-3">Produk</th>
                                    <th scope="col" class="px-3 py-3 w-32 text-right">Harga Jual</th>
                                    <th scope="col" class="px-3 py-3 w-24 text-center">Qty</th>
                                    <th scope="col" class="px-3 py-3 w-32 text-right">Diskon Item</th>
                                    <th scope="col" class="px-3 py-3 w-28 text-right">Pajak Item</th>
                                    <th scope="col" class="px-3 py-3 w-36 text-right">Sub Total</th>
                                    <th scope="col" class="px-3 py-3 w-16 text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $it)
                                    @include('sale::partials.edit-row', ['it' => $it])
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t font-semibold text-gray-900">
                                <tr>
                                    <td colspan="5" class="px-3 py-3 text-right">Subtotal (item)</td>
                                    <td class="px-3 py-3 text-right">
                                        <span id="js-cart-subtotal">{{ number_format($subtotalServer, 0, ',', '.') }}</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Manual Item Add --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h6 class="mb-3 text-sm font-bold text-gray-900 flex items-center gap-2">
                             <i class="bi bi-plus-circle"></i> Tambah Item Manual
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <div class="md:col-span-4">
                                <label for="m_name" class="block mb-1 text-xs font-medium text-gray-700">Nama Item</label>
                                <input id="m_name" type="text" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Jasa Pasang Ban">
                            </div>
                            <div class="md:col-span-3">
                                <label for="m_price" class="block mb-1 text-xs font-medium text-gray-700">Harga (Rp)</label>
                                <input id="m_price" type="text" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 js-money" placeholder="0">
                            </div>
                            <div class="md:col-span-2">
                                <label for="m_qty" class="block mb-1 text-xs font-medium text-gray-700">Qty</label>
                                <input id="m_qty" type="number" min="1" value="1" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div class="md:col-span-3">
                                <button id="btnAddManual" type="button" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full flex items-center justify-center gap-2 transition-colors">
                                    <i class="bi bi-plus-lg"></i> Tambah
                                </button>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="bi bi-info-circle mr-1"></i> Item manual tidak memengaruhi stok produk.
                        </p>
                    </div>
                </div>

                {{-- Status, Payment, Shipping, Tax, Discount --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    {{-- Status --}}
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                             <option {{ $sale->status == 'Pending' ? 'selected' : '' }} value="Pending">Pending</option>
                             <option {{ $sale->status == 'Completed' ? 'selected' : '' }} value="Completed">Completed</option>
                        </select>
                    </div>

                     {{-- Payment Method --}}
                    <div x-data="{ pm: '{{ old('payment_method', $sale->payment_method) }}' }">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Metode Pembayaran <span class="text-red-500">*</span></label>
                        <select name="payment_method" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" x-model="pm">
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer">Transfer</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                        
                        <div class="mt-3" x-show="pm === 'Transfer'" x-transition>
                             <label class="block mb-1 text-sm font-medium text-gray-900">Bank / Rekening</label>
                             <input type="text" name="bank_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                value="{{ old('bank_name', $sale->bank_name) }}"
                                placeholder="BCA a.n. ..." :disabled="pm !== 'Transfer'"
                                x-effect="if(pm !== 'Transfer'){ $el.value = '' }">
                        </div>
                    </div>

                    {{-- Amounts --}}
                    <div class="space-y-4">
                        <div>
                             <label for="shipping_amount" class="block mb-2 text-sm font-medium text-gray-900">Biaya Kirim</label>
                             @php $shipValue = $normalize100($sale->shipping_amount ?? 0); @endphp
                             <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">Rp</span>
                                <input type="text" id="shipping_amount" name="shipping_amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 js-money"
                                value="{{ number_format((int) $shipValue, 0, ',', '.') }}" placeholder="0">
                             </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="tax_percentage" class="block mb-2 text-sm font-medium text-gray-900">Pajak (%)</label>
                                <input type="number" id="tax_percentage" name="tax_percentage" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="{{ (int) $sale->tax_percentage }}" min="0" max="100" step="1">
                            </div>
                            <div>
                                <label for="discount_percentage" class="block mb-2 text-sm font-medium text-gray-900">Diskon (%)</label>
                                <input type="number" id="discount_percentage" name="discount_percentage" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="{{ (int) $sale->discount_percentage }}" min="0" max="100" step="1">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary Panel --}}
                @php $existingPaid = (int) ($sale->paid_amount ?? 0); @endphp
                <div class="grid grid-cols-1 md:grid-cols-12 mb-6">
                    <div class="md:col-start-9 md:col-span-4">
                        <div class="mb-4">
                            <label for="paid_amount" class="block mb-2 text-sm font-medium text-gray-900">Target Total Dibayar (Preview)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">Rp</span>
                                <input type="text" name="paid_amount" id="paid_amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 js-money" value="{{ number_format($existingPaid, 0, ',', '.') }}">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Isi jika ada pembayaran susulan/refund. Kosongkan = tetap.</p>
                        </div>

                        <div x-data="invoiceSummary({
                            existingPaid: {{ $existingPaid }},
                            initialSubtotal: {{ $subtotalServer }},
                            ids: {
                                subtotalEl: 'js-cart-subtotal',
                                paid: 'paid_amount',
                                ship: 'shipping_amount',
                                tax: 'tax_percentage',
                                disc: 'discount_percentage',
                            }
                        })" x-init="init()" class="bg-gray-50 rounded-lg p-4 border border-gray-200" wire:ignore>
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-gray-700">Preview Status Baru</span>
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded"
                                    :class="{
                                        'bg-green-100 text-green-800': status === 'Paid',
                                        'bg-yellow-100 text-yellow-800': status === 'Partial',
                                        'bg-gray-100 text-gray-800': status === 'Unpaid'
                                    }" x-text="status"></span>
                            </div>

                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Subtotal item:</span>
                                    <span class="font-semibold" x-text="formatIDR(subtotal)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Pajak (<span x-text="taxPct"></span>%):</span>
                                    <span class="font-semibold" x-text="formatIDR(taxAmt)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Diskon (<span x-text="discPct"></span>%):</span>
                                    <span class="font-semibold" x-text="formatIDR(discAmt)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Ongkir:</span>
                                    <span class="font-semibold" x-text="formatIDR(shipping)"></span>
                                </div>
                                <div class="flex justify-between border-t border-gray-300 pt-2 mt-2">
                                    <span class="font-bold text-gray-800">Grand Total Baru:</span>
                                    <span class="font-bold text-gray-800" x-text="formatIDR(grandTotal)"></span>
                                </div>
                                <div class="flex justify-between text-blue-600">
                                    <span>Target Dibayar:</span>
                                    <span class="font-bold" x-text="formatIDR(targetPaid)"></span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>Selisih:</span>
                                    <span x-text="formatIDR(targetPaid - grandTotal)"></span>
                                </div>
                            </div>

                            <template x-if="warnTotalVsPaid">
                                <div class="mt-3 p-2 bg-yellow-50 text-yellow-700 text-xs rounded border border-yellow-200">
                                    <strong>Perhatian:</strong> Grand total menyimpang dari pembayaran yg ada. Pertimbangkan koreksi pembayaran.
                                </div>
                            </template>
                             <template x-if="warnDeltaPaid">
                                <div class="mt-2 p-2 bg-blue-50 text-blue-700 text-xs rounded border border-blue-200">
                                    <strong>Info:</strong> Perubahan pembayaran akan dicatat sebagai penyesuaian (+/-).
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div class="mb-6">
                    <label for="note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan</label>
                    <textarea id="note" name="note" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ $sale->note }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ url()->previous() ?: route('sales.index') }}" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200">
                        Batal
                    </a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none flex items-center shadow hover:shadow-lg transition-all">
                        Simpan Perubahan <i class="bi bi-check-lg ml-2"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4"></script>
    <script>
        /** ================= Helpers umum ================= */
        function stripMoney(v) {
            if (v == null) return 0;
            return parseInt(String(v).replace(/[^\d\-]/g, '')) || 0;
        }

        function formatIDR(n) {
            return (n || 0).toLocaleString('id-ID');
        }
        window.stripMoney = stripMoney;
        window.formatIDR = formatIDR;

        function getCsrfToken() {
            const f = document.getElementById('sale-form');
            const i = f?.querySelector('input[name="_token"]');
            if (i?.value) return i.value;
            const m = document.querySelector('meta[name="csrf-token"]');
            return m?.content || '';
        }

        function initAutoNumeric() {
            document.querySelectorAll('input.js-money').forEach(el => {
                if (el._an) return;
                el._an = new AutoNumeric(el, {
                    decimalPlaces: 0,
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    modifyValueOnWheel: false,
                    emptyInputBehavior: 'zero'
                });
            });
        }

        function setANValueById(id) {
            const el = document.getElementById(id);
            if (!el) return;
            if (!el._an) {
                el._an = new AutoNumeric(el, {
                    decimalPlaces: 0,
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    modifyValueOnWheel: false,
                    emptyInputBehavior: 'zero'
                });
            }
            el._an.set(stripMoney(el.value));
        }

        // Inisialisasi Alpine pada node yang baru disisipkan
        window.initAlpineTree = (node) => {
            if (window.Alpine?.initTree) Alpine.initTree(node);
        };

        /** ================= Endpoint (samakan dengan web.php) ================= */
        window.URLS = {
            addManual: "{{ route('sales.cart.addManual') }}",
            updateLine: "{{ route('sales.cart.updateLine') }}",
            removeLine: "{{ route('sales.cart.removeLine') }}",
        };

        /** ================= fetch wrapper ================= */
        async function postJSON(url, payload, timeoutMs = 15000) {
            const ctl = new AbortController();
            const timer = setTimeout(() => ctl.abort(), timeoutMs);
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    signal: ctl.signal
                });
                let json;
                try {
                    json = await res.json();
                } catch {
                    throw new Error('Respon bukan JSON valid');
                }
                if (!res.ok || json.ok === false) {
                    throw new Error(json.message || ('HTTP ' + res.status));
                }
                return json;
            } catch (err) {
                if (err.name === 'AbortError') {
                    throw new Error('Permintaan timeout. Coba lagi.');
                }
                throw err;
            } finally {
                clearTimeout(timer);
            }
        }

        /** ================= Invoice Summary (Alpine) ================= */
        function invoiceSummary({
            existingPaid,
            initialSubtotal,
            ids
        }) {
            return {
                WARN_ABS: 100000,
                WARN_PCT: 5,
                AUTO_K: true,
                existingPaid: parseInt(existingPaid || 0),
                subtotal: parseInt(initialSubtotal || 0),
                shipping: 0,
                taxPct: 0,
                discPct: 0,
                targetPaid: 0,

                init() {
                    initAutoNumeric();
                    setANValueById(ids.paid);
                    setANValueById(ids.ship);

                    const elSub = document.getElementById(ids.subtotalEl);
                    const elShip = document.getElementById(ids.ship);
                    const elTax = document.getElementById(ids.tax);
                    const elDisc = document.getElementById(ids.disc);
                    const elPaid = document.getElementById(ids.paid);

                    const readSubtotal = () => this.subtotal = stripMoney(elSub?.textContent || '0');
                    if (elSub) {
                        readSubtotal();
                        new MutationObserver(readSubtotal).observe(elSub, {
                            childList: true,
                            subtree: true,
                            characterData: true
                        });
                    }

                    const getAN = (el) => el ? (el._an ? parseInt(el._an.getNumber()) : stripMoney(el.value)) : 0;

                    const readShip = () => this.shipping = getAN(elShip);
                    if (elShip) {
                        readShip();
                        elShip.addEventListener('input', readShip);
                    }

                    const readTax = () => this.taxPct = parseInt(elTax?.value || 0);
                    const readDisc = () => this.discPct = parseInt(elDisc?.value || 0);
                    if (elTax) {
                        readTax();
                        elTax.addEventListener('input', readTax);
                    }
                    if (elDisc) {
                        readDisc();
                        elDisc.addEventListener('input', readDisc);
                    }

                    const readPaid = () => {
                        let v = getAN(elPaid);
                        if (this.AUTO_K && v > 0 && v < 1000 && (this.subtotal >= 5000 || this.shipping >= 5000)) {
                            v = v * 1000;
                            if (elPaid?._an) elPaid._an.set(v);
                        }
                        this.targetPaid = v;
                    };
                    if (elPaid) {
                        readPaid();
                        elPaid.addEventListener('input', readPaid);
                        setTimeout(readPaid, 120);
                        setTimeout(readPaid, 400);
                    }

                    const form = document.getElementById('sale-form');
                    form?.addEventListener('submit', () => {
                        [ids.paid, ids.ship].forEach(id => {
                            const el = document.getElementById(id);
                            if (el && el._an) el.value = el._an.getNumber();
                            else if (el) el.value = stripMoney(el.value);
                        });
                    });
                },

                get taxAmt() {
                    return Math.round(this.subtotal * (this.taxPct / 100));
                },
                get discAmt() {
                    return Math.round(this.subtotal * (this.discPct / 100));
                },
                get grandTotal() {
                    return Math.max(0, this.subtotal + this.taxAmt - this.discAmt + this.shipping);
                },

                get status() {
                    if (this.targetPaid <= 0) return 'Unpaid';
                    if (this.targetPaid >= this.grandTotal) return 'Paid';
                    return 'Partial';
                },

                get warnTotalVsPaid() {
                    const dev = Math.abs(this.grandTotal - this.existingPaid);
                    const pct = this.existingPaid > 0 ? (dev / this.existingPaid * 100) : 0;
                    return dev > this.WARN_ABS || pct > this.WARN_PCT;
                },
                get warnDeltaPaid() {
                    const dev = Math.abs(this.targetPaid - this.existingPaid);
                    const pct = this.existingPaid > 0 ? (dev / this.existingPaid * 100) : (this.targetPaid > 0 ? 100 :
                        0);
                    return dev > this.WARN_ABS || pct > this.WARN_PCT;
                },

                formatIDR
            }
        }
        window.invoiceSummary = invoiceSummary;

        /** ================= Row Editor (Alpine) ================= */
        function rowEditor({
            rowId,
            source,
            price,
            qty,
            discount,
            tax
        }) {
            return {
                rowId,
                source,
                price,
                qty,
                discount,
                tax,
                updating: false,
                _t: null,

                get lineSubtotal() {
                    const q = (this.source === 'second') ? 1 : (parseInt(this.qty || 0, 10) || 0);
                    const p = parseInt(this.price || 0, 10) || 0;
                    const d = parseInt(this.discount || 0, 10) || 0;
                    const t = parseInt(this.tax || 0, 10) || 0;
                    return Math.max(0, (p - d + t) * q);
                },
                get lineSubtotalLocal() {
                    return this.lineSubtotal;
                },

                init() {
                    if (this.source === 'second') this.qty = 1;
                    const AN_OPTS = {
                        decimalPlaces: 0,
                        digitGroupSeparator: '.',
                        decimalCharacter: ',',
                        modifyValueOnWheel: false,
                        emptyInputBehavior: 'zero'
                    };
                    this.$nextTick(() => {
                        this.$root.querySelectorAll('input.js-money').forEach(el => {
                            if (!el._an) el._an = new AutoNumeric(el, AN_OPTS);
                            const f = el.dataset.field;
                            if (f && typeof this[f] !== 'undefined') el._an.set(this[f]);
                            else el._an.reformat();
                        });
                    });
                },

                onPriceInput(e) {
                    this.price = stripMoney(e.target.value);
                    this._debouncePush();
                },
                onDiscInput(e) {
                    this.discount = stripMoney(e.target.value);
                    this._debouncePush();
                },
                onTaxInput(e) {
                    this.tax = stripMoney(e.target.value);
                    this._debouncePush();
                },
                onQtyInput(e) {
                    const v = parseInt(e.target.value || '0', 10) || 0;
                    this.qty = (this.source === 'second') ? 1 : Math.max(0, v);
                    this._debouncePush();
                },

                _debouncePush() {
                    clearTimeout(this._t);
                    this._t = setTimeout(() => this._pushUpdate(), 350);
                },

                async _pushUpdate() {
                    if (this.updating) return;
                    this.updating = true;
                    try {
                        const data = await postJSON(window.URLS.updateLine, {
                            rowId: this.rowId,
                            price: this.price,
                            qty: (this.source === 'second') ? 1 : this.qty,
                            discount: this.discount,
                            tax: this.tax
                        });

                        if (data.rowIdNew && data.rowIdNew !== this.rowId) {
                            this.rowId = data.rowIdNew;
                            this.$root?.setAttribute('data-rowid', this.rowId);
                        }
                        const subEl = document.getElementById('js-cart-subtotal');
                        if (subEl) {
                            subEl.textContent = data.formatted?.subtotalItems ?? formatIDR(data.subtotalItems);
                            subEl.dataset.raw = String(data.subtotalItems ?? 0);
                        }
                        const lineEl = this.$root?.querySelector('.js-line-subtotal');
                        if (lineEl) {
                            lineEl.textContent = data.formatted?.lineSubtotal ?? formatIDR(data.lineSubtotal);
                            lineEl.dataset.raw = String(data.lineSubtotal ?? 0);
                        }
                    } catch (e) {
                        console.error(e);
                        Swal.fire('Gagal', e.message || 'Update baris gagal', 'error');
                    } finally {
                        this.updating = false;
                    }
                },

                async remove() {
                    const ok = await Swal.fire({
                        icon: 'question',
                        title: 'Hapus item ini?',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(r => r.isConfirmed);
                    if (!ok || this.updating) return;

                    this.updating = true;
                    try {
                        const json = await postJSON(window.URLS.removeLine, {
                            rowId: this.rowId
                        });
                        const sub = document.getElementById('js-cart-subtotal');
                        if (sub) {
                            const val = json.formatted?.subtotalItems ?? json.summary?.display_sub ?? '0';
                            sub.textContent = val;
                        }
                        this.$root.remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Item dihapus',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    } catch (e) {
                        console.error(e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: e.message || 'Gagal terhubung ke server.'
                        });
                    } finally {
                        this.updating = false;
                    }
                },

                formatIDR
            }
        }
        window.rowEditor = rowEditor;

        /** ================= Tambah Item Manual ================= */
        document.addEventListener('DOMContentLoaded', () => {
            initAutoNumeric();

            const btn = document.getElementById('btnAddManual');
            const name = document.getElementById('m_name');
            const price = document.getElementById('m_price');
            const qty = document.getElementById('m_qty');
            const tbody = document.querySelector('#edit-cart-table tbody');

            [name, price, qty].forEach(el => {
                el?.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        btn?.click();
                    }
                });
            });

            btn?.addEventListener('click', async () => {
                const nm = (name?.value || '').trim();
                const pr = price && price._an ? parseInt(price._an.getNumber()) : stripMoney(price
                    ?.value || '0');
                const q = parseInt(qty?.value || '1', 10);

                if (!nm) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nama wajib diisi'
                    });
                    return;
                }
                if (pr <= 0 || q <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Harga & Qty harus > 0'
                    });
                    return;
                }

                btn.disabled = true;
                Swal.fire({
                    title: 'Menambahkan...',
                    didOpen: () => Swal.showLoading(),
                    allowOutsideClick: false
                });

                try {
                    const data = await postJSON(window.URLS.addManual, {
                        name: nm,
                        price: pr,
                        qty: q
                    });
                    Swal.close();

                    if (data.rowHtml) {
                        const temp = document.createElement('tbody');
                        temp.innerHTML = data.rowHtml.trim();
                        const newRow = temp.firstElementChild;
                        tbody.appendChild(newRow);

                        newRow.querySelectorAll('input.js-money').forEach(el => {
                            if (!el._an) el._an = new AutoNumeric(el, {
                                decimalPlaces: 0,
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                modifyValueOnWheel: false,
                                emptyInputBehavior: 'zero'
                            });
                        });
                        window.initAlpineTree(newRow);
                    }

                    const sub = document.getElementById('js-cart-subtotal');
                    if (sub) {
                        const val = data.formatted?.subtotalItems ?? data.summary?.display_sub ?? '0';
                        sub.textContent = val;
                    }

                    if (name) name.value = '';
                    if (price) price._an ? price._an.clear(true) : (price.value = '');
                    if (qty) qty.value = 1;

                    Swal.fire({
                        icon: 'success',
                        title: 'Item manual ditambahkan',
                        timer: 1200,
                        showConfirmButton: false
                    });
                } catch (e) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: e.message || 'Tidak bisa menambah item.'
                    });
                } finally {
                    btn.disabled = false;
                }
            });
        });
    </script>
@endpush
