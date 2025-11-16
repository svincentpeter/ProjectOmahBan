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
        // ✅ Perbaikan: tambahkan tanda kurung di sekitar new EloquentDataTable
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            // Kolom Supplier Name dengan link ke detail
            ->editColumn('supplier_name', function ($data) {
                return '<a href="' . route('suppliers.show', $data->id) . '" class="text-primary font-weight-bold">' . e($data->supplier_name) . '</a>';
            })

            // Format email dengan icon
            ->editColumn('supplier_email', function ($data) {
                return '<i class="bi bi-envelope"></i> ' . e($data->supplier_email);
            })

            // Format phone dengan icon
            ->editColumn('supplier_phone', function ($data) {
                return '<i class="bi bi-telephone"></i> ' . e($data->supplier_phone);
            })

            // Kolom City dengan badge
            ->editColumn('city', function ($data) {
                return '<span class="badge badge-light-info">' . e($data->city) . '</span>';
            })

            // Kolom total pembelian (computed column)
            ->addColumn('total_purchases', function ($data) {
                $count = $data->purchases_count ?? 0;

                if ($count > 0) {
                    return '<span class="badge badge-success">' . $count . ' transaksi</span>';
                }

                return '<span class="badge badge-secondary">Belum ada</span>';
            })

            // Kolom total nilai pembelian (computed column)
            ->addColumn('total_amount', function ($data) {
                $total = $data->purchases_sum_total_amount ?? 0;

                if ($total > 0) {
                    return '<strong class="text-success">' . format_currency($total) . '</strong>';
                }

                return '<span class="text-muted">-</span>';
            })

            // Status aktif (berdasarkan transaksi 6 bulan terakhir)
            ->addColumn('status', function ($data) {
                $hasRecentPurchase = \Modules\Purchase\Entities\Purchase::where('supplier_id', $data->id)
                    ->where('date', '>=', now()->subMonths(6))
                    ->exists();

                if ($hasRecentPurchase) {
                    return '<span class="badge badge-success">Aktif</span>';
                }

                return '<span class="badge badge-warning">Tidak Aktif</span>';
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
            ->dom(
                "
                <'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>>
                <'row'<'col-md-12'tr>>
                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>
            ",
            )
            ->orderBy(1, 'asc')
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel')->text('<i class="bi bi-file-earmark-excel"></i> Excel'),
                Button::make('print')->text('<i class="bi bi-printer"></i> Print'),
                Button::make('reset')->text('<i class="bi bi-arrow-clockwise"></i> Reset'),
                Button::make('reload')->text('<i class="bi bi-arrow-repeat"></i> Reload'),
            ])
            ->parameters([
                'language' => [
                    'emptyTable'   => 'Tidak ada data supplier',
                    'info'         => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ supplier',
                    'infoEmpty'    => 'Menampilkan 0 sampai 0 dari 0 supplier',
                    'infoFiltered' => '(difilter dari _MAX_ total supplier)',
                    'lengthMenu'   => 'Tampilkan _MENU_ supplier',
                    'search'       => 'Cari:',
                    'zeroRecords'  => 'Tidak ditemukan data supplier yang sesuai',
                    'paginate'     => [
                        'first'    => 'Pertama',
                        'last'     => 'Terakhir',
                        'next'     => 'Selanjutnya',
                        'previous' => 'Sebelumnya',
                    ],
                ],
                'responsive' => true,
                'autoWidth'  => false,
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
