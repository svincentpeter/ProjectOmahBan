<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Modules\Product\Entities\ProductSecond; // Import model produk bekas kita
use Livewire\WithPagination;

class ProductListSecond extends Component
{
    use WithPagination;

    public $query = '';

    public function updatingQuery()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = ProductSecond::where('status', 'available')
            ->where(function ($queryBuilder) {
                $queryBuilder->where('name', 'like', '%' . $this->query . '%')
                             ->orWhere('unique_code', 'like', '%' . $this->query . '%');
            })
            ->paginate(9);

        return view('livewire.pos.product-list-second', compact('products'));
    }

    public function addToCart($productId)
    {
        $product = ProductSecond::findOrFail($productId);

        \Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->selling_price,
            'quantity' => 1,
            'attributes' => [
                'source_type' => 'second', // INI KUNCINYA!
                'discount' => 0,
                'discount_type' => 'fixed',
                'tax' => 0,
            ]
        ]);

        $this->emit('cartUpdated'); // Memberi sinyal ke komponen lain bahwa cart berubah
        $this->dispatchBrowserEvent('showSuccess', ['message' => 'Produk bekas ditambahkan!']);
    }
}