<?php

namespace Modules\Product\DataTables;

use Illuminate\Support\Str;
use Modules\Product\Entities\ServiceMaster;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ServiceMasterDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('standard_price', function ($d) {
                return '<span class="font-bold text-emerald-600">' . format_currency($d->standard_price) . '</span>';
            })
            ->editColumn('category', function ($d) {
                $map = [
                    'service' => '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Service</span>',
                    'goods' => '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Goods</span>',
                    'custom' => '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Custom</span>',
                ];
                return $map[$d->category] ?? $d->category;
            })
            ->editColumn('description', function ($d) {
                if (!$d->description) {
                    return '<span class="text-zinc-400">-</span>';
                }
                return e(Str::limit($d->description, 60));
            })
            ->addColumn('status_text', function ($d) {
                return $d->status
                    ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Aktif</span>'
                    : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-600">Nonaktif</span>';
            })
            ->addColumn('action', fn ($d) => view('product::service-masters.partials.actions', compact('d'))->render())
            ->rawColumns(['standard_price', 'category', 'status_text', 'description', 'action']);
    }

    public function query(ServiceMaster $model)
    {
        $q = $model->newQuery();

        // quick filter: all|active|inactive
        $quick = request('quick', 'all');
        if ($quick === 'active') {
            $q->where('status', 1);
        } elseif ($quick === 'inactive') {
            $q->where('status', 0);
        }

        return $q->select(['id', 'service_name', 'standard_price', 'category', 'description', 'status']);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('service-masters-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'order' => [[1, 'asc']],
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->width(60)->addClass('text-center')->orderable(false)->searchable(false),
            Column::make('service_name')->title('Nama Jasa'),
            Column::make('standard_price')->title('Harga Standar')->addClass('text-end')->width(160),
            Column::make('category')->title('Kategori')->width(140)->orderable(false)->searchable(false),
            Column::make('description')->title('Deskripsi')->orderable(false),
            Column::computed('status_text')->title('Status')->width(120)->orderable(false)->searchable(false),
            Column::computed('action')->title('Aksi')->width(140)->addClass('text-center')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'ServiceMasters_' . date('YmdHis');
    }
}
