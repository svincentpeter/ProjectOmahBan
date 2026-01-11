<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\ProductSecond;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductSecondDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('partials.datatable-actions', [
                    'id' => $data->id,
                    'showRoute' => route('products_second.show', $data->id),
                    'editRoute' => route('products_second.edit', $data->id),
                    'deleteRoute' => route('products_second.destroy', $data->id),
                    'itemName' => $data->name,
                    'showPermission' => 'show_products',
                    'editPermission' => 'edit_products',
                    'deletePermission' => 'delete_products',
                ])->render();
            })
            ->editColumn('name', function ($data) {
                return '<span class="font-bold text-slate-700 hover:text-blue-600 transition-colors cursor-pointer">' . $data->name . '</span>';
            })
            ->editColumn('brand.name', function ($data) {
                if ($data->brand) {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">' . $data->brand->name . '</span>';
                }
                return '<span class="text-zinc-400">-</span>';
            })
            ->editColumn('product_year', function ($data) {
                return '<span class="font-medium text-slate-500">' . $data->product_year . '</span>';
            })
            ->addColumn('purchase_price', function ($data) {
                return format_currency($data->purchase_price);
            })
            ->addColumn('selling_price', function ($data) {
                return format_currency($data->selling_price);
            })
            ->addColumn('status', function ($data) {
                if ($data->status == 'available') {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                        <i class="bi bi-check-circle me-1"></i> Tersedia
                    </span>';
                } else {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-600">
                        <i class="bi bi-x-circle me-1"></i> Terjual
                    </span>';
                }
            })
            ->editColumn('condition_notes', function($data) {
                return '<span class="text-xs text-slate-500 italic">' . \Illuminate\Support\Str::limit($data->condition_notes, 50) . '</span>';
            })
            ->rawColumns(['action', 'status', 'name', 'brand.name', 'product_year', 'condition_notes']);
    }

    public function query(ProductSecond $model)
    {
        $query = $model->newQuery()->with(['category', 'brand']);

        if (request()->has('brand_id') && request('brand_id') != '') {
            $query->where('brand_id', request('brand_id'));
        }

        if (request()->has('status') && request('status') != '' && request('status') != 'all') {
            $query->where('status', request('status'));
        }

        // Smart Filter: size (ukuran ban)
        if (request()->has('size') && request('size') != '') {
            $query->where('size', 'like', '%' . request('size') . '%');
        }

        // Smart Filter: ring (ring ban) - handle both "R15" and "15" formats
        if (request()->has('ring') && request('ring') != '') {
            $ringValue = request('ring');
            // Remove leading "R" or "r" if present
            $ringValue = preg_replace('/^R/i', '', $ringValue);
            $query->where('ring', 'like', '%' . $ringValue . '%');
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('product-second-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'drawCallback' => 'function() { 
                    if (typeof initFlowbite === "function") {
                        initFlowbite();
                    }
                }'
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('created_at')->title('Tanggal')->visible(false),
            Column::make('unique_code')->title('Kode')->visible(false),
            // Image column removed
            Column::make('name')->title('Nama Barang')->addClass('font-semibold'),
            Column::make('brand.name')->title('Merek')->addClass('text-center'),
            Column::make('product_year')->title('Tahun')->addClass('text-center'),
            Column::make('size')->title('Ukuran'),
            Column::make('ring')->title('Ring')->addClass('text-center'),
            Column::make('purchase_price')->title('Modal')->addClass('text-right text-slate-600'),
            Column::make('selling_price')->title('Harga Jual')->addClass('text-right font-bold text-emerald-600'),
            Column::make('status')->title('Status')->addClass('text-center'),
            Column::make('condition_notes')->title('Deskripsi')->width(200),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'ProductSecond_' . date('YmdHis');
    }
}
