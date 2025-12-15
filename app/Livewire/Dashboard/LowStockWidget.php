<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Modules\Product\Entities\Product;

class LowStockWidget extends Component
{
    public $products = [];
    public $showAll = false;
    public $count = 0;

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $query = Product::query()
            ->whereNull('deleted_at')
            ->whereNotNull('product_stock_alert')
            ->where('product_stock_alert', '>', 0)
            ->whereColumn('product_quantity', '<=', 'product_stock_alert')
            ->orderByRaw('product_quantity - product_stock_alert ASC');

        $this->count = $query->count();
        
        $this->products = $query
            ->limit($this->showAll ? 50 : 5)
            ->get(['id', 'product_code', 'product_name', 'product_quantity', 'product_stock_alert'])
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'code' => $p->product_code ?? '-',
                    'name' => $p->product_name ?? $p->name ?? 'Unknown',
                    'qty' => $p->product_quantity,
                    'alert' => $p->product_stock_alert,
                    'critical' => $p->product_quantity <= 0,
                    'percentage' => $p->product_stock_alert > 0 
                        ? round(($p->product_quantity / $p->product_stock_alert) * 100) 
                        : 0,
                ];
            })
            ->toArray();
    }

    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
        $this->loadProducts();
    }

    public function render()
    {
        return view('livewire.dashboard.low-stock-widget');
    }
}
