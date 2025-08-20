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

    public function mount($categories)
    {
        $this->categories = $categories;
        $this->category_id = '';
    }

    public function render()
    {
        return view('livewire.pos.product-list', [
            'products' => Product::when($this->category_id, function ($query) {
                return $query->where('category_id', $this->category_id);
            })
                ->paginate($this->limit)
        ]);
    }

    public function categoryChanged($category_id)
    {
        $this->category_id = $category_id;
        $this->resetPage();
    }

    public function showCountChanged($value)
    {
        $this->limit = $value;
        $this->resetPage();
    }

    public function selectProduct(int $id): void
    {
        $p = Product::findOrFail($id);

        $payload = [
            'id'               => $p->id,
            'product_name'     => (string) $p->product_name,
            'product_code'     => (string) $p->product_code,
            'product_price'    => (int) $p->product_price,         // harga jual
            'product_cost'     => (int) ($p->product_cost ?? 0),    // HPP
            'product_order_tax' => (int) ($p->product_order_tax ?? 0),
            'product_tax_type' => $p->product_tax_type ?? null,     // 'inclusive' / 'exclusive' / null
            'product_quantity' => (int) ($p->product_quantity ?? 0),
            'source_type'      => 'new',
        ];

        // HANYA dispatch event ke Checkout.
        // Notifikasi sukses/duplikat akan ditangani eksklusif oleh Checkout::productSelected()
        $this->dispatch('productSelected', $payload)
     ->to(\App\Livewire\Pos\Checkout::class);
    }



    // === Tambahkan/replace method di bawah ini! ===
    public function addToCart($productId): void
    {
        // Hindari duplikasi logic: cukup arahkan ke selectProduct()
        $this->selectProduct((int) $productId);
    }
}
