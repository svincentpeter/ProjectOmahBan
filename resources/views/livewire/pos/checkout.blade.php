<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            @if($sale)
                {{-- ================== SETELAH INVOICE DIBUAT ================== --}}
                <div class="alert alert-success">
                    Invoice <strong>{{ $sale->reference }}</strong> berhasil dibuat! Silakan proses pembayaran.
                </div>

                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                        @foreach($sale_details as $detail)
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

                <hr>
                <h4 class="text-center">Total: {{ format_currency($sale->total_amount) }}</h4>
                <hr>

                @if($sale->payment_status != 'Paid')

                    {{-- ====== JUMLAH DIBAYAR (AutoNumeric + Livewire hidden binding) ====== --}}
                    <div class="form-group">
                        <label for="paid_amount_view">Jumlah Dibayar</label>

                        {{-- Input tampilan yang diformat (tidak di-wire:model) --}}
                        <div wire:ignore>
                            <input
                                type="text"
                                class="form-control"
                                id="paid_amount_view"
                                inputmode="numeric"
                                placeholder="0"
                                autocomplete="off"
                                value="{{ number_format((int)($paid_amount ?? 0), 0, ',', '.') }}"
                            >
                        </div>

                        {{-- Input hidden yang terhubung ke komponen Livewire --}}
                        <input type="hidden" id="paid_amount_hidden" wire:model.live.debounce.300ms="paid_amount">

                        @error('paid_amount') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- ====== KEMBALIAN (Tunai) ====== --}}
                    @if($payment_method === 'Tunai')
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <strong>Kembalian</strong>
                            <strong>{{ format_currency($change) }}</strong>
                        </div>
                    @endif

                    <button
                        wire:click="markAsPaid"
                        wire:loading.attr="disabled"
                        class="btn btn-primary btn-block"
                    >
                        <span wire:loading wire:target="markAsPaid"
                              class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Tandai Lunas
                    </button>
                @endif

                @if($sale->payment_status == 'Paid')
                    <div class="text-center text-success mb-3">
                        <strong><i class="bi bi-check-circle"></i> LUNAS</strong>
                    </div>
                    <a href="{{ route('sales.pos.pdf', $sale->id) }}" target="_blank" class="btn btn-info btn-block">
                        <i class="bi bi-printer"></i> Cetak Struk
                    </a>
                @endif

                <button wire:click="newTransaction" class="btn btn-secondary btn-block mt-2">
                    Transaksi Baru
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
                                <td class="align-middle">{{ $cart_item->name }}</td>
                                <td class="align-middle text-center">{{ format_currency($cart_item->price) }}</td>

                                <td class="align-middle">
                                    @if(in_array($cart_item->options->source_type, ['new', 'manual']))
                                        <div class="input-group">
                                            <input
                                                wire:model.live.debounce.500ms="quantity.{{ $cart_item->id }}"
                                                style="min-width: 40px;"
                                                type="number"
                                                class="form-control text-center"
                                                value="{{ $cart_item->qty }}"
                                                min="1"
                                            >
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

                                <td class="align-middle text-center">{{ format_currency($cart_item->subtotal) }}</td>
                                <td class="align-middle text-center">
                                    <a href="#" wire:click.prevent="removeItem('{{ $cart_item->rowId }}')">
                                        <i class="bi bi-x-circle text-danger"></i>
                                    </a>
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

                {{-- Metode Pembayaran (diterapkan saat buat invoice) --}}
                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <div class="d-flex flex-wrap">
                        <div class="form-check form-check-inline">
                            <input wire:model.live="payment_method" class="form-check-input" type="radio"
                                   id="payment_tunai" value="Tunai">
                            <label class="form-check-label" for="payment_tunai">Tunai</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input wire:model.live="payment_method" class="form-check-input" type="radio"
                                   id="payment_transfer" value="Transfer">
                            <label class="form-check-label" for="payment_transfer">Transfer</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input wire:model.live="payment_method" class="form-check-input" type="radio"
                                   id="payment_kredit" value="Kredit">
                            <label class="form-check-label" for="payment_kredit">Kredit</label>
                        </div>
                    </div>
                </div>

                @if ($payment_method !== 'Tunai')
                    <div class="form-group mt-2">
                        <label for="bank_name">Nama Bank <span class="text-danger">*</span></label>
                        <input wire:model.defer="bank_name" type="text" id="bank_name" class="form-control"
                               placeholder="Contoh: BCA, Mandiri, dll.">
                        @error('bank_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Subtotal</th>
                            <td>{{ format_currency(Cart::instance('sale')->subtotal()) }}</td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <td><strong>{{ format_currency($total_amount) }}</strong></td>
                        </tr>
                    </table>
                </div>

                <button
                    wire:click="createInvoice"
                    wire:loading.attr="disabled"
                    class="btn btn-primary btn-block"
                    {{ Cart::instance('sale')->count() == 0 ? 'disabled' : '' }}
                >
                    <span wire:loading wire:target="createInvoice"
                          class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Buat Invoice
                </button>
            @endif
        </div>
    </div>
</div>

@push('page_scripts')
    {{-- AutoNumeric untuk formatting rupiah --}}
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
    <script>
        (function initPaidAmountAN() {
            const boot = () => {
                const view   = document.getElementById('paid_amount_view');
                const hidden = document.getElementById('paid_amount_hidden'); // <- diperbaiki (ID benar)

                if (!view || !hidden) return;

                // Kalau sudah pernah di-init, cukup sinkronkan lagi
                if (!view._an) {
                    view._an = new AutoNumeric(view, {
                        digitGroupSeparator: '.',
                        decimalCharacter: ',',
                        decimalPlaces: 0,
                        unformatOnSubmit: true,
                        modifyValueOnWheel: false,
                        emptyInputBehavior: 'zero',
                        minimumValue: '0'
                    });

                    // set nilai awal dari Livewire ke tampilan
                    const initial = parseInt(hidden.value || '0', 10);
                    if (initial > 0) view._an.set(initial);

                    const sync = () => {
                        try {
                            const numberStr = view._an.getNumber(); // string angka murni
                            const numberVal = numberStr ? parseInt(numberStr, 10) : 0;

                            // isi hidden agar wire:model kepikup
                            hidden.value = numberVal;

                            // set langsung ke properti Livewire di komponen ini
                            if (window.Livewire) {
                                const root = view.closest('[wire\\:id]');
                                if (root) {
                                    const comp = Livewire.find(root.getAttribute('wire:id'));
                                    if (comp) comp.set('paid_amount', numberVal);
                                }
                            }
                        } catch (e) {
                            // no-op
                        }
                    };

                    view.addEventListener('input', sync);
                    view.addEventListener('change', sync);

                    // sinkron awal
                    sync();
                } else {
                    // jika sudah ada, pastikan tampilan selaras dgn hidden (Livewire)
                    const current = parseInt(hidden.value || '0', 10);
                    view._an.set(current);
                }
            };

            // Inisialisasi awal
            document.addEventListener('DOMContentLoaded', boot);

            // Re-init setiap Livewire morph (v3)
            if (window.Livewire && Livewire.hook) {
                Livewire.hook('morph.updated', boot);
            }

            // Fallback saat navigasi internal Livewire
            document.addEventListener('livewire:navigated', boot);
        })();
    </script>
@endpush
