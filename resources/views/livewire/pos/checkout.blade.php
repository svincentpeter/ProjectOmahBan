<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            @if ($sale)
                {{-- ================== SETELAH INVOICE DIBUAT ================== --}}
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    Invoice <strong>{{ $sale->reference }}</strong> berhasil dibuat!
                </div>

                {{-- 👉 Customer Name (jika ada) --}}
                @if (!empty($sale->customer_name))
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="bi bi-person-badge-fill mr-2" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>Customer:</strong> {{ $sale->customer_name }}
                        </div>
                    </div>
                @endif

                {{-- Detail Items --}}
                <div class="table-responsive">
                    <table class="table table-sm mb-2">
                        <tbody>
                            @foreach ($sale_details as $detail)
                                <tr>
                                    <td>{{ $detail->product_name }}</td>
                                    <td class="text-right">
                                        {{ $detail->quantity }} x {{ format_currency($detail->price) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr class="my-3">
                <h4 class="text-center">Total: {{ format_currency($sale->total_amount) }}</h4>
                <hr class="my-3">

                {{-- Tombol Cetak selalu tersedia --}}
                <a href="{{ route('sales.pos.pdf', $sale->id) }}" target="_blank"
                    class="btn btn-primary btn-block mb-3">
                    <i class="bi bi-printer"></i> Cetak Invoice
                </a>

                {{-- Status Pembayaran --}}
                @if ($sale->payment_status === 'Paid')
                    <div class="alert alert-success text-center mb-3">
                        <i class="bi bi-check-circle-fill"></i> <strong>TRANSAKSI TELAH LUNAS</strong>
                    </div>
                @else
                    <div class="alert alert-info text-center mb-3">
                        <i class="bi bi-info-circle-fill"></i> <strong>Menunggu Pembayaran</strong>
                    </div>

                    {{-- ====== TOMBOL LANJUT KE PEMBAYARAN ====== --}}
                    @if (!$show_payment)
                        <button wire:click="showPayment" class="btn btn-success btn-block btn-lg mb-2">
                            <i class="bi bi-cash-stack"></i> Lanjut ke Pembayaran
                        </button>
                    @endif

                    {{-- ====== FORM PEMBAYARAN ====== --}}
                    @if ($show_payment)
                        <hr class="my-3">

                        <form wire:submit.prevent="markAsPaid" novalidate>
                            {{-- Metode Pembayaran --}}
                            <div class="form-group">
                                <label class="d-block font-weight-bold mb-2">Metode Pembayaran</label>

                                <fieldset>
                                    <legend class="sr-only">Pilih metode pembayaran</legend>

                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="payCash" name="payment_method"
                                            class="custom-control-input" wire:model.live="payment_method"
                                            wire:change="onPaymentMethodChange" value="Tunai" wire:key="pm-tunai">
                                        <label class="custom-control-label" for="payCash">Tunai</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="payTransfer" name="payment_method"
                                            class="custom-control-input" wire:model.live="payment_method"
                                            wire:change="onPaymentMethodChange" value="Transfer" wire:key="pm-transfer">
                                        <label class="custom-control-label" for="payTransfer">Transfer</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="payQRIS" name="payment_method"
                                            class="custom-control-input" wire:model.live="payment_method"
                                            wire:change="onPaymentMethodChange" value="QRIS" wire:key="pm-qris">
                                        <label class="custom-control-label" for="payQRIS">QRIS</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="payMidtrans" name="payment_method"
                                            class="custom-control-input" wire:model.live="payment_method"
                                            wire:change="onPaymentMethodChange" value="Midtrans" wire:key="pm-midtrans">
                                        <label class="custom-control-label" for="payMidtrans">Midtrans (Digital
                                            Payment)</label>
                                    </div>
                                </fieldset>

                                @error('payment_method')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- === BLOK KHUSUS MIDTRANS === --}}
                            @if ($payment_method === 'Midtrans')
                                <div wire:key="midtrans-section">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i>
                                        <strong>Pembayaran Digital via Midtrans</strong><br>
                                        Anda dapat membayar menggunakan GoPay, ShopeePay, QRIS, Virtual Account, atau
                                        Kartu Kredit.
                                    </div>

                                    <button type="button" wire:click="generateMidtransToken"
                                        class="btn btn-success btn-lg btn-block mb-3" wire:loading.attr="disabled">
                                        <span wire:loading wire:target="generateMidtransToken"
                                            class="spinner-border spinner-border-sm" role="status"></span>
                                        <i class="bi bi-credit-card"></i> Bayar dengan Midtrans
                                    </button>

                                    @if ($show_midtrans_payment && $midtrans_snap_token)
                                        <button type="button" id="pay-button-midtrans"
                                            class="btn btn-primary btn-lg btn-block">
                                            <i class="bi bi-wallet"></i> Buka Ulang Pembayaran Midtrans
                                        </button>
                                    @endif
                                </div>
                            @else
                                {{-- === BLOK PEMBAYARAN MANUAL (Tunai/Transfer/QRIS) === --}}
                                <div wire:key="manual-section">
                                    {{-- Nama bank (khusus Transfer) --}}
                                    @if ($payment_method === 'Transfer')
                                        <div class="form-group">
                                            <label>Nama Bank</label>
                                            <input type="text" class="form-control" wire:model.live="bank_name"
                                                placeholder="Mandiri / BCA / BRI ...">
                                            @error('bank_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    @endif

                                    {{-- Jumlah Dibayar (AutoNumeric + Alpine entangle) --}}
                                    <div class="form-group" x-data="paidBox(@entangle('paid_amount').live)" x-init="init()">
                                        <label for="paid_amount_view">Jumlah Dibayar</label>
                                        <div wire:ignore>
                                            <input type="text" class="form-control" id="paid_amount_view"
                                                inputmode="numeric" placeholder="0" autocomplete="off"
                                                value="{{ number_format((int) ($paid_amount ?? 0), 0, ',', '.') }}">
                                        </div>
                                        @error('paid_amount')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- Ringkasan bayar --}}
                                    @php
                                        $total = (int) ($sale->total_amount ?? 0);
                                        $paid = (int) ($paid_amount ?? 0);
                                        $diff = $paid - $total;
                                    @endphp

                                    <div class="alert {{ $diff >= 0 ? 'alert-primary' : 'alert-warning' }} d-flex justify-content-between align-items-center"
                                        aria-live="polite">
                                        <strong>{{ $diff >= 0 ? 'Kembalian' : 'Sisa yang harus dibayar' }}</strong>
                                        <span>{{ format_currency(abs($diff)) }}</span>
                                    </div>

                                    {{-- Tombol Tandai Lunas --}}
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="btn btn-success btn-block" @disabled(($paid_amount ?? 0) < (int) ($sale->total_amount ?? 0))
                                        title="Jumlah dibayar harus ≥ total">
                                        <span wire:loading wire:target="markAsPaid"
                                            class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        <i class="bi bi-check-circle"></i> Tandai Lunas
                                    </button>
                                </div>
                            @endif
                        </form>
                    @endif
                @endif

                {{-- Transaksi baru --}}
                <button wire:click="newTransaction" class="btn btn-secondary btn-block mt-3">
                    <i class="bi bi-arrow-clockwise"></i> Transaksi Baru
                </button>
            @else
                {{-- ================== TAMPILAN STANDAR KASIR (sebelum invoice) ================== --}}
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th class="align-middle">Produk</th>
                                <th class="align-middle">Harga</th>
                                <th class="align-middle" style="width: 130px;">Jumlah</th>
                                <th class="align-middle">Subtotal</th>
                                <th class="align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cart_items as $cart_item)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center gap-2">
                                            <span>{{ $cart_item->name }}</span>

                                            @php
                                                $st = $cart_item->options->source_type ?? 'new';

                                                // ✅ FIXED: Baca source_type_label dari options
                                                $label = $cart_item->options->source_type_label ?? null;

                                                // Fallback jika tidak ada label
                                                if (!$label) {
                                                    if ($st === 'new') {
                                                        $label = 'Baru';
                                                        $class = 'badge-primary';
                                                    } elseif ($st === 'second') {
                                                        $label = 'Bekas';
                                                        $class = 'badge-warning';
                                                    } elseif ($st === 'manual') {
                                                        // Check manual_kind
                                                        $mk = $cart_item->options->manual_kind ?? 'service';
                                                        $label = $mk === 'service' ? 'Jasa' : 'Barang';
                                                        $class = $mk === 'service' ? 'badge-info' : 'badge-success';
                                                    } else {
                                                        $label = 'Lainnya';
                                                        $class = 'badge-secondary';
                                                    }
                                                } else {
                                                    // Set class based on label
                                                    $class = match ($label) {
                                                        'Baru' => 'badge-primary',
                                                        'Bekas' => 'badge-warning',
                                                        'Jasa' => 'badge-info',
                                                        'Barang' => 'badge-success',
                                                        default => 'badge-secondary',
                                                    };
                                                }
                                            @endphp

                                            <span class="badge {{ $class }}" title="Tipe sumber item">
                                                {{ $label }}
                                            </span>

                                            @if (!empty($cart_item->options->code) && $cart_item->options->code !== '-')
                                                <span class="badge badge-light">{{ $cart_item->options->code }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="align-middle text-center">{{ format_currency($cart_item->price) }}</td>

                                    <td class="align-middle">
                                        @if (in_array($cart_item->options->source_type, ['new', 'manual']))
                                            <div class="input-group">
                                                <input wire:model.live.debounce.500ms="quantity.{{ $cart_item->id }}"
                                                    style="min-width: 40px;" type="number"
                                                    class="form-control text-center" value="{{ $cart_item->qty }}"
                                                    min="1">
                                                <div class="input-group-append">
                                                    <button type="button"
                                                        wire:click="updateQuantity('{{ $cart_item->rowId }}', '{{ $cart_item->id }}', '{{ $cart_item->options->source_type }}')"
                                                        class="btn btn-info">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Produk bekas (second) read-only --}}
                                            <input type="number" class="form-control text-center"
                                                value="{{ $cart_item->qty }}" readonly>
                                        @endif
                                    </td>

                                    <td class="align-middle text-center">{{ format_currency($cart_item->subtotal) }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{-- ✅ FIXED: Tambah wire:loading.attr dan wire:target untuk prevent double click --}}
                                        <button type="button" wire:click="removeItem('{{ $cart_item->rowId }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="removeItem('{{ $cart_item->rowId }}')"
                                            class="btn btn-sm btn-link text-danger p-0" title="Hapus item"
                                            style="border: none; background: none;">
                                            <i class="bi bi-x-circle" style="font-size: 1.5rem;" wire:loading.remove
                                                wire:target="removeItem('{{ $cart_item->rowId }}')"></i>
                                            <span wire:loading wire:target="removeItem('{{ $cart_item->rowId }}')"
                                                class="spinner-border spinner-border-sm text-danger"
                                                role="status"></span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-danger">Keranjang Kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr>

                {{-- Total ringkas --}}
                <div class="table-responsive">
                    <table class="table table-striped mb-3">
                        <tr>
                            <th>Subtotal</th>
                            <td>{{ format_currency(Cart::instance($cart_instance)->subtotal()) }}</td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <td><strong>{{ format_currency($total_amount) }}</strong></td>
                        </tr>
                    </table>
                </div>

                {{-- Input Customer Name (Opsional) --}}
                <div class="form-group mb-3">
                    <label for="customer_name" class="form-label">
                        <i class="bi bi-person-fill"></i> Nama Customer (Opsional)
                    </label>
                    <input type="text" id="customer_name" class="form-control" wire:model.defer="customer_name"
                        placeholder="Kosongkan jika tidak perlu" maxlength="255">
                    <small class="form-text text-muted">
                        💡 Isi hanya jika customer meminta namanya dicantumkan di invoice
                    </small>
                    @error('customer_name')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <button wire:click="createInvoice" wire:loading.attr="disabled" class="btn btn-primary btn-block"
                    {{ Cart::instance($cart_instance)->count() == 0 ? 'disabled' : '' }}>
                    <span wire:loading wire:target="createInvoice" class="spinner-border spinner-border-sm"
                        role="status" aria-hidden="true"></span>
                    Buat Invoice
                </button>
            @endif
        </div>
    </div>
</div>

@once
    @push('page_scripts')
        {{-- AutoNumeric untuk input jumlah bayar --}}
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>

        {{-- MIDTRANS SNAP.JS (sandbox/production sesuai config) --}}
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
        </script>

        <script>
            function paidBox(entangled) {
                return {
                    an: null,
                    paid: entangled,
                    init() {
                        const el = this.$root.querySelector('#paid_amount_view') || document.getElementById('paid_amount_view');
                        if (!el) return;

                        const toInt = (s) => (s == null) ? 0 : (parseInt(String(s).replace(/[^\d-]/g, ''), 10) || 0);

                        if (window.AutoNumeric) {
                            this.an = new AutoNumeric(el, {
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                decimalPlaces: 0,
                                unformatOnSubmit: true,
                                modifyValueOnWheel: false,
                            });

                            this.$nextTick(() => this.an.set(Number(this.paid || 0) || 0));

                            const push = () => {
                                const raw = this.an.getNumber();
                                const v = raw ? parseInt(raw, 10) : 0;
                                if (this.paid !== v) this.paid = v;
                            };
                            el.addEventListener('autoNumeric:rawValueModified', push);
                            el.addEventListener('change', push);
                        } else {
                            // Fallback tanpa AutoNumeric
                            const push = () => {
                                const v = toInt(el.value);
                                if (this.paid !== v) this.paid = v;
                            };
                            el.addEventListener('input', push);
                            el.addEventListener('change', push);
                            this.$nextTick(() => {
                                const n = Number(this.paid || 0);
                                el.value = n ? n.toLocaleString('id-ID') : '';
                            });
                        }

                        // sinkron Livewire -> input
                        this.$watch('paid', (v) => {
                            const n = Number(v || 0);
                            if (this.an) {
                                if (String(n) !== this.an.getNumber()) this.an.set(isNaN(n) ? 0 : n);
                            } else {
                                el.value = n ? n.toLocaleString('id-ID') : '';
                            }
                        });
                    }
                }
            }

            // Livewire events
            document.addEventListener('livewire:initialized', () => {
                // Buka popup SNAP ketika token ada
                Livewire.on('open-midtrans-snap', (data) => {
                    const snapToken = data?.[0]?.token || null;

                    if (!snapToken || typeof snap === 'undefined') {
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Token pembayaran tidak valid'
                            });
                        } else {
                            alert('Token pembayaran tidak valid');
                        }
                        return;
                    }

                    snap.pay(snapToken, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            if (window.Swal) Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: 'Transaksi Anda telah diproses',
                                timer: 3000,
                                showConfirmButton: false
                            });
                            @this.checkMidtransStatus();
                            setTimeout(() => window.location.reload(), 3000);
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            if (window.Swal) Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                text: 'Silakan selesaikan pembayaran Anda'
                            });
                            @this.checkMidtransStatus();
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            if (window.Swal) Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: 'Terjadi kesalahan dalam proses pembayaran'
                            });
                        },
                        onClose: function() {
                            console.log('Customer closed the popup');
                            if (window.Swal) Swal.fire({
                                icon: 'warning',
                                title: 'Pembayaran Dibatalkan',
                                text: 'Anda menutup jendela pembayaran'
                            });
                        }
                    });
                });

                // Re-open SNAP via button
                document.addEventListener('click', function(e) {
                    if (e.target && e.target.id === 'pay-button-midtrans') {
                        const token = @json($midtrans_snap_token ?? null);
                        if (token && typeof snap !== 'undefined') {
                            snap.pay(token);
                        } else {
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Token pembayaran tidak ditemukan'
                                });
                            } else {
                                alert('Token pembayaran tidak ditemukan');
                            }
                        }
                    }
                });

                // Re-init AutoNumeric aman (idempoten)
                Livewire.on('paid-input-ready', () => {
                    const el = document.getElementById('paid_amount_view');
                    if (el && !el.dataset.anReady) {
                        el.dataset.anReady = '1';
                    }
                });
            });
        </script>
    @endpush
@endonce
