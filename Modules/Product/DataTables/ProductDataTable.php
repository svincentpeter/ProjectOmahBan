<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\Product;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('product::products.partials.actions', compact('data'))->render();
            })
            ->addColumn('product_price', fn($d) => format_currency($d->product_price))
            ->addColumn('product_cost', fn($d) => format_currency($d->product_cost))
            ->addColumn('stok_sisa', function ($d) {
                $qty = $d->product_quantity;
                $alert = $d->product_stock_alert;
                $unit = $d->product_unit;
                
                if ($qty <= 0) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">' . $qty . ' ' . $unit . '</span>';
                } elseif ($qty <= $alert) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">' . $qty . ' ' . $unit . '</span>';
                } else {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">' . $qty . ' ' . $unit . '</span>';
                }
            })
            ->addColumn('merk', function ($d) {
                if ($d->brand) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">' . $d->brand->name . '</span>';
                }
                return '<span class="text-zinc-400">-</span>';
            })
            ->addColumn('image', function ($data) {
                $imageUrl = $data->getFirstMediaUrl('images', 'thumb');
                if ($imageUrl) {
                    return '<img src="' . $imageUrl . '" width="50" height="50" class="rounded-lg shadow-sm">';
                }
                return '<img src="/images/fallback_product_image.png" width="50" height="50" class="rounded-lg opacity-50">';
            })
            ->addColumn('harga_jual_badge', function ($d) {
                return '<span class="font-bold text-emerald-600">' . format_currency($d->product_price) . '</span>';
            })
            ->filterColumn('merk', function($query, $keyword) {
                $query->whereHas('brand', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['action', 'image', 'stok_sisa', 'merk', 'harga_jual_badge']);
    }

    public function query(Product $model)
    {
        $query = $model->newQuery()->with(['category', 'brand']);
        
        // Filter by category
        if (request()->has('category_id') && request('category_id') != '') {
            $query->where('category_id', request('category_id'));
        }
        
        // Filter by quick filter
        if (request()->has('quick_filter') && request('quick_filter') != 'all') {
            $filter = request('quick_filter');
            switch ($filter) {
                case 'low-stock':
                    $query->whereColumn('product_quantity', '<=', 'product_stock_alert')
                          ->where('product_quantity', '>', 0);
                    break;
                case 'out-of-stock':
                    $query->where('product_quantity', '<=', 0);
                    break;
            }
        }
        
        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('products-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->parameters([
                'responsive' => true,
                'autoWidth' => false
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('product_name')->title('Nama Barang')->className('align-middle font-semibold'),

            Column::computed('merk')->title('Merk')->className('text-center align-middle'),

            Column::make('product_year')->title('Tahun')->className('text-center align-middle'),

            Column::make('product_size')->title('Ukuran')->className('text-center align-middle'),

            Column::make('ring')->title('Ring')->className('text-center align-middle'),

            Column::computed('product_cost')->title('Modal')->className('text-center align-middle'),

            Column::computed('harga_jual_badge')->title('Harga Jual')->className('text-center align-middle'),

            Column::computed('stok_sisa')->title('Stok')->className('text-center align-middle'),

            Column::computed('action')->title('Aksi')->exportable(false)->printable(false)->width(80)->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Daftar_Produk_' . date('YmdHis');
    }
}
