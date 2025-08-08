<div wire:ignore.self class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">
                    <i class="bi bi-cart-check text-primary"></i> Konfirmasi Penjualan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            {{-- Menggunakan Alpine.js untuk mengelola state UI secara lokal --}}
            <div x-data="{ paymentMethod: @entangle('payment_method') }">
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="form-row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="total_amount">Total Belanja <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ format_currency($total_amount) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="paid_amount">Jumlah Dibayar <span class="text-danger">*</span></label>
                                            {{-- wire:model.defer agar tidak memicu update sampai form disubmit --}}
                                            <input wire:model.defer="paid_amount" id="paid_amount" type="text" class="form-control" required>
                                            @error('paid_amount') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Metode Pembayaran <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap">
                                        <div class="form-check form-check-inline">
                                            <input x-model="paymentMethod" class="form-check-input" type="radio" id="payment_tunai" value="Tunai">
                                            <label class="form-check-label" for="payment_tunai">Tunai</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input x-model="paymentMethod" class="form-check-input" type="radio" id="payment_transfer" value="Transfer">
                                            <label class="form-check-label" for="payment_transfer">Transfer</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input x-model="paymentMethod" class="form-check-input" type="radio" id="payment_kredit" value="Kredit">
                                            <label class="form-check-label" for="payment_kredit">Kredit</label>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- x-show akan menampilkan/menyembunyikan kolom bank secara instan --}}
                                <div x-show="paymentMethod === 'Transfer' || paymentMethod === 'Kredit'" class="form-group mt-2">
                                    <label for="bank_name">Nama Bank <span class="text-danger">*</span></label>
                                    <input wire:model.defer="bank_name" type="text" class="form-control" id="bank_name" placeholder="Contoh: BCA, Mandiri, dll.">
                                    @error('bank_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="note">Catatan (Jika Perlu)</label>
                                    <textarea wire:model.defer="note" id="note" rows="3" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Total Produk</th>
                                            <td><span class="badge badge-success">{{ Cart::instance($cart_instance)->count() }}</span></td>
                                        </tr>
                                        <tr class="text-primary">
                                            <th>Grand Total</th>
                                            <th>(=) {{ format_currency($total_amount) }}</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading wire:target="store" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>