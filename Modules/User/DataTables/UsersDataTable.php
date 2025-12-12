<?php

namespace Modules\User\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('role', function ($data) {
                return view('user::users.partials.roles', [
                    'roles' => $data->getRoleNames()
                ]);
            })
            ->addColumn('action', function ($data) {
                return view('user::users.partials.actions', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->is_active == 1) {
                    $html = '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Aktif</span>';
                } else {
                    $html = '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Nonaktif</span>';
                }

                return $html;
            })
            ->addColumn('image', function ($data) {
                $url = $data->getFirstMediaUrl('avatars');

                return '<img src="' . $url . '" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-700"/>';
            })
            // TAMBAHKAN BARIS INI
            ->rawColumns(['image', 'status', 'action', 'role']);
    }

    public function query(User $model) {
        return $model->newQuery()->where('id', '!=', auth()->id());
    }

    public function html() {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex items-center space-x-2"lB>f>rt<"flex flex-col md:flex-row justify-between items-center mt-4"ip>')
            ->orderBy(6)
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel me-1"></i> Excel')
                    ->addClass('text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700'),
                Button::make('print')
                    ->text('<i class="bi bi-printer me-1"></i> Print')
                    ->addClass('text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700'),

                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat me-1"></i> Reload')
                    ->addClass('text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700')
                    ->action('function(e, dt, node, config){ dt.ajax.reload(); }')
            );

    }

    protected function getColumns() {
        return [
            Column::computed('image')
                ->title('Gambar')
                ->className('text-center align-middle'),

            Column::make('name')
                ->title('Nama')
                ->className('text-center align-middle'),

            Column::make('email')
                ->title('Email')
                ->className('text-center align-middle'),

            Column::computed('role')
                ->title('Peran')
                ->className('text-center align-middle'),

            Column::computed('status')
                ->title('Status')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Users_' . date('YmdHis');
    }
}