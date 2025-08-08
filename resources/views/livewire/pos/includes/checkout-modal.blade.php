<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            {{-- Alert Session (jika ada notifikasi sukses/error dari session) --}}
            @if(session()->has('swal-success'))
                <div class="alert alert-success">
                    {{ session('swal-success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="text-center">
                            <th class="align-middle">Produk</th>
                            <th class="align-middle">Harga</th>
                            <th class="align-middle">Diskon</th>
                            <th class="align-middle">Jumlah</th>
                            <th class="align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if($cart_items->isNotEmpty())
                        @foreach($cart_items as $cart_item)
                            <tr>
                                <td class="align-middle">
                                    {{ $cart_item->name }} <br>
                                    <span class="badge badge-success">
                                        {{ $cart_item->options->code ?? '' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">{{ format_currency($cart_item->price) }}</td>
                                
                                {{-- Tombol Diskon: Panggil modal diskon yang sesuai --}}
                                <td class="align-middle text-center">
                                    <span role="button"
                                          class="badge badge-warning"
                                          data-toggle="modal"
                                          data-target="#discountModal{{ $cart_item->id }}">
                                        Atur Diskon
                                    </span>
                                </td>

                                <td class="align-middle" style="width: 120px;">
                                    @if($cart_item->options->source_type == 'new')
                                        <div class="input-group">
                                            <input wire:model.live="quantity.{{ $cart_item->id }}"
                                                   type="number"
                                                   min="1"
                                                   max="{{ $cart_item->options->stock ?? 999 }}"
                                                   class="form-control">
                                            <div class="input-group-append">
                                                <button wire:click="updateQuantity('{{ $cart_item->rowId }}', '{{ $cart_item->id }}')"
                                                        class="btn btn-info">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <input type="number" class="form-control text-center" value="{{ $cart_item->qty }}" readonly>
                                    @endif
                                </td>

                                <td class="align-middle text-center">
                                    <a href="#" wire:click.prevent="removeItem('{{ $cart_item->rowId }}')">
                                        <i class="bi bi-x-circle font-2xl text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            {{-- Modal Diskon per Item --}}
                            @include('livewire.includes.product-cart-modal', ['cart_item' => $cart_item])
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">
                                <span class="text-danger">Silakan cari & pilih produk!</span>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>

            {{-- Ringkasan Grand Total & Tombol Lanjutkan Checkout --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <h4>
                        Grand Total: <span class="text-primary">{{ format_currency($total_amount) }}</span>
                    </h4>
                </div>
                <div>
                    <button class="btn btn-success"
                        wire:click="proceed"
                        @if($cart_items->isEmpty()) disabled @endif>
                        <i class="bi bi-cart-check"></i> Lanjutkan Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi & Pembayaran --}}
    @include('livewire.pos.includes.checkout-modal')
</div>
