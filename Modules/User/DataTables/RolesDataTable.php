<?php

namespace Modules\User\DataTables;

use Spatie\Permission\Models\Role;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RolesDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('user::roles.partials.actions', compact('data'));
            })
            ->addColumn('permissions', function ($data) {
                return view('user::roles.partials.permissions', [
                    'data' => $data
                ]);
            });
    }

    public function query(Role $model) {
        return $model->newQuery()->with(['permissions' => function ($query) {
            $query->select('name')->take(10)->get();
        }])->where('name', '!=', 'Super Admin');
    }

    public function html() {
        return $this->builder()
            ->setTableId('roles-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex items-center space-x-2"lB>f>rt<"flex flex-col md:flex-row justify-between items-center mt-4"ip>')
            ->orderBy(4)
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel')
                    ->addClass('px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800'),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> Print')
                    ->addClass('px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800'),

                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> Reload')
                    ->addClass('px-3 py-2 text-sm font-medium text-center inline-flex items-center text-gray-900 bg-white border border-gray-200 rounded-lg focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700')
                    ->action('function(e, dt, node, config){ dt.ajax.reload(); }')
            );
    }

    protected function getColumns() {
        return [
            Column::make('id')
                ->title('ID')
                ->addClass('text-center align-middle whitespace-nowrap'),

            Column::make('name')
                ->title('Nama Peran')
                ->addClass('text-center align-middle font-semibold'),

            Column::computed('permissions')
                ->title('Hak Akses (Contoh)')
                ->addClass('text-center align-middle')
                ->width('700px'),

            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center align-middle whitespace-nowrap'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Roles_' . date('YmdHis');
    }
}