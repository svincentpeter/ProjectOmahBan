<?php

namespace Modules\Setting\DataTables;

use Modules\Setting\Entities\Unit;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UnitsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('setting::units.partials.actions', compact('data'));
            })
            ->editColumn('operation_value', function ($data) {
                return '<span class="font-extrabold text-black dark:text-white">' . $data->operation_value . '</span>';
            })
            ->editColumn('short_name', function ($data) {
                return '<span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">' . $data->short_name . '</span>';
            })
            ->editColumn('operator', function ($data) {
                return '<span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">' . $data->operator . '</span>';
            })
            ->rawColumns(['short_name', 'operator', 'operation_value', 'action']);
    }

    public function query(Unit $model)
    {
        return $model->newQuery();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('units-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1);
    }

    protected function getColumns()
    {
        return [
            Column::make('id')->visible(false),
            Column::make('name')->title('Nama Satuan')->addClass('font-bold align-middle'),
            Column::make('short_name')->title('Singkatan')->addClass('text-center align-middle'),
            Column::make('operator')->title('Operator')->addClass('text-center align-middle'),
            Column::make('operation_value')->title('Nilai Operasi')->addClass('text-center align-middle'),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Units_' . date('YmdHis');
    }
}
