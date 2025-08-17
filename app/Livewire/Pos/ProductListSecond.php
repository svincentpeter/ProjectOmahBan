<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use Gloudemans\Shoppingcart\Facades\Cart;
use Modules\Product\Entities\ProductSecond;

class ProductListSecond extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $query = '';
    public string $cart_instance = 'sale';
    public function updatingQuery(): void
    {
        $this->resetPage();
    }

    // Backward-compat untuk pemanggilan lama
    public function addToCart($id): void
    {
        $this->addSecondToCart($id);
    }

    public function addSecondToCart($secondId): void
    {
        $second = ProductSecond::findOrFail($secondId);

        // Tolak jika status tidak ready/available/tersedia (jika kolom status ada)
        if (isset($second->status) && !in_array(strtolower($second->status), ['ready', 'available', 'tersedia'])) {
            $this->dispatch('notify', type: 'warning', message: 'Barang sudah terjual / tidak tersedia.');
            return;
        }

        // Cegah duplikat di Cart
        $dup = Cart::instance($this->cart_instance)->search(function ($cartItem) use ($second) {
            return ($cartItem->id == $second->id)
                && (($cartItem->options['source_type'] ?? null) === 'second');
        });

        if ($dup->isNotEmpty()) {
            $this->dispatch('notify', type: 'warning', message: 'Item bekas ini sudah di keranjang.');
            return;
        }

        Cart::instance($this->cart_instance)->add([
            'id'      => $second->id,
            'name'    => $second->name,
            'qty'     => 1,
            'price'   => (int) ($second->selling_price ?? $second->price ?? 0),
            'weight'  => 0,
            'options' => [
                'source_type' => 'second',
                'code'        => $second->unique_code ?? null,
                'status'      => $second->status ?? 'available',
                'original_id' => $second->id,            // <-- baru
            ],
        ]);


        // Sinkronkan UI keranjang
        if (method_exists($this, 'refreshCart')) {
            $this->refreshCart();
        } else {
            $this->dispatch('cartUpdated'); // Livewire v3
        }

        $this->dispatch('notify', type: 'success', message: 'Item bekas ditambahkan ke keranjang.');
    }

    public function render()
    {
        $products = ProductSecond::query()
            ->when(strlen($this->query) > 0, function ($q) {
                $s = trim($this->query);
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('unique_code', 'like', "%{$s}%")
                        ->orWhere('sku', 'like', "%{$s}%");
                });
            })
            ->latest('id')
            ->paginate(9);

        return view('livewire.pos.product-list-second', [
            'products' => $products,
        ]);
    }
}
