@extends('layouts.app')

@section('title', 'Edit Penjualan')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Penjualan</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@php
    // ====== Opsional: sembunyikan kolom pajak per-item kalau memang tidak dipakai ======
    $USE_ITEM_TAX = true; // set ke false untuk menyembunyikan kolom "Pajak Item" di tabel editor
@endphp

@push('third_party_stylesheets')
    @if (!$USE_ITEM_TAX)
        <style>
            /* Sembunyikan kolom Pajak Item (kolom ke-5) di tabel editor */
            #edit-cart-table thead th:nth-child(5),
            #edit-cart-table tbody td:nth-child(5),
            #edit-cart-table tfoot th:nth-child(5) {
                display: none !important;
            }
        </style>
    @endif
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                {{-- Komponen pencarian/penambahan produk tetap --}}
                <livewire:search-product />
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('utils.alerts')

                        <form id="sale-form" action="{{ route('sales.update', $sale) }}" method="POST">
                            @csrf
                            @method('patch')

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="reference">Nomor Referensi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required
                                            value="{{ $sale->reference }}" readonly>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required
                                            value="{{ $sale->date }}">
                                    </div>
                                </div>
                            </div>

                            @php
                                use Gloudemans\Shoppingcart\Facades\Cart;

                                $cartItems = Cart::instance('sale')->content();

                                // Subtotal item (server-truth) — sesuai struktur detail (price, qty, discount, tax)
                                $subtotalServer = $cartItems->sum(function ($i) {
                                    $price = (int) $i->price;
                                    $qty = data_get($i->options, 'source_type') === 'second' ? 1 : (int) $i->qty;
                                    $disc = (int) data_get($i->options, 'discount', 0);
                                    $tax = (int) data_get($i->options, 'tax', 0);
                                    return max(0, $price * $qty - $disc + $tax);
                                });

                                // Normalisasi angka ribuan yang mungkin disimpan x100 (kebiasaan impor)
                                $normalize100 = function ($amount) use ($subtotalServer) {
                                    $amount = (int) $amount;
                                    if (
                                        $amount > 0 &&
                                        $subtotalServer > 0 &&
                                        $amount >= $subtotalServer * 10 &&
                                        $amount % 100 === 0
                                    ) {
                                        return (int) round($amount / 100);
                                    }
                                    return $amount;
                                };
                            @endphp

                            {{-- ========== Editor Keranjang (tetap pakai partial baris agar selaras server) ========== --}}
                            <div wire:ignore id="cart-editor">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-body p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0" id="edit-cart-table">
                                                <thead>
                                                    <tr>
                                                        <th>Produk</th>
                                                        <th style="width:110px" class="text-right">Harga Jual</th>
                                                        <th style="width:90px" class="text-center">Qty</th>
                                                        <th style="width:120px" class="text-right">Diskon Item</th>
                                                        <th style="width:100px" class="text-right">Pajak Item</th>
                                                        <th style="width:140px" class="text-right">Sub Total</th>
                                                        <th style="width:42px"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cartItems as $it)
                                                        {{-- Tetap gunakan partial agar markup server (rowHtml) konsisten --}}
                                                        @include('sale::partials.edit-row', ['it' => $it])
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="5" class="text-right">Subtotal (item)</th>
                                                        <th class="text-right">
                                                            <span id="js-cart-subtotal">
                                                                {{ number_format($subtotalServer, 0, ',', '.') }}
                                                            </span>
                                                        </th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- === Tambah Item Manual === --}}
                                <div class="card mb-3" wire:ignore>
                                    <div class="card-body">
                                        <h6 class="mb-2">Tambah Item Manual</h6>
                                        <div class="form-row">
                                            <div class="col-md-4">
                                                <input id="m_name" type="text" class="form-control"
                                                    placeholder="Nama item">
                                            </div>
                                            <div class="col-md-3">
                                                <input id="m_price" type="text" class="form-control js-money"
                                                    placeholder="Harga (Rp)">
                                            </div>
                                            <div class="col-md-2">
                                                <input id="m_qty" type="number" min="1" value="1"
                                                    class="form-control" placeholder="Qty">
                                            </div>
                                            <div class="col-md-3">
                                                <button id="btnAddManual" type="button"
                                                    class="btn btn-success">Tambah</button>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Item manual tidak memengaruhi stok dan dicatat sebagai <em>source</em>
                                            <code>manual</code>.
                                        </small>
                                    </div>
                                </div>
                                {{-- === /Tambah Item Manual === --}}
                            </div>
                            {{-- ========== /Editor Keranjang ========== --}}

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option {{ $sale->status == 'Pending' ? 'selected' : '' }} value="Pending">
                                                Pending</option>
                                            <option {{ $sale->status == 'Completed' ? 'selected' : '' }} value="Completed">
                                                Completed</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Metode Pembayaran + bank_name saat Transfer --}}
                                <div class="col-lg-4">
                                    <div class="form-group" x-data="{ pm: '{{ old('payment_method', $sale->payment_method) }}' }">
                                        <label class="mb-1">Metode Pembayaran <span class="text-danger">*</span></label>
                                        <select name="payment_method" class="form-control" x-model="pm">
                                            <option value="Tunai">Tunai</option>
                                            <option value="Transfer">Transfer</option>
                                            <option value="QRIS">QRIS</option>
                                        </select>

                                        <div class="mt-2" x-show="pm === 'Transfer'">
                                            <label class="mb-1">Bank / Rekening</label>
                                            <input type="text" name="bank_name" class="form-control"
                                                value="{{ old('bank_name', $sale->bank_name) }}"
                                                placeholder="BCA a.n. ..." :disabled="pm !== 'Transfer'"
                                                x-effect="if(pm !== 'Transfer'){ $el.value = '' }">
                                            <small class="text-muted">Akan muncul pada catatan pembayaran.</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Ongkir, Pajak (%), Diskon (%) sesuai field header sale --}}
                                <div class="col-lg-4">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="shipping_amount">Biaya Kirim</label>
                                                @php $shipValue = $normalize100($sale->shipping_amount ?? 0); @endphp
                                                <input type="text" id="shipping_amount" name="shipping_amount"
                                                    class="form-control js-money"
                                                    value="{{ number_format((int) $shipValue, 0, ',', '.') }}"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="tax_percentage">Pajak (%)</label>
                                                <input type="number" id="tax_percentage" name="tax_percentage"
                                                    class="form-control" value="{{ (int) $sale->tax_percentage }}"
                                                    min="0" max="100" step="1">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="discount_percentage">Diskon (%)</label>
                                                <input type="number" id="discount_percentage" name="discount_percentage"
                                                    class="form-control" value="{{ (int) $sale->discount_percentage }}"
                                                    min="0" max="100" step="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                // Catatan: gunakan aggregate header paid_amount agar konsisten
                                $existingPaid = (int) ($sale->paid_amount ?? 0);
                            @endphp

                            {{-- ===== Panel Ringkasan ===== --}}
                            <div class="row">
                                <div class="col-lg-4 offset-lg-8">
                                    <div class="form-group">
                                        <label for="paid_amount">Target Total Dibayar (setelah edit)</label>
                                        <input type="text" name="paid_amount" id="paid_amount"
                                            class="form-control js-money"
                                            value="{{ number_format($existingPaid, 0, ',', '.') }}"
                                            placeholder="mis. 1.500.000">
                                        <small class="text-muted">
                                            Isi bila ingin menambah/mengurangi pembayaran (refund). Kosongkan = biarkan
                                            total pembayaran saat ini.
                                        </small>
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
                                    })" x-init="init()" class="border rounded p-2 mt-2"
                                        wire:ignore>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong>Preview Status Baru</strong>
                                            <span class="badge"
                                                :class="{ 'badge-success': status === 'Paid', 'badge-warning': status === 'Partial', 'badge-secondary': status === 'Unpaid' }"
                                                x-text="status"></span>
                                        </div>

                                        <div class="small text-muted mb-2">
                                            Sistem menghitung ulang total saat kamu mengubah harga/qty/diskon/pajak/ongkir
                                            atau target pembayaran.
                                        </div>

                                        <ul class="list-unstyled mb-2" style="font-size:.875rem">
                                            <li>Subtotal item: <strong><span x-text="formatIDR(subtotal)"></span></strong>
                                            </li>
                                            <li>Pajak (× <span x-text="taxPct"></span>%): <strong><span
                                                        x-text="formatIDR(taxAmt)"></span></strong></li>
                                            <li>Diskon (× <span x-text="discPct"></span>%): <strong><span
                                                        x-text="formatIDR(discAmt)"></span></strong></li>
                                            <li>Ongkir: <strong><span x-text="formatIDR(shipping)"></span></strong></li>
                                            <li class="mt-1">Grand Total Baru: <strong><span
                                                        x-text="formatIDR(grandTotal)"></span></strong></li>
                                            <li>Target Total Dibayar: <strong><span
                                                        x-text="formatIDR(targetPaid)"></span></strong></li>
                                            <li>Selisih Target vs Grand Total: <strong><span
                                                        x-text="formatIDR(targetPaid - grandTotal)"></span></strong></li>
                                        </ul>

                                        <template x-if="warnTotalVsPaid">
                                            <div class="alert alert-warning py-1 px-2">
                                                <small><strong>Perhatian:</strong> Grand total berbeda jauh dari total
                                                    pembayaran yang sudah ada
                                                    (deviasi > <span x-text="formatIDR(WARN_ABS)"></span> atau > <span
                                                        x-text="WARN_PCT"></span>% dari pembayaran saat ini).
                                                    Pertimbangkan koreksi pembayaran (tambah/refund).</small>
                                            </div>
                                        </template>

                                        <template x-if="warnDeltaPaid">
                                            <div class="alert alert-info py-1 px-2">
                                                <small><strong>Info:</strong> Kamu mengubah target pembayaran sebesar
                                                    <span x-text="formatIDR(targetPaid - existingPaid)"></span>. Sistem
                                                    akan mencatatnya sebagai
                                                    <em>SalePayment</em> penyesuaian (+ untuk tambahan, − untuk
                                                    refund).</small>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            {{-- ===== /Panel Ringkasan ===== --}}

                            <div class="form-group mt-3">
                                <label for="note">Catatan</label>
                                <textarea name="note" id="note" rows="5" class="form-control">{{ $sale->note }}</textarea>
                            </div>

                            <div class="mt-3 d-flex">
                                <a href="{{ url()->previous() ?: route('sales.index') }}"
                                    class="btn btn-light mr-2">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    Simpan Perubahan <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    {{-- AutoNumeric (format uang) --}}
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

            // Konfirmasi submit + unmask AN + cegah double submit
            const form = document.getElementById('sale-form');
            form?.addEventListener('submit', async (ev) => {
                ev.preventDefault();
                const ok = await Swal.fire({
                    icon: 'question',
                    title: 'Simpan perubahan?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal'
                }).then(r => r.isConfirmed);
                if (!ok) return;

                const btnSubmit = form.querySelector('button[type="submit"]');
                btnSubmit?.setAttribute('disabled', 'disabled');

                ['paid_amount', 'shipping_amount'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el && el._an) el.value = el._an.getNumber();
                    else if (el) el.value = stripMoney(el.value);
                });

                form.submit();
            });
        });

        // Flash swal (opsional)
        @if (session('swal-success'))
            window.addEventListener('load', () => {
                Swal.fire({
                    icon: 'success',
                    title: @json(session('swal-success')),
                    timer: 1600,
                    showConfirmButton: false
                });
            });
        @endif
        @if (session('swal-error'))
            window.addEventListener('load', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json(session('swal-error'))
                });
            });
        @endif
    </script>
@endpush
