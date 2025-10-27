<div>
    {{-- Main Card --}}
    <div class="card border-0" style="box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px; overflow: hidden;">
        <div class="card-body" style="padding: 1rem;">
            @if ($sale)
                {{-- ========== SETELAH INVOICE DIBUAT ========== --}}
                <div class="alert alert-success"
                    style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: none; border-left: 5px solid #28a745; border-radius: 10px; padding: 1.0rem;">
                    <i class="bi bi-check-circle-fill" style="font-size: 1.0rem; margin-right: 0.5rem;"></i>
                    Invoice <strong style="font-size: 1.0rem;">{{ $sale->reference }}</strong> berhasil dibuat!
                </div>

                {{-- Customer Name --}}
                @if (!empty($sale->customer_name))
                    <div class="alert alert-info"
                        style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border: none; border-left: 5px solid #17a2b8; border-radius: 10px; padding: 1rem; display: flex; align-items: center; margin-bottom: 1.0rem;">
                        <i class="bi bi-person-badge-fill"
                            style="font-size: 1rem; margin-right: 1rem; color: #17a2b8;"></i>
                        <div>
                            <strong style="font-size: 1rem;">Customer:</strong>
                            <span style="font-size: 1rem; font-weight: 600;">{{ $sale->customer_name }}</span>
                        </div>
                    </div>
                @endif

                {{-- Detail Items --}}
                <div class="table-responsive" style="margin-bottom: 1.5rem;">
                    <table class="table" style="margin-bottom: 0;">
                        <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <tr>
                                <th style="color: white; padding: 1rem; font-weight: 600;">Produk</th>
                                <th class="text-right" style="color: white; padding: 1rem; font-weight: 600;">Detail
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale_details as $detail)
                                <tr style="border-bottom: 1px solid #e9ecef; transition: background 0.3s;">
                                    <td style="padding: 1rem; font-weight: 500;">{{ $detail->product_name }}</td>
                                    <td class="text-right" style="padding: 1rem;">
                                        <span style="color: #6c757d;">{{ $detail->quantity }} x</span>
                                        <strong style="color: #28a745; font-size: 1.05rem;">
                                            {{ format_currency($detail->price) }}
                                        </strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr style="border-top: 2px solid #e9ecef; margin: 1.5rem 0;">

                <div
                    style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 1.5rem; border-radius: 12px; text-align: center; margin-bottom: 1.5rem; border-left: 5px solid #667eea;">
                    <h4 style="margin: 0; font-weight: 700; font-size: 1.6rem; color: #2c3e50;">
                        Total: <span style="color: #667eea;">{{ format_currency($sale->total_amount) }}</span>
                    </h4>
                </div>

                <hr style="border-top: 2px solid #e9ecef; margin: 1.5rem 0;">

                {{-- Tombol Cetak --}}
                <a href="{{ route('sales.pos.pdf', $sale->id) }}" target="_blank" class="btn btn-primary btn-block"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 0.6rem; font-size: 0.95rem; font-weight: 600; border-radius: 8px; margin-bottom: 1rem; transition: all 0.3s; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.5)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)'">
                    <i class="bi bi-printer"></i> Cetak Invoice
                </a>

                {{-- Status Pembayaran --}}
                @if ($sale->payment_status === 'Paid')
                    <div class="alert alert-success text-center"
                        style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; font-size: 1.1rem; font-weight: 700;">
                        <i class="bi bi-check-circle-fill" style="font-size: 1.5rem; margin-right: 0.5rem;"></i>
                        TRANSAKSI TELAH LUNAS
                    </div>
                @else
                    <div class="alert alert-info text-center"
                        style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; font-size: 1rem; font-weight: 600;">
                        <i class="bi bi-info-circle-fill" style="font-size: 1.3rem; margin-right: 0.5rem;"></i>
                        Menunggu Pembayaran
                    </div>

                    {{-- Tombol Lanjut ke Pembayaran --}}
                    @if (!$show_payment)
                        <button wire:click="showPayment" class="btn btn-success btn-block"
                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; padding: 0.6rem; font-size: 0.95rem; font-weight: 600; border-radius: 8px; margin-bottom: 0.8rem; transition: all 0.3s; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(40, 167, 69, 0.5)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(40, 167, 69, 0.3)'">
                            <i class="bi bi-cash-stack"></i> Lanjut ke Pembayaran
                        </button>
                    @endif

                    {{-- FORM PEMBAYARAN --}}
                    @if ($show_payment)
                        <hr style="border-top: 2px solid #e9ecef; margin: 1.5rem 0;">

                        <div
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); padding: 1.5rem; border-radius: 10px; border: 2px solid #667eea;">
                            <h5 style="margin-bottom: 1rem; font-weight: 700; color: #667eea; font-size: 1.1rem;">
                                <i class="bi bi-wallet" style="margin-right: 0.5rem;"></i>
                                Pilih Metode Pembayaran
                            </h5>

                            <form wire:submit.prevent="markAsPaid" novalidate>
                                {{-- Metode Pembayaran --}}
                                <div class="form-group">
                                    <fieldset>
                                        <legend class="sr-only">Pilih metode pembayaran</legend>

                                        <div class="custom-control custom-radio"
                                            style="padding: 0.6rem; margin-bottom: 0.6rem; border: 2px solid #e9ecef; border-radius: 8px; transition: all 0.3s; cursor: pointer;"
                                            onmouseover="this.style.borderColor='#667eea'; this.style.background='rgba(102, 126, 234, 0.05)'"
                                            onmouseout="this.style.borderColor='#e9ecef'; this.style.background='white'">
                                            <input type="radio" id="payCash" name="payment_method"
                                                class="custom-control-input" wire:model.live="payment_method"
                                                wire:change="onPaymentMethodChange" value="Tunai" wire:key="pm-tunai">
                                            <label class="custom-control-label" for="payCash"
                                                style="font-size: 0.95rem; font-weight: 500; cursor: pointer;">
                                                💵 Tunai
                                            </label>
                                        </div>

                                        <div class="custom-control custom-radio"
                                            style="padding: 0.6rem; margin-bottom: 0.6rem; border: 2px solid #e9ecef; border-radius: 8px; transition: all 0.3s; cursor: pointer;"
                                            onmouseover="this.style.borderColor='#667eea'; this.style.background='rgba(102, 126, 234, 0.05)'"
                                            onmouseout="this.style.borderColor='#e9ecef'; this.style.background='white'">
                                            <input type="radio" id="payTransfer" name="payment_method"
                                                class="custom-control-input" wire:model.live="payment_method"
                                                wire:change="onPaymentMethodChange" value="Transfer"
                                                wire:key="pm-transfer">
                                            <label class="custom-control-label" for="payTransfer"
                                                style="font-size: 0.95rem; font-weight: 500; cursor: pointer;">
                                                🏦 Transfer Bank
                                            </label>
                                        </div>

                                        <div class="custom-control custom-radio"
                                            style="padding: 0.6rem; margin-bottom: 0.6rem; border: 2px solid #e9ecef; border-radius: 8px; transition: all 0.3s; cursor: pointer;"
                                            onmouseover="this.style.borderColor='#667eea'; this.style.background='rgba(102, 126, 234, 0.05)'"
                                            onmouseout="this.style.borderColor='#e9ecef'; this.style.background='white'">
                                            <input type="radio" id="payQRIS" name="payment_method"
                                                class="custom-control-input" wire:model.live="payment_method"
                                                wire:change="onPaymentMethodChange" value="QRIS"
                                                wire:key="pm-qris">
                                            <label class="custom-control-label" for="payQRIS"
                                                style="font-size: 0.95rem; font-weight: 500; cursor: pointer;">
                                                📱 QRIS
                                            </label>
                                        </div>

                                        <div class="custom-control custom-radio"
                                            style="padding: 0.6rem; margin-bottom: 0.6rem; border: 2px solid #e9ecef; border-radius: 8px; transition: all 0.3s; cursor: pointer;"
                                            onmouseover="this.style.borderColor='#667eea'; this.style.background='rgba(102, 126, 234, 0.05)'"
                                            onmouseout="this.style.borderColor='#e9ecef'; this.style.background='white'">
                                            <input type="radio" id="payMidtrans" name="payment_method"
                                                class="custom-control-input" wire:model.live="payment_method"
                                                wire:change="onPaymentMethodChange" value="Midtrans"
                                                wire:key="pm-midtrans">
                                            <label class="custom-control-label" for="payMidtrans"
                                                style="font-size: 0.95rem; font-weight: 500; cursor: pointer;">
                                                💳 Midtrans (Digital Payment)
                                            </label>
                                        </div>
                                    </fieldset>

                                    @error('payment_method')
                                        <small class="text-danger"
                                            style="display: block; margin-top: 0.5rem; font-size: 0.9rem;">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- MIDTRANS SECTION --}}
                                @if ($payment_method === 'Midtrans')
                                    <div wire:key="midtrans-section">
                                        <div class="alert alert-info"
                                            style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border: none; border-left: 4px solid #17a2b8; border-radius: 8px; padding: 0.8rem; margin-bottom: 1rem;">
                                            <i class="bi bi-info-circle"
                                                style="font-size: 1.2rem; margin-right: 0.5rem;"></i>
                                            <strong style="font-size: 0.95rem;">Pembayaran Digital via
                                                Midtrans</strong><br>
                                            <span style="font-size: 0.85rem;">Anda dapat membayar menggunakan GoPay,
                                                ShopeePay, QRIS, Virtual Account, atau Kartu Kredit.</span>
                                        </div>

                                        {{-- Button Generate Midtrans Token --}}
                                        <button type="button" wire:click="generateMidtransToken"
                                            class="btn btn-success btn-block"
                                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; padding: 0.6rem; font-size: 0.95rem; font-weight: 600; border-radius: 8px; margin-bottom: 0.8rem; transition: all 0.3s;"
                                            wire:loading.attr="disabled">
                                            <span wire:loading wire:target="generateMidtransToken"
                                                class="spinner-border spinner-border-sm" role="status"></span>
                                            <i class="bi bi-credit-card"></i> Bayar dengan Midtrans
                                        </button>

                                        @if ($show_midtrans_payment && $midtrans_snap_token)
                                            <button type="button" id="pay-button-midtrans"
                                                class="btn btn-primary btn-block"
                                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 0.6rem; font-size: 0.95rem; font-weight: 600; border-radius: 8px; margin-bottom: 0.8rem;">
                                                <i class="bi bi-wallet"></i> Buka Ulang Pembayaran Midtrans
                                            </button>
                                        @endif

                                        <hr style="border-top: 1px solid #dee2e6; margin: 1rem 0;">

                                        {{-- ✅ FALLBACK: Manual Input untuk Midtrans --}}
                                        <div class="alert alert-warning"
                                            style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); border: none; border-left: 4px solid #856404; border-radius: 8px; padding: 0.8rem; margin-bottom: 1rem; font-size: 0.85rem;">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <strong>Manual Fallback:</strong> Jika pembayaran Midtrans tidak berhasil,
                                            masukkan nominal secara manual di bawah untuk menandai lunas.
                                        </div>

                                        <div class="form-group" x-data="paidBox(@entangle('paid_amount').live)" x-init="init()"
                                            style="margin-bottom: 1rem;">
                                            <label for="paid_amount_view"
                                                style="font-weight: 600; font-size: 0.95rem; color: #495057; margin-bottom: 0.5rem;">
                                                💰 Jumlah Dibayar (Manual)
                                            </label>
                                            <div wire:ignore>
                                                <input type="text" class="form-control" id="paid_amount_view"
                                                    style="padding: 0.6rem; border-radius: 8px; border: 2px solid #ced4da; font-size: 1rem; font-weight: 600; text-align: right;"
                                                    inputmode="numeric" placeholder="0" autocomplete="off">
                                            </div>
                                            @error('paid_amount')
                                                <small class="text-danger"
                                                    style="display: block; margin-top: 0.5rem;">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        @php
                                            $total = (int) ($sale->total_amount ?? 0);
                                            $paid = (int) ($paid_amount ?? 0);
                                            $diff = $paid - $total;
                                        @endphp

                                        <div class="alert {{ $diff >= 0 ? 'alert-primary' : 'alert-warning' }}"
                                            style="background: linear-gradient(135deg, {{ $diff >= 0 ? '#cce5ff 0%, #b3d7ff 100%' : '#fff3cd 0%, #ffe69c 100%' }}); border: none; border-left: 4px solid {{ $diff >= 0 ? '#004085' : '#856404' }}; border-radius: 8px; padding: 0.8rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                            <strong
                                                style="font-size: 0.95rem;">{{ $diff >= 0 ? '💵 Kembalian' : '⚠️ Sisa yang harus dibayar' }}</strong>
                                            <span
                                                style="font-size: 1.2rem; font-weight: 700;">{{ format_currency(abs($diff)) }}</span>
                                        </div>

                                        <button type="submit" wire:loading.attr="disabled"
                                            class="btn btn-success btn-block"
                                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; padding: 0.6rem; font-size: 0.95rem; font-weight: 600; border-radius: 8px; transition: all 0.3s;"
                                            @disabled(($paid_amount ?? 0) < (int) ($sale->total_amount ?? 0))>
                                            <span wire:loading wire:target="markAsPaid"
                                                class="spinner-border spinner-border-sm" role="status"></span>
                                            <i class="bi bi-check-circle"></i> Tandai Lunas
                                        </button>
                                    </div>
                                @else
                                    {{-- MANUAL PAYMENT (Tunai/Transfer/QRIS) --}}
                                    <div wire:key="manual-section">
                                        @if ($payment_method === 'Transfer')
                                            <div class="form-group" style="margin-bottom: 1rem;">
                                                <label
                                                    style="font-weight: 600; font-size: 0.95rem; color: #495057; margin-bottom: 0.5rem;">🏦
                                                    Nama Bank</label>
                                                <input type="text" class="form-control"
                                                    wire:model.live="bank_name"
                                                    style="padding: 0.6rem; border-radius: 8px; border: 2px solid #ced4da; font-size: 0.95rem;"
                                                    placeholder="Mandiri / BCA / BRI ...">
                                                @error('bank_name')
                                                    <small class="text-danger"
                                                        style="display: block; margin-top: 0.5rem;">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="form-group" x-data="paidBox(@entangle('paid_amount').live)" x-init="init()"
                                            style="margin-bottom: 1rem;">
                                            <label for="paid_amount_view"
                                                style="font-weight: 600; font-size: 0.95rem; color: #495057; margin-bottom: 0.5rem;">
                                                💰 Jumlah Dibayar
                                            </label>
                                            <div wire:ignore>
                                                <input type="text" class="form-control" id="paid_amount_view"
                                                    style="padding: 0.6rem; border-radius: 8px; border: 2px solid #ced4da; font-size: 1rem; font-weight: 600; text-align: right;"
                                                    inputmode="numeric" placeholder="0" autocomplete="off">
                                            </div>
                                            @error('paid_amount')
                                                <small class="text-danger"
                                                    style="display: block; margin-top: 0.5rem;">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        @php
                                            $total = (int) ($sale->total_amount ?? 0);
                                            $paid = (int) ($paid_amount ?? 0);
                                            $diff = $paid - $total;
                                        @endphp

                                        <div class="alert {{ $diff >= 0 ? 'alert-primary' : 'alert-warning' }}"
                                            style="background: linear-gradient(135deg, {{ $diff >= 0 ? '#cce5ff 0%, #b3d7ff 100%' : '#fff3cd 0%, #ffe69c 100%' }}); border: none; border-left: 4px solid {{ $diff >= 0 ? '#004085' : '#856404' }}; border-radius: 8px; padding: 0.8rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                            <strong
                                                style="font-size: 0.95rem;">{{ $diff >= 0 ? '💵 Kembalian' : '⚠️ Sisa yang harus dibayar' }}</strong>
                                            <span
                                                style="font-size: 1.2rem; font-weight: 700;">{{ format_currency(abs($diff)) }}</span>
                                        </div>

                                        <button type="submit" wire:loading.attr="disabled"
                                            class="btn btn-success btn-block"
                                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; padding: 0.6rem; font-size: 0.95rem; font-weight: 600; border-radius: 8px; transition: all 0.3s;"
                                            @disabled(($paid_amount ?? 0) < (int) ($sale->total_amount ?? 0))>
                                            <span wire:loading wire:target="markAsPaid"
                                                class="spinner-border spinner-border-sm" role="status"></span>
                                            <i class="bi bi-check-circle"></i> Tandai Lunas
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endif
                @endif

                {{-- Transaksi Baru --}}
                <button wire:click="newTransaction" class="btn btn-block"
                    style="background: linear-gradient(135deg, #ff9800 0%, #ff6f00 100%); border: none; padding: 0.6rem; font-size: 0.95rem; font-weight: 600; border-radius: 8px; margin-top: 1rem; transition: all 0.3s; color: white; box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255, 152, 0, 0.5)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255, 152, 0, 0.3)'">
                    <i class="bi bi-arrow-clockwise" style="font-size: 1rem;"></i> Transaksi Baru
                </button>
            @else
                {{-- ========== CART SECTION ========== --}}
                <div class="table-responsive" style="margin-bottom: 1.5rem;">
                    <table class="table" style="margin-bottom: 0;">
                        <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <tr class="text-center">
                                <th style="color: white; padding: 1rem; font-weight: 600;">Produk</th>
                                <th style="color: white; padding: 1rem; font-weight: 600;">Harga</th>
                                <th style="color: white; padding: 1rem; font-weight: 600; width: 130px;">Jumlah</th>
                                <th style="color: white; padding: 1rem; font-weight: 600;">Subtotal</th>
                                <th style="color: white; padding: 1rem; font-weight: 600;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cart_items as $cart_item)
                                <tr style="border-bottom: 1px solid #e9ecef; transition: background 0.3s;"
                                    onmouseover="this.style.background='rgba(102, 126, 234, 0.05)'"
                                    onmouseout="this.style.background='white'">
                                    <td class="align-middle" style="padding: 1rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="font-weight: 500;">{{ $cart_item->name }}</span>

                                            @php
                                                $st = $cart_item->options->source_type ?? 'new';
                                                $label = $cart_item->options->source_type_label ?? null;

                                                if (!$label) {
                                                    if ($st === 'new') {
                                                        $label = 'Baru';
                                                        $class = 'badge-primary';
                                                    } elseif ($st === 'second') {
                                                        $label = 'Bekas';
                                                        $class = 'badge-warning';
                                                    } elseif ($st === 'manual') {
                                                        $mk = $cart_item->options->manual_kind ?? 'service';
                                                        $label = $mk === 'service' ? 'Jasa' : 'Barang';
                                                        $class = $mk === 'service' ? 'badge-info' : 'badge-success';
                                                    } else {
                                                        $label = 'Lainnya';
                                                        $class = 'badge-secondary';
                                                    }
                                                } else {
                                                    $class = match ($label) {
                                                        'Baru' => 'badge-primary',
                                                        'Bekas' => 'badge-warning',
                                                        'Jasa' => 'badge-info',
                                                        'Barang' => 'badge-success',
                                                        default => 'badge-secondary',
                                                    };
                                                }
                                            @endphp

                                            <span class="badge {{ $class }}"
                                                style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                                {{ $label }}
                                            </span>

                                            @if (!empty($cart_item->options->code) && $cart_item->options->code !== '-')
                                                <span class="badge badge-light"
                                                    style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                                    {{ $cart_item->options->code }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- KOLOM HARGA (dengan indicator diskon) --}}
                                    <td class="text-center align-middle" style="padding: 1rem;">
                                        @php
                                            $isAdjusted = !empty($cart_item->options->is_price_adjusted);
                                            $originalPrice = $cart_item->options->original_price ?? $cart_item->price;
                                        @endphp

                                        @if ($isAdjusted)
                                            <div class="mb-1">
                                                <small
                                                    class="text-muted"><del>{{ format_currency($originalPrice) }}</del></small>
                                            </div>
                                            <strong class="text-danger" style="font-size: 1.1em;">
                                                {{ format_currency($cart_item->price) }}
                                            </strong><br>
                                            <span class="badge badge-warning badge-sm mt-1"
                                                title="{{ $cart_item->options->price_adjustment_note ?? 'Ada diskon' }}"
                                                style="cursor: help;">
                                                <i class="bi bi-tag-fill"></i> Diskon
                                            </span>
                                        @else
                                            <strong
                                                style="font-weight: 600;">{{ format_currency($cart_item->price) }}</strong>
                                        @endif
                                    </td>

                                    <td class="align-middle" style="padding: 1rem;">
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
                                            <input type="number" class="form-control text-center"
                                                value="{{ $cart_item->qty }}" readonly>
                                        @endif
                                    </td>

                                    <td class="align-middle text-center"
                                        style="padding: 1rem; font-weight: 700; color: #28a745; font-size: 1.05rem;">
                                        {{ format_currency($cart_item->subtotal) }}
                                    </td>

                                    {{-- KOLOM AKSI --}}
                                    <td class="text-center align-middle" style="padding: 1rem;">
                                        <div class="btn-group-vertical" role="group" style="gap: .25rem;">
                                            {{-- ✅ BUTTON EDIT HARGA (hanya new/second) --}}
                                            @if (in_array($cart_item->options->source_type ?? 'new', ['new', 'second']))
                                                <button type="button"
                                                    wire:click="openEditPriceModal('{{ $cart_item->rowId }}')"
                                                    class="btn btn-sm btn-warning" title="Edit Harga"
                                                    style="min-width: 120px;">
                                                    <i class="bi bi-pencil-square"></i> Edit Harga
                                                </button>
                                            @endif

                                            {{-- Button Hapus --}}
                                            <button type="button" wire:click="removeItem('{{ $cart_item->rowId }}')"
                                                wire:loading.attr="disabled"
                                                wire:target="removeItem('{{ $cart_item->rowId }}')"
                                                class="btn btn-sm btn-danger" style="min-width: 120px;">
                                                <span wire:loading.remove
                                                    wire:target="removeItem('{{ $cart_item->rowId }}')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </span>
                                                <span wire:loading wire:target="removeItem('{{ $cart_item->rowId }}')"
                                                    class="spinner-border spinner-border-sm" role="status"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center"
                                        style="padding: 3rem; font-size: 1.2rem; color: #dc3545; font-weight: 600;">
                                        🛒 Keranjang Kosong
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr style="border-top: 2px solid #e9ecef; margin: 1.5rem 0;">

                <div class="table-responsive" style="margin-bottom: 1.5rem;">
                    <table class="table table-striped"
                        style="margin-bottom: 1rem; background: white; border-radius: 10px; overflow: hidden;">
                        <tr style="font-size: 1.1rem;">
                            <th style="padding: 1rem;">Subtotal</th>
                            <td style="padding: 1rem; font-weight: 600;">
                                {{ format_currency(Cart::instance($cart_instance)->subtotal()) }}
                            </td>
                        </tr>
                        <tr
                            style="font-size: 1.3rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                            <th style="padding: 1rem; font-weight: 700; color: #667eea;">Grand Total</th>
                            <td style="padding: 1rem; font-weight: 700; color: #667eea;">
                                {{ format_currency($total_amount) }}
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="customer_name" style="font-weight: 600; font-size: 1.05rem; color: #495057;">
                        <i class="bi bi-person-fill" style="margin-right: 0.5rem;"></i> Nama Customer
                        <span class="badge badge-secondary" style="font-size: 0.75rem;">Opsional</span>
                    </label>
                    <input type="text" id="customer_name" class="form-control" wire:model.defer="customer_name"
                        style="padding: 0.75rem; border-radius: 8px; border: 2px solid #ced4da; font-size: 1rem;"
                        placeholder="Kosongkan jika tidak perlu" maxlength="255">
                    <small class="form-text text-muted" style="font-size: 0.9rem;">
                        💡 Isi hanya jika customer meminta namanya dicantumkan di invoice
                    </small>
                    @error('customer_name')
                        <span class="text-danger" style="display: block; margin-top: 0.5rem;">{{ $message }}</span>
                    @enderror
                </div>

                <button wire:click="createInvoice" wire:loading.attr="disabled" class="btn btn-primary btn-block"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 0.6rem; font-size: 0.95rem; font-weight: 600; border-radius: 8px; transition: all 0.3s; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.5)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)'"
                    {{ Cart::instance($cart_instance)->count() == 0 ? 'disabled' : '' }}>
                    <span wire:loading wire:target="createInvoice"
                        class="spinner-border spinner-border-sm mr-1"></span>
                    <i class="bi bi-receipt"></i> Buat Invoice
                </button>
            @endif
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT HARGA DENGAN CATATAN WAJIB (DI DALAM ROOT) --}}
    {{-- ========================================= --}}
    <div wire:ignore.self class="modal fade" id="editPriceModal" tabindex="-1" role="dialog"
        aria-labelledby="editPriceModalLabel" aria-hidden="true" x-data="{ show: @entangle('showEditPriceModal') }" x-init="$watch('show', value => {
            if (value) { $('#editPriceModal').modal('show'); } else { $('#editPriceModal').modal('hide'); }
        });
        $('#editPriceModal').on('hidden.bs.modal', function() {
            if (@this.showEditPriceModal) { @this.closeEditPriceModal(); }
        });">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editPriceModalLabel">
                        <i class="bi bi-pencil-square"></i> Edit Harga Produk
                    </h5>
                    <button type="button" class="close text-white" wire:click="closeEditPriceModal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    {{-- Info Produk --}}
                    <div class="alert alert-info mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <strong><i class="bi bi-box-seam"></i> Produk:</strong>
                                <span class="font-weight-bold">{{ $editingProductName }}</span>
                            </div>
                            <div class="col-md-4 text-right">
                                @php
                                    $sourceLabel = match ($editingSourceType) {
                                        'new' => 'Baru',
                                        'second' => 'Bekas',
                                        default => 'Lainnya',
                                    };
                                    $badgeClass = match ($editingSourceType) {
                                        'new' => 'badge-primary',
                                        'second' => 'badge-warning',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} badge-lg">{{ $sourceLabel }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Harga Original (Read-only) --}}
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="bi bi-tag"></i> Harga Asli (Original)</label>
                        <input type="text" class="form-control form-control-lg bg-light"
                            value="Rp {{ number_format($editingOriginalPrice, 0, ',', '.') }}" disabled readonly>
                        <small class="form-text text-muted">Harga dari master produk (tidak bisa diubah)</small>
                    </div>

                    {{-- Harga Baru (AutoNumeric) --}}
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="bi bi-currency-dollar"></i> Harga Baru <span
                                class="text-danger">*</span></label>
                        <input type="text" wire:model.lazy="newPrice" id="newPriceInput"
                            class="form-control form-control-lg @error('newPrice') is-invalid @enderror"
                            placeholder="Masukkan harga baru" x-data x-init="new AutoNumeric('#newPriceInput', {
                                currencySymbol: 'Rp ',
                                decimalCharacter: ',',
                                digitGroupSeparator: '.',
                                decimalPlaces: 0,
                                minimumValue: '1',
                                modifyValueOnWheel: false
                            });">
                        @error('newPrice')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted"><i class="bi bi-info-circle"></i> Minimal Rp 1. Format: Rp
                            1.000.000</small>
                    </div>

                    {{-- Selisih/Diskon (Computed) --}}
                    @php
                        $diff = (int) $editingOriginalPrice - (int) $newPrice;
                        $diffPercent = $editingOriginalPrice > 0 ? round(($diff / $editingOriginalPrice) * 100, 1) : 0;
                    @endphp
                    @if ($diff != 0)
                        <div class="alert {{ $diff > 0 ? 'alert-warning' : 'alert-success' }} mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <strong style="font-size: 1.1em;">
                                        @if ($diff > 0)
                                            <i class="bi bi-arrow-down-circle text-danger"></i> Diskon/Potongan:
                                        @else
                                            <i class="bi bi-arrow-up-circle text-success"></i> Kenaikan Harga:
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-md-6 text-right">
                                    <h4 class="mb-0 {{ $diff > 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format(abs($diff), 0, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">({{ $diffPercent }}% dari harga asli)</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Catatan (WAJIB jika harga turun) --}}
                    @if ((int) $newPrice < (int) $editingOriginalPrice)
                        <div class="form-group">
                            <label class="font-weight-bold text-danger">
                                <i class="bi bi-chat-left-text"></i> Catatan/Alasan Diskon
                                <span class="badge badge-danger">WAJIB DIISI</span>
                            </label>
                            <textarea wire:model.defer="priceNote" class="form-control @error('priceNote') is-invalid @enderror" rows="4"
                                placeholder="Contoh:
- Nego customer Rp 50.000
- Promo diskon 10% bulan ini
- Barang cacat minor (lecet/goresan)
- Customer loyal/repeat order"
                                required></textarea>
                            @error('priceNote')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="bi bi-exclamation-triangle"></i> <strong>Minimal 10 karakter.</strong>
                                Jelaskan alasan penurunan harga.
                            </small>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="font-weight-bold"><i class="bi bi-chat-left-text"></i> Catatan
                                (Opsional)</label>
                            <textarea wire:model.defer="priceNote" class="form-control" rows="3"
                                placeholder="Catatan tambahan (opsional)"></textarea>
                            <small class="form-text text-muted">Anda bisa menambahkan catatan meskipun harga tidak
                                turun.</small>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditPriceModal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning btn-lg" wire:click="saveEditedPrice"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveEditedPrice"><i class="bi bi-check-circle"></i>
                            Simpan Perubahan</span>
                        <span wire:loading wire:target="saveEditedPrice"><i class="bi bi-hourglass-split"></i>
                            Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- ====== /Modal ====== --}}
</div> {{-- <-- Ini penutup ROOT Livewire. Tidak ada elemen HTML lain setelahnya --}}

{{-- SCRIPTS --}}
@once
    @push('page_scripts')
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
        </script>

        <script>
            function paidBox(entangled) {
                return {
                    an: null,
                    paid: entangled,
                    init() {
                        const el = document.getElementById('paid_amount_view');
                        if (!el || el._anInitialized) return;
                        el._anInitialized = true;

                        const toInt = (s) => (s == null) ? 0 : (parseInt(String(s).replace(/[^\d-]/g, ''), 10) || 0);

                        if (window.AutoNumeric) {
                            this.an = new AutoNumeric(el, {
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                decimalPlaces: 0,
                                unformatOnSubmit: true,
                                modifyValueOnWheel: false,
                            });

                            this.$nextTick(() => {
                                const initVal = Number(this.paid || 0);
                                this.an.set(initVal);
                            });

                            const push = () => {
                                const raw = this.an.getNumber();
                                const v = raw ? parseInt(raw, 10) : 0;
                                if (this.paid !== v) this.paid = v;
                            };

                            el.addEventListener('autoNumeric:rawValueModified', push);
                            el.addEventListener('change', push);
                        } else {
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

                        this.$watch('paid', (v) => {
                            const n = Number(v || 0);
                            if (this.an) {
                                const current = this.an.getNumber();
                                if (String(n) !== String(current)) {
                                    this.an.set(isNaN(n) ? 0 : n);
                                }
                            } else {
                                el.value = n ? n.toLocaleString('id-ID') : '';
                            }
                        });
                    }
                }
            }

            document.addEventListener('livewire:initialized', () => {
                Livewire.on('open-midtrans-snap', (data) => {
                    const snapToken = data?.[0]?.token || null;
                    if (!snapToken || typeof snap === 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Token pembayaran tidak valid'
                        });
                        return;
                    }
                    snap.pay(snapToken, {
                        onSuccess: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                timer: 3000,
                                showConfirmButton: false
                            });
                            @this.checkMidtransStatus();
                            setTimeout(() => window.location.reload(), 3000);
                        },
                        onPending: function() {
                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran'
                            });
                            @this.checkMidtransStatus();
                        },
                        onError: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal'
                            });
                        },
                        onClose: function() {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pembayaran Dibatalkan'
                            });
                        }
                    });
                });

                document.addEventListener('click', function(e) {
                    if (e.target && e.target.id === 'pay-button-midtrans') {
                        const token = @json($midtrans_snap_token ?? null);
                        if (token && typeof snap !== 'undefined') {
                            snap.pay(token);
                        }
                    }
                });
            });
        </script>
    @endpush
@endonce
