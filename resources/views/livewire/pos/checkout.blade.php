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

                {{-- Customer Info (Updated) --}}
                @if ($sale->customer_id || $sale->customer_name)
                    <div class="alert alert-info"
                        style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border: none; border-left: 5px solid #17a2b8; border-radius: 10px; padding: 1rem; display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.0rem;">

                        <div style="display: flex; align-items: flex-start;">
                            <i class="bi bi-person-circle"
                                style="font-size: 1.6rem; margin-right: 1rem; color: #17a2b8;"></i>

                            <div>
                                {{-- Nama utama pakai accessor --}}
                                <h6 style="margin: 0 0 0.25rem 0; font-weight: 700; font-size: 1.05rem;">
                                    {{ $sale->customer_display_name }}
                                </h6>

                                <div class="small text-muted">
                                    {{-- Email: prioritas dari relasi customer --}}
                                    @if ($sale->customer && $sale->customer->customer_email)
                                        <div class="mb-1">
                                            <i class="bi bi-envelope mr-1"></i>
                                            {{ $sale->customer->customer_email }}
                                        </div>
                                    @elseif($sale->customer_email)
                                        <div class="mb-1">
                                            <i class="bi bi-envelope mr-1"></i>
                                            {{ $sale->customer_email }}
                                        </div>
                                    @endif

                                    {{-- Phone --}}
                                    @if ($sale->customer && $sale->customer->customer_phone)
                                        <div class="mb-1">
                                            <i class="bi bi-telephone mr-1"></i>
                                            {{ $sale->customer->customer_phone }}
                                        </div>
                                    @elseif($sale->customer_phone)
                                        <div class="mb-1">
                                            <i class="bi bi-telephone mr-1"></i>
                                            {{ $sale->customer_phone }}
                                        </div>
                                    @endif

                                    {{-- City --}}
                                    @if ($sale->customer && $sale->customer->city)
                                        <div>
                                            <i class="bi bi-geo-alt mr-1"></i>
                                            {{ $sale->customer->city }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Link ke detail customer kalau punya customer_id --}}
                        @if ($sale->customer_id)
                            <a href="{{ route('customers.show', $sale->customer_id) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank" title="Lihat Detail Customer">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        @endif
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
                                                wire:change="onPaymentMethodChange" value="Cash" wire:key="pm-cash">
                                            <label class="custom-control-label" for="payCash">💵 Tunai</label>
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

                                        <div class="form-group" x-data="paidBox(@entangle('paid_amount').live, '#paid_amount_view_midtrans')" x-init="init()"
                                            style="margin-bottom: 1rem;">
                                            <label for="paid_amount_view"
                                                style="font-weight: 600; font-size: 0.95rem; color: #495057; margin-bottom: 0.5rem;">
                                                💰 Jumlah Dibayar (Manual)
                                            </label>
                                            <div wire:ignore>
                                                <input type="text" class="form-control"
                                                    id="paid_amount_view_midtrans"
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

                                        <div class="form-group" x-data="paidBox(@entangle('paid_amount').live, '#paid_amount_view_manual')" x-init="init()"
                                            style="margin-bottom: 1rem;">
                                            <label for="paid_amount_view"
                                                style="font-weight: 600; font-size: 0.95rem; color: #495057; margin-bottom: 0.5rem;">
                                                💰 Jumlah Dibayar
                                            </label>
                                            <div wire:ignore>
                                                <input type="text" class="form-control"
                                                    id="paid_amount_view_manual"
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
                                                title="Potongan {{ format_currency($cart_item->options->price_adjustment_amount ?? 0) }}{{ !empty($cart_item->options->price_adjustment_note) ? ' • ' . $cart_item->options->price_adjustment_note : '' }}"
                                                style="cursor: help;">
                                                <i class="bi bi-tag-fill"></i> Diskon
                                            </span>
                                        @else
                                            <strong
                                                style="font-weight: 600;">{{ format_currency($cart_item->price) }}</strong>
                                        @endif
                                    </td>

                                    <td class="align-middle" style="padding: 1rem;">
                                        @php
                                            // Item yang BISA edit qty:
                                            // - new: Produk baru dari master
                                            // - manual: Input manual dari ManualItemForm
                                            // - service_master: Jasa dari ServiceMaster
                                            $editableQty = in_array($cart_item->options->source_type, [
                                                'new',
                                                'manual',
                                                'service_master',
                                            ]);
                                        @endphp

                                        @if ($editableQty)
                                            {{-- BISA EDIT QTY --}}
                                            <div class="input-group" style="min-width: 150px;">
                                                <div class="input-group-prepend">
                                                    <button type="button"
                                                        wire:click="decrementQuantity('{{ $cart_item->rowId }}')"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        {{ $cart_item->qty <= 1 ? 'disabled' : '' }}>
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                </div>
                                                <input wire:model.defer="quantity.{{ $cart_item->id }}"
                                                    wire:blur="updateQuantity('{{ $cart_item->rowId }}', '{{ $cart_item->id }}', '{{ $cart_item->options->source_type }}')"
                                                    type="number" class="form-control text-center"
                                                    value="{{ $cart_item->qty }}" min="1">
                                                <div class="input-group-append">
                                                    <button type="button"
                                                        wire:click="incrementQuantity('{{ $cart_item->rowId }}')"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            {{-- READONLY: Tidak bisa edit qty --}}
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
                                                @hasanyrole('Admin|Super Admin|Owner|Supervisor|Kasir')
                                                    <button type="button"
                                                        wire:click="openEditPriceModal('{{ $cart_item->rowId }}')"
                                                        class="btn btn-sm btn-warning" style="min-width:120px;">
                                                        <i class="bi bi-pencil-square"></i> Edit Harga
                                                    </button>
                                                @endhasanyrole
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

                {{-- ==================== CUSTOMER SECTION (REVISED) ==================== --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-circle text-primary mr-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-0 font-weight-bold">Data Customer</h6>
                                <small class="text-muted">Pilih dari database atau input manual</small>
                            </div>
                            <span class="badge badge-light-info ml-auto">Opsional</span>
                        </div>

                        {{-- Toggle: Select Customer atau Manual Input (PAKAI LIVEWIRE) --}}
                        <div class="btn-group w-100 mb-3" role="group" aria-label="Customer mode">
                            <button type="button"
                                class="btn btn-outline-primary {{ $customer_mode === 'select' ? 'active' : '' }}"
                                wire:click="setCustomerMode('select')" id="btn-select-customer">
                                <i class="bi bi-search mr-1"></i> Pilih Customer
                            </button>
                            <button type="button"
                                class="btn btn-outline-primary {{ $customer_mode === 'manual' ? 'active' : '' }}"
                                wire:click="setCustomerMode('manual')" id="btn-manual-customer">
                                <i class="bi bi-pencil mr-1"></i> Input Manual
                            </button>
                        </div>

                        {{-- Mode 1: Select Customer dari Database --}}
                        <div id="select-customer-mode" @class(['d-none' => $customer_mode !== 'select'])>
                            <div class="form-group mb-3">
                                <label class="small font-weight-semibold mb-2">
                                    <i class="bi bi-person-check mr-1 text-primary"></i> Pilih Customer Terdaftar
                                </label>

                                {{-- Penting: isolasi Select2 dari Livewire --}}
                                <div wire:ignore>
                                    <select class="form-control select2-customer" id="select-customer"
                                        style="width: 100%;">
                                        <option value="">-- Ketik untuk mencari customer --</option>
                                    </select>
                                </div>

                                
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle mr-1"></i>
                                    Cari berdasarkan nama, email, atau nomor telepon
                                </small>
                            </div>

                            {{-- Display Selected Customer Info (PAKAI DATA LIVEWIRE, persist saat re-render) --}}
                            <div id="selected-customer-info"
                                class="alert alert-success border-0 shadow-sm {{ $customer_id ? '' : 'd-none' }}">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success mr-2 mt-1"
                                        style="font-size: 1.5rem;"></i>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold mb-1" id="display-customer-name"
                                            style="font-size: 1.05rem;">
                                            {{ $customer_name }}
                                        </div>
                                        <div class="small text-muted">
                                            <div class="mb-1"><i class="bi bi-envelope mr-1"></i>
                                                <span id="display-customer-email">{{ $customer_email ?: '-' }}</span>
                                            </div>
                                            <div class="mb-1"><i class="bi bi-telephone mr-1"></i>
                                                <span id="display-customer-phone">{{ $customer_phone ?: '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle"
                                        id="clear-customer" title="Hapus Pilihan">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Mode 2: Manual Input Customer --}}
                        <div id="manual-customer-mode" @class(['d-none' => $customer_mode !== 'manual'])>
                            <div class="form-group mb-3">
                                <label class="small font-weight-semibold mb-2">
                                    <i class="bi bi-person mr-1 text-primary"></i> Nama Customer
                                </label>
                                <input type="text" class="form-control" id="manual-customer-name"
                                    wire:model.defer="customer_name" placeholder="Kosongkan jika tidak perlu">
                                <small class="form-text text-muted">
                                    <i class="bi bi-lightbulb mr-1 text-warning"></i>
                                    Isi hanya jika customer meminta namanya dicantumkan di invoice
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-semibold mb-2">
                                            <i class="bi bi-envelope mr-1"></i> Email
                                        </label>
                                        <input type="email" class="form-control" id="manual-customer-email"
                                            wire:model.defer="customer_email" placeholder="email@example.com">
                                        <small class="form-text text-muted">Opsional</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-semibold mb-2">
                                            <i class="bi bi-telephone mr-1"></i> No. Telepon
                                        </label>
                                        <input type="text" class="form-control" id="manual-customer-phone"
                                            wire:model.defer="customer_phone" placeholder="08123456789">
                                        <small class="form-text text-muted">Opsional</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden input untuk customer_id terpilih --}}
                        <input type="hidden" id="selected-customer-id" wire:model.defer="customer_id">
                    </div>
                </div>
                {{-- ==================== END CUSTOMER SECTION ==================== --}}


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

                    {{-- Harga Asli (read-only) --}}
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="bi bi-tag"></i> Harga Asli</label>
                        <input type="text" class="form-control form-control-lg bg-light"
                            value="Rp {{ number_format($editingOriginalPrice, 0, ',', '.') }}" disabled readonly>
                        <small class="form-text text-muted">Harga dari master produk.</small>
                    </div>

                    {{-- Pengurangan Harga (AutoNumeric) --}}
                    <div class="form-group" x-data="paidBox(@entangle('discountAmount').live, '#discount_amount_input')" x-init="init()">
                        <label class="font-weight-bold">
                            <i class="bi bi-cash-stack"></i> Pengurangan Harga <span class="text-danger">*</span>
                        </label>
                        <div wire:ignore>
                            <input type="text" id="discount_amount_input"
                                class="form-control form-control-lg @error('discountAmount') is-invalid @enderror"
                                placeholder="0" inputmode="numeric" autocomplete="off" style="text-align: right;">
                        </div>
                        @error('discountAmount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Masukkan 0 jika tidak ada diskon. Batas maksimal = harga asli.
                        </small>
                    </div>

                    @php
                        $orig = (int) ($editingOriginalPrice ?? 0);
                        $disc = (int) ($discountAmount ?? 0);
                        $new = max(0, $orig - $disc);
                        $pct = $orig > 0 ? round(($disc / $orig) * 100, 1) : 0;
                    @endphp

                    {{-- Ringkasan --}}
                    <div class="alert {{ $disc > 0 ? 'alert-warning' : 'alert-secondary' }} mb-3"
                        style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <strong>
                                {{ $disc > 0 ? 'Diskon/Potongan:' : 'Tidak ada diskon' }}
                            </strong>
                            @if ($disc > 0)
                                <div>Rp {{ number_format($disc, 0, ',', '.') }} ({{ $pct }}% dari harga asli)
                                </div>
                            @endif
                        </div>
                        <div style="text-align:right;">
                            <div class="text-muted" style="font-size:.9rem;">Harga Setelah Potong</div>
                            <div class="h4 m-0">Rp {{ number_format($new, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    {{-- Alasan (WAJIB jika ada diskon) --}}
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <i class="bi bi-chat-left-text"></i> Alasan
                            @if ($disc > 0)
                                <span class="badge badge-danger">WAJIB</span>
                            @else
                                <span class="badge badge-secondary">Opsional</span>
                            @endif
                        </label>
                        <textarea wire:model.defer="priceNote" class="form-control @error('priceNote') is-invalid @enderror" rows="4"
                            placeholder="Contoh:
- Nego pelanggan (1 set)
- Promo stok tahun lama
- Barang cacat minor">
            </textarea>
                        @error('priceNote')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Minimal 10 karakter jika ada potongan.
                        </small>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditPriceModal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning btn-lg" wire:click="saveEditedPrice"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveEditedPrice">
                            <i class="bi bi-check-circle"></i> Simpan Perubahan
                        </span>
                        <span wire:loading wire:target="saveEditedPrice">
                            <i class="bi bi-hourglass-split"></i> Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- ====== /Modal ====== --}}
</div> {{-- <-- Ini penutup ROOT Livewire. Tidak ada elemen HTML lain setelahnya --}}
@push('styles')
    {{-- ... existing styles ... --}}

    <style>
        /* ========== CUSTOMER SECTION STYLES ========== */
        .btn-group-toggle .btn {
            transition: all 0.3s ease;
        }

        .btn-group-toggle .btn.active {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            color: white;
            border-color: #4834DF;
            box-shadow: 0 4px 12px rgba(72, 52, 223, 0.3);
        }

        .btn-group-toggle .btn:not(.active) {
            background: white;
            color: #4834DF;
            border-color: #e0e0e0;
        }

        .btn-group-toggle .btn:not(.active):hover {
            background: #f8f7ff;
            border-color: #4834DF;
            transform: translateY(-2px);
        }

        /* Select2 Customer Dropdown */
        .select2-customer {
            font-size: 0.95rem;
        }

        .select2-result-customer {
            padding: 8px 0;
        }

        .select2-result-customer__name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .select2-result-customer__meta {
            font-size: 0.85rem;
            color: #718096;
        }

        .select2-result-customer__meta i {
            color: #a0aec0;
            margin-right: 4px;
        }

        /* Selected Customer Info Alert */
        #selected-customer-info {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #selected-customer-info .btn-outline-danger {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #selected-customer-info .btn-outline-danger:hover {
            background-color: #e55353;
            border-color: #e55353;
            color: white;
        }

        /* Badge Light Info */
        .badge-light-info {
            background-color: #e7f3ff;
            color: #004085;
            padding: 0.35em 0.65em;
            font-weight: 600;
            font-size: 0.75rem;
        }
    </style>
@endpush

{{-- SCRIPTS --}}
@once
    @push('page_scripts')
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
        </script>

        <script>
            // ==========================
            // Currency helper (AutoNumeric)
            // ==========================
            function paidBox(entangled, selector) {
                return {
                    an: null,
                    paid: entangled,
                    el: null,
                    init() {
                        this.el = document.querySelector(selector);
                        if (!this.el || this.el.anInitialized) return;
                        this.el.anInitialized = true;

                        const toInt = (s) => s == null ? 0 : (parseInt(String(s).replace(/-/g, ''), 10) || 0);

                        if (window.AutoNumeric) {
                            this.an = new AutoNumeric(this.el, {
                                digitGroupSeparator: '.',
                                decimalCharacter: ',',
                                decimalPlaces: 0,
                                unformatOnSubmit: true,
                                modifyValueOnWheel: false,
                                minimumValue: 0,
                                overrideMinMaxLimits: 'invalid',
                                allowDecimalPadding: false,
                            });

                            this.$nextTick(() => {
                                this.an.set(Number(this.paid) || 0);
                            });

                            const push = () => {
                                const raw = this.an.getNumber();
                                const v = raw ? parseInt(raw, 10) : 0;
                                if (this.paid !== v) this.paid = v;
                            };

                            this.el.addEventListener('autoNumeric:rawValueModified', push);
                            this.el.addEventListener('change', push);
                        } else {
                            const push = () => {
                                const v = toInt(this.el.value);
                                if (this.paid !== v) this.paid = v;
                            };

                            this.el.addEventListener('input', push);
                            this.el.addEventListener('change', push);

                            this.$nextTick(() => {
                                const n = Number(this.paid) || 0;
                                this.el.value = n ? n.toLocaleString('id-ID') : '';
                            });

                            this.$watch('paid', (v) => {
                                const n = Number(v) || 0;
                                if (this.an) {
                                    const current = this.an.getNumber();
                                    if (String(n) !== String(current)) {
                                        this.an.set(isNaN(n) ? 0 : n);
                                    }
                                } else {
                                    this.el.value = n ? n.toLocaleString('id-ID') : '';
                                }
                            });
                        }
                    }
                };
            }

            // ==========================
            // Midtrans handlers
            // ==========================
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

            // ==========================
            // Customer UI state & Select2 init
            // ==========================
            (function() {
                const MODE_KEY = 'ob_customer_mode';
                const SELECTED_CACHE_KEY = 'ob_customer_selected';

                let customerMode = localStorage.getItem(MODE_KEY) || 'select';

                function applyMode() {
                    if (customerMode === 'manual') {
                        $('#select-customer-mode').hide();
                        $('#manual-customer-mode').show();
                        $('#btn-manual-customer').addClass('active');
                        $('#btn-select-customer').removeClass('active');
                    } else {
                        $('#select-customer-mode').show();
                        $('#manual-customer-mode').hide();
                        $('#btn-select-customer').addClass('active');
                        $('#btn-manual-customer').removeClass('active');
                    }
                }

                function saveMode(mode) {
                    customerMode = mode === 'manual' ? 'manual' : 'select';
                    localStorage.setItem(MODE_KEY, customerMode);
                    applyMode();
                }

                function cacheSelectedCustomer(data) {
                    const cached = {
                        id: data?.id ?? null,
                        customer_name: data?.customer_name || data?.text || '',
                        customer_email: data?.customer_email || '',
                        customer_phone: data?.customer_phone || '',
                        city: data?.city || ''
                    };
                    localStorage.setItem(SELECTED_CACHE_KEY, JSON.stringify(cached));
                }

                function clearCachedCustomer() {
                    localStorage.removeItem(SELECTED_CACHE_KEY);
                }

                function refreshSelectedInfoUI() {
                    const id = $('#selected-customer-id').val();
                    if (id) {
                        const cached = JSON.parse(localStorage.getItem(SELECTED_CACHE_KEY) || '{}');
                        if (cached.customer_name) $('#display-customer-name').text(cached.customer_name);
                        if ('customer_email' in cached) $('#display-customer-email').text(cached.customer_email || '-');
                        if ('customer_phone' in cached) $('#display-customer-phone').text(cached.customer_phone || '-');
                        if ('city' in cached) $('#display-customer-city').text(cached.city || '-');
                        $('#selected-customer-info').removeClass('d-none');
                    } else {
                        $('#selected-customer-info').addClass('d-none');
                    }
                }

                function initCustomerSelect2() {
                    const $el = $('#select-customer');
                    if (!$el.length || $el.data('select2')) return;

                    if (typeof $el.select2 !== 'function') {
                        console.warn('Select2 belum ter-load di halaman ini.');
                        return;
                    }

                    $el.select2({
                        placeholder: '-- Ketik untuk mencari customer --',
                        allowClear: true,
                        ajax: {
                            url: '{{ route('customers.list') }}',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term || '',
                                    q: params.term || '',
                                    term: params.term || '',
                                    page: params.page || 1
                                };
                            },
                            processResults: function(data, params) {
                                params.page = params.page || 1;

                                const itemsRaw = data?.results ?? data?.data ?? data?.items ?? data ?? [];
                                const items = Array.isArray(itemsRaw) ? itemsRaw : [];
                                const results = items.map(it => ({
                                    id: it.id ?? it.value ?? it.customer_id ?? null,
                                    customer_name: it.customer_name ?? it.name ?? it.text ?? '',
                                    customer_email: it.customer_email ?? it.email ?? '',
                                    customer_phone: it.customer_phone ?? it.phone ?? '',
                                    city: it.city ?? it.kota ?? '',
                                    text: it.customer_name ?? it.name ?? it.text ?? ''
                                })).filter(r => r.id !== null);

                                const more = Boolean(data?.pagination?.more || data?.next_page_url);
                                return {
                                    results,
                                    pagination: {
                                        more
                                    }
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 0,
                        templateResult: function(c) {
                            if (c.loading) return c.text;
                            if (!c.id) return c.text;
                            return $(
                                '<div class="select2-result-customer">' +
                                '<div class="select2-result-customer__name">' + (c.customer_name || c
                                .text) + '</div>' +
                                '<div class="select2-result-customer__meta">' +
                                '<i class="bi bi-envelope"></i> ' + (c.customer_email || '-') + ' | ' +
                                '<i class="bi bi-telephone"></i> ' + (c.customer_phone || '-') + ' | ' +
                                '<i class="bi bi-geo-alt"></i> ' + (c.city || '-') +
                                '</div>' +
                                '</div>'
                            );
                        },
                        templateSelection: function(c) {
                            return c.customer_name || c.text || '';
                        },
                        language: {
                            inputTooShort: () => 'Ketik untuk mencari customer...',
                            searching: () => 'Mencari...',
                            noResults: () => 'Customer tidak ditemukan'
                        }
                    });

                    $el.on('select2:select', function(e) {
                        const d = e.params.data || {};
                        cacheSelectedCustomer(d);

                        @this.set('customer_id', d.id);
                        @this.set('customer_name', d.customer_name || d.text || '');
                        @this.set('customer_email', d.customer_email || '');
                        @this.set('customer_phone', d.customer_phone || '');

                        $('#selected-customer-id').val(d.id);
                        $('#display-customer-name').text(d.customer_name || d.text || '');
                        $('#display-customer-email').text(d.customer_email || '-');
                        $('#display-customer-phone').text(d.customer_phone || '-');
                        $('#display-customer-city').text(d.city || '-');
                        $('#selected-customer-info').removeClass('d-none');

                        saveMode('select');
                    });

                    $el.on('select2:clear', function() {
                        clearCachedCustomer();

                        @this.set('customer_id', null);
                        @this.set('customer_name', '');
                        @this.set('customer_email', '');
                        @this.set('customer_phone', '');

                        $('#selected-customer-id').val('');
                        $('#selected-customer-info').addClass('d-none');
                    });
                }

                // Toggle mode (hanya UI)
                $(document).on('click', '#btn-select-customer', function(e) {
                    e.preventDefault();
                    saveMode('select');
                });
                $(document).on('click', '#btn-manual-customer', function(e) {
                    e.preventDefault();
                    saveMode('manual');
                });

                function bootCustomerUI() {
                    applyMode();
                    initCustomerSelect2();
                    refreshSelectedInfoUI();
                }

                // 1)Init saat DOM ready
                $(function() {
                    bootCustomerUI();
                });

                // 2) Init ulang setelah Livewire render
                if (window.Livewire && typeof Livewire.hook === 'function') {
                    Livewire.hook('message.processed', () => {
                        bootCustomerUI();
                    });
                }
            })();
        </script>
    @endpush
@endonce
