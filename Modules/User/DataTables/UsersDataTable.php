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
        $query = $model->newQuery()->where('id', '!=', auth()->id());

        if (request()->has('status') && request('status') !== 'all') {
            // Handle 'inactive' string mapping to 0
            $status = request('status') === 'inactive' ? 0 : request('status');
            $query->where('is_active', $status);
        }

        if (request()->has('role') && request('role') !== 'all') {
            $query->whereHas('roles', function($q) {
                $q->where('name', request('role'));
            });
        }

        return $query;
    }

    public function html() {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax(request()->fullUrl()) // Put Full URL to persist Search Params
            ->orderBy(6);
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