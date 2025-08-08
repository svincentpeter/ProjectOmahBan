<div class="modal fade" id="discountModal{{ $cart_item->id }}" tabindex="-1" role="dialog" aria-labelledby="discountModalLabel{{ $cart_item->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="discountModalLabel{{ $cart_item->id }}">
                    Diskon: <span class="text-primary">{{ $cart_item->name }}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if (session()->has('discount_message_'. $cart_item->id))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('discount_message_'. $cart_item->id) }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="form-group">
                    <label for="discount_type{{ $cart_item->id }}">Tipe Diskon <span class="text-danger">*</span></label>
                    <select wire:model.live="discount_type.{{ $cart_item->id }}" id="discount_type{{ $cart_item->id }}" class="form-control" required>
                        <option value="fixed">Tetap</option>
                        <option value="percentage">Persentase</option>
                    </select>
                </div>
                <div class="form-group">
                    @if(isset($discount_type[$cart_item->id]) && $discount_type[$cart_item->id] == 'percentage')
                        <label for="item_discount{{ $cart_item->id }}">Diskon (%) <span class="text-danger">*</span></label>
                        <input wire:model.live="item_discount.{{ $cart_item->id }}" id="item_discount{{ $cart_item->id }}" type="number" class="form-control" min="0" max="100">
                    @else
                        <label for="item_discount{{ $cart_item->id }}">Diskon (Jumlah Tetap) <span class="text-danger">*</span></label>
                        <input wire:model.live="item_discount.{{ $cart_item->id }}" id="item_discount{{ $cart_item->id }}" type="number" class="form-control" min="0">
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button wire:click.prevent="setProductDiscount('{{ $cart_item->rowId }}', '{{ $cart_item->id }}')" type="button" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>