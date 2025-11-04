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
            ->editColumn('standard_price', fn ($d) => format_currency($d->standard_price))
            ->editColumn('category', function ($d) {
                $map = ['service' => 'Service', 'goods' => 'Goods', 'custom' => 'Custom'];
                return '<span class="badge bg-light text-dark border">' . ($map[$d->category] ?? $d->category) . '</span>';
            })
            ->editColumn('description', function ($d) {
                if (!$d->description) {
                    return '<span class="text-muted">-</span>';
                }
                return e(Str::limit($d->description, 60));
            })
            ->addColumn('status_text', fn ($d) => $d->status
                ? '<span class="badge badge-success"><i class="cil-check-circle me-1"></i>Aktif</span>'
                : '<span class="badge badge-warning"><i class="cil-x-circle me-1"></i>Nonaktif</span>')
            ->addColumn('action', fn ($d) => view('product::service-masters.partials.actions', compact('d'))->render())
            ->rawColumns(['category', 'status_text', 'description', 'action']);
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
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'autoWidth'  => false,
                'searchDelay'=> 500,
                'language'   => [
                    'search' => '',
                    'searchPlaceholder' => 'Cari nama, kategori, deskripsi…',
                ],
                'ajax' => [
                    'url'  => route('service-masters.index'),
                    'data' => 'function(d){ d.quick = window._svcQuick || "all"; }',
                ],
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
