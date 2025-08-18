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
public int $perPage = 9;
protected $queryString = ['query' => ['except' => '']];

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

    // Hanya boleh kalau available
    if (strtolower((string)$second->status) !== 'available') {
        $this->dispatch('notify', type: 'warning', message: 'Barang bekas ini sudah terjual / tidak tersedia.');
        return;
    }

    // Cegah duplikat (unit unik)
    $dup = Cart::instance($this->cart_instance)->search(function ($cartItem) use ($second) {
        return ($cartItem->id == $second->id) && (($cartItem->options['source_type'] ?? null) === 'second');
    });

    if ($dup->isNotEmpty()) {
        $this->dispatch('notify', type: 'warning', message: 'Item bekas ini sudah ada di keranjang.');
        return;
    }

    Cart::instance($this->cart_instance)->add([
        'id'      => $second->id,
        'name'    => $second->name,
        'qty'     => 1, // unit unik
        'price'   => (int)$second->selling_price,
        'weight'  => 1,
        'options' => [
            'source_type'  => 'second',
            'code'         => $second->unique_code,
            'unit_price'   => (int)$second->selling_price,
            'hpp'          => (int)($second->purchase_price ?? 0),
            'size'         => $second->size,
            'ring'         => $second->ring,
            'product_year' => $second->product_year,
        ],
    ]);

    $this->dispatch('notify', type: 'success', message: 'Item bekas masuk keranjang.');
    $this->dispatch('cartUpdated');
}


    public function render()
{
    $products = ProductSecond::query()
        ->when(strlen($this->query) > 0, function ($q) {
            $s = trim($this->query);
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                   ->orWhere('unique_code', 'like', "%{$s}%")
                   ->orWhere('size', 'like', "%{$s}%")
                   ->orWhere('ring', 'like', "%{$s}%");
                if (ctype_digit($s)) {
                    $qq->orWhere('product_year', (int)$s);
                }
            });
        })
        // Tampilkan yang available dulu
        ->orderByRaw("CASE WHEN status='available' THEN 0 ELSE 1 END")
        ->latest('id')
        ->paginate($this->perPage);

    return view('livewire.pos.product-list-second', [
        'products' => $products,
    ]);
}

}
