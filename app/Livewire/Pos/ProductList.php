<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Brand; // Tambahkan import ini

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'selectedCategory' => 'categoryChanged',
        'selectedBrand' => 'brandChanged', // Tambahkan listener brand
        'showCount' => 'showCountChanged'
    ];

    public $categories;
    public $brands; // Tambahkan property brands
    public $category_id;
    public $brand_id; // Tambahkan property brand_id
    public $search = ''; // Search Term
    public $limit = 9;

    public function mount($categories)
    {
        $this->categories = $categories;
        $this->brands = Brand::orderBy('name', 'asc')->get(); // Load brands di sini
        $this->category_id = '';
        $this->brand_id = ''; // Inisialisasi brand_id
    }

    public function render()
    {
        return view('livewire.pos.product-list', [
            'products' => Product::with(['brand', 'media', 'category'])
                ->when($this->category_id, function ($query) {
                    return $query->where('category_id', $this->category_id);
                })
                ->when($this->brand_id, function ($query) { 
                    return $query->where('brand_id', $this->brand_id);
                })
                ->when($this->search, function ($query) {
                    return $query->where(function ($subQuery) {
                        $subQuery->where('product_name', 'like', '%'.$this->search.'%')
                                 ->orWhere('product_code', 'like', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->limit)
        ]);
    }

    public function categoryChanged($category_id)
    {
        $this->category_id = $category_id;
        $this->resetPage();
    }

    public function brandChanged($brand_id) // Method baru untuk brand
    {
        $this->brand_id = $brand_id;
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
            'id' => $p->id,
            'product_name' => (string) $p->product_name,
            'product_code' => (string) $p->product_code,
            'product_price' => (int) $p->product_price,
            'product_cost' => (int) ($p->product_cost ?? 0),
            'product_order_tax' => (int) ($p->product_order_tax ?? 0),
            'product_tax_type' => $p->product_tax_type ?? null,
            'product_quantity' => (int) ($p->product_quantity ?? 0),
            'source_type' => 'new',
        ];

        $this->dispatch('productSelected', $payload)
            ->to(\App\Livewire\Pos\Checkout::class);
    }

    public function addToCart($productId): void
    {
        $this->selectProduct((int) $productId);
    }
}
