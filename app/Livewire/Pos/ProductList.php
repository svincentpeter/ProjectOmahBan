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

    public function selectProduct($product)
{
    // Langsung kirim data produk (yang sudah dalam bentuk array) ke komponen checkout
    $this->dispatch('productSelected', product: $product)->to('pos.checkout');
}

    // === Tambahkan/replace method di bawah ini! ===
    public function addToCart($productId)
{
    $p = Product::findOrFail($productId);

    // Susun payload sesuai yang dibutuhkan Checkout::productSelected()
    $payload = [
        'id'                 => (int) $p->id,
        'product_name'       => (string) $p->product_name,
        'product_code'       => $p->product_code,
        'product_price'      => (int) $p->product_price,
        'product_cost'       => (int) $p->product_cost,
        'product_quantity'   => (int) $p->product_quantity,
        'product_unit'       => $p->product_unit,
        'product_order_tax'  => (int) ($p->product_order_tax ?? 0),
        'product_tax_type'   => (int) ($p->product_tax_type ?? 0), // 0:none, 1:inclusive, 2:exclusive
    ];

    // Kirim ke komponen Checkout agar keranjang tunggal via Gloudemans\Shoppingcart
    $this->dispatch('productSelected', $payload)
         ->to(\App\Livewire\Pos\Checkout::class);

    $this->dispatchBrowserEvent('showSuccess', ['message' => 'Produk berhasil ditambahkan!']);
}
}
