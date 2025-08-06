<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Product\Entities\Product;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'selectedCategory' => 'categoryChanged',
        'showCount'        => 'showCountChanged'
    ];

    public $categories;
    public $category_id;
    public $limit = 9;

    public function mount($categories) {
        $this->categories = $categories;
        $this->category_id = '';
    }

    public function render() {
        return view('livewire.pos.product-list', [
            'products' => Product::when($this->category_id, function ($query) {
                return $query->where('category_id', $this->category_id);
            })
            ->paginate($this->limit)
        ]);
    }

    public function categoryChanged($category_id) {
        $this->category_id = $category_id;
        $this->resetPage();
    }

    public function showCountChanged($value) {
        $this->limit = $value;
        $this->resetPage();
    }

    public function selectProduct($product) {
        $this->dispatch('productSelected', $product);
    }

    // === Tambahkan/replace method di bawah ini! ===
    public function addToCart($productId)
{
    $product = Product::findOrFail($productId);

    \Cart::add([
        'id'       => $product->id,
        'name'     => $product->product_name,
        'price'    => $product->product_price,
        'quantity' => 1,
        'attributes' => [
            'source_type' => 'new', // <-- CUKUP TAMBAHKAN BARIS INI!
            'discount' => 0,
            'discount_type' => 'fixed',
            'tax' => 0
        ]
    ]);

    $this->emit('cartUpdated');
    $this->dispatchBrowserEvent('showSuccess', ['message' => 'Product Added To Cart!']);
}
}
