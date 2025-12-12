<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\Brand;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BrandsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', 'product::brands.partials.actions') // Using partial for consistent action buttons
            ->editColumn('name', function ($brand) {
                return '<div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md">
                                <i class="bi bi-bookmark-star"></i>
                            </div>
                            <span class="font-bold text-black">' . e($brand->name) . '</span>
                        </div>';
            })
            ->rawColumns(['name', 'action']);
    }

    public function query(Brand $model)
    {
        return $model->newQuery()->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('brands-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt<"p-5 flex items-center justify-between"lp>')
            ->orderBy(1)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false, 
                'drawCallback' => 'function() {
                    // Re-bind events if needed, but delegating to document is better
                }'
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->addClass('text-center font-bold text-zinc-500')->width(60),
            Column::make('name')->title('Nama Merek')->addClass('align-middle'),
            Column::computed('action')->title('Aksi')->addClass('text-center align-middle')->width(150),
        ];
    }

    protected function filename(): string
    {
        return 'Brands_' . date('YmdHis');
    }
}
