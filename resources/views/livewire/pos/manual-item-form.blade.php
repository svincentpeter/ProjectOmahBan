<div>
    <div class="form-group">
        <label for="manual_item_name">Nama Jasa / Item</label>
        <input wire:model.defer="name" type="text" class="form-control" id="manual_item_name">
        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label for="manual_item_price">Harga</label>
        <input wire:model.defer="price" type="number" class="form-control" id="manual_item_price">
         @error('price') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <button wire:click="addToCart" class="btn btn-primary">Tambah ke Keranjang</button>
</div>