<?php

namespace Modules\People\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\People\Entities\Supplier;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SuppliersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            // Kolom Supplier Name dengan link ke detail
            ->editColumn('supplier_name', function ($data) {
                return '<a href="' . route('suppliers.show', $data->id) . '" class="text-blue-600 hover:text-blue-800 font-bold hover:underline">' . e($data->supplier_name) . '</a>';
            })

            // Format email dengan icon
            ->editColumn('supplier_email', function ($data) {
                return '<div class="flex items-center text-zinc-600"><i class="bi bi-envelope me-2 text-zinc-400"></i> ' . e($data->supplier_email) . '</div>';
            })

            // Format phone dengan icon
            ->editColumn('supplier_phone', function ($data) {
                return '<div class="flex items-center text-zinc-600"><i class="bi bi-telephone me-2 text-zinc-400"></i> ' . e($data->supplier_phone) . '</div>';
            })

            // Kolom City dengan badge
            ->editColumn('city', function ($data) {
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">' . e($data->city) . '</span>';
            })

            // Kolom total pembelian (computed column)
            ->addColumn('total_purchases', function ($data) {
                $count = $data->purchases_count ?? 0;

                if ($count > 0) {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">' . $count . ' transaksi</span>';
                }

                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600">Belum ada</span>';
            })

            // Kolom total nilai pembelian (computed column)
            ->addColumn('total_amount', function ($data) {
                $total = $data->purchases_sum_total_amount ?? 0;

                if ($total > 0) {
                    return '<strong class="text-emerald-600">' . format_currency($total) . '</strong>';
                }

                return '<span class="text-zinc-400">-</span>';
            })

            // Status aktif (berdasarkan transaksi 6 bulan terakhir)
            ->addColumn('status', function ($data) {
                $hasRecentPurchase = \Modules\Purchase\Entities\Purchase::where('supplier_id', $data->id)
                    ->where('date', '>=', now()->subMonths(6))
                    ->exists();

                if ($hasRecentPurchase) {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Aktif</span>';
                }

                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-zinc-100 text-zinc-500">Tidak Aktif</span>';
            })

            // Format tanggal dibuat
            ->editColumn('created_at', function ($data) {
                return $data->created_at ? $data->created_at->format('d M Y') : '-';
            })

            // Kolom action buttons
            ->addColumn('action', function ($data) {
                return view('people::suppliers.partials.actions', compact('data'));
            })

            // Set kolom yang mengandung HTML (jangan di-escape)
            ->rawColumns([
                'supplier_name',
                'supplier_email',
                'supplier_phone',
                'city',
                'total_purchases',
                'total_amount',
                'status',
                'action',
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Modules\People\Entities\Supplier $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Supplier $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->withCount('purchases')
            ->withSum('purchases', 'total_amount')
            ->latest('created_at');

        // Filter by city
        if (request()->has('city') && request('city') != '') {
            $query->where('city', request('city'));
        }

        // Filter by country
        if (request()->has('country') && request('country') != '') {
            $query->where('country', request('country'));
        }

        // Filter by status (active/inactive)
        if (request()->has('status') && request('status') != '') {
            $status = request('status');

            if ($status === 'active') {
                $query->whereHas('purchases', function ($q) {
                    $q->where('date', '>=', now()->subMonths(6));
                });
            } elseif ($status === 'inactive') {
                $query->whereDoesntHave('purchases', function ($q) {
                    $q->where('date', '>=', now()->subMonths(6));
                });
            }
        }

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('suppliers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'processing' => true,
                'serverSide' => true,
            ]); 
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('#')
                ->searchable(false)
                ->orderable(false)
                ->width(30)
                ->addClass('text-center'),

            Column::make('supplier_name')
                ->title('Nama Supplier')
                ->searchable(true)
                ->orderable(true)
                ->addClass('align-middle'),

            Column::make('supplier_email')
                ->title('Email')
                ->searchable(true)
                ->orderable(true)
                ->addClass('align-middle'),

            Column::make('supplier_phone')
                ->title('No. Telepon')
                ->searchable(true)
                ->orderable(false)
                ->addClass('align-middle'),

            Column::make('city')
                ->title('Kota')
                ->searchable(true)
                ->orderable(true)
                ->addClass('align-middle text-center'),

            Column::computed('total_purchases')
                ->title('Total Pembelian')
                ->searchable(false)
                ->orderable(false)
                ->addClass('align-middle text-center')
                ->width(120),

            Column::computed('total_amount')
                ->title('Total Nilai')
                ->searchable(false)
                ->orderable(false)
                ->addClass('align-middle text-right')
                ->width(150),

            Column::computed('status')
                ->title('Status')
                ->searchable(false)
                ->orderable(false)
                ->addClass('align-middle text-center')
                ->width(100),

            Column::make('created_at')
                ->title('Tgl Daftar')
                ->searchable(false)
                ->orderable(true)
                ->addClass('align-middle text-center')
                ->width(100),

            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false)
                ->addClass('align-middle text-center')
                ->width(120),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Suppliers_' . date('YmdHis');
    }
}
