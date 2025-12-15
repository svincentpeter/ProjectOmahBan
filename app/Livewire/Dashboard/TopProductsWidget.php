<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopProductsWidget extends Component
{
    public $products = [];
    public $period = 'month'; // 'week', 'month', 'year'
    public $limit = 10;

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $startDate = match($this->period) {
            'week' => Carbon::now()->subDays(7),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $this->products = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.date', '>=', $startDate)
            ->whereNull('sales.deleted_at')
            ->select([
                'sale_details.product_name',
                'sale_details.product_code',
                DB::raw('SUM(sale_details.quantity) as total_qty'),
                DB::raw('SUM(sale_details.sub_total) as total_revenue'),
            ])
            ->groupBy('sale_details.product_name', 'sale_details.product_code')
            ->orderByDesc('total_qty')
            ->limit($this->limit)
            ->get()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $item->product_name ?? 'Unknown',
                    'code' => $item->product_code ?? '-',
                    'qty' => (int) $item->total_qty,
                    'revenue' => (float) $item->total_revenue,
                ];
            })
            ->toArray();
    }

    public function setPeriod($period)
    {
        $this->period = $period;
        $this->loadProducts();
    }

    public function render()
    {
        return view('livewire.dashboard.top-products-widget');
    }
}
