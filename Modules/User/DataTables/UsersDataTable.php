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
                    $html = '<span class="badge badge-success">Aktif</span>';
                } else {
                    $html = '<span class="badge badge-warning">Nonaktif</span>';
                }

                return $html;
            })
            ->addColumn('image', function ($data) {
                $url = $data->getFirstMediaUrl('avatars');

                return '<img src="' . $url . '" style="width:50px;height:50px;" class="img-thumbnail rounded-circle"/>';
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
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(6)
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')
                    ->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> Reload')
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