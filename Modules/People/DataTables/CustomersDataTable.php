<?php

namespace Modules\People\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\People\Entities\Customer;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CustomersDataTable extends DataTable
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
            
            // Kolom Customer Name dengan link ke detail
            ->editColumn('customer_name', function ($data) {
                return '<a href="' . route('customers.show', $data->id) . '" class="text-blue-600 hover:text-blue-800 font-bold hover:underline">' 
                    . e($data->customer_name) 
                    . '</a>';
            })
            
            // Format email
            ->editColumn('customer_email', function ($data) {
                return '<div class="text-zinc-600">' . e($data->customer_email) . '</div>';
            })
            
            // Format phone
            ->editColumn('customer_phone', function ($data) {
                return '<div class="text-zinc-600 font-mono">' . e($data->customer_phone) . '</div>';
            })
            
            // Kolom City dengan badge
            ->editColumn('city', function ($data) {
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">' . e($data->city) . '</span>';
            })
            
            // Kolom total penjualan (computed column)
            ->addColumn('total_sales', function ($data) {
                $count = $data->sales_count ?? 0;
                
                if ($count > 0) {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">' . $count . ' transaksi</span>';
                }
                
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600">Belum ada</span>';
            })
            
            // Kolom total nilai penjualan (computed column)
            ->addColumn('total_amount', function ($data) {
                $total = $data->sales_sum_total_amount ?? 0;
                
                if ($total > 0) {
                    return '<strong class="text-emerald-600">' . format_currency($total) . '</strong>';
                }
                
                return '<span class="text-zinc-400">-</span>';
            })
            
            // Status aktif (berdasarkan transaksi 6 bulan terakhir)
            ->addColumn('status', function ($data) {
                $hasRecentSale = \Modules\Sale\Entities\Sale::where('customer_id', $data->id)
                    ->where('date', '>=', now()->subMonths(6))
                    ->exists();
                
                if ($hasRecentSale) {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Aktif</span>';
                }
                
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-zinc-100 text-zinc-500">Tidak Aktif</span>';
            })
            
            // Kolom action buttons
            ->addColumn('action', function ($data) {
                return view('partials.datatable-actions', [
                    'id' => $data->id,
                    'showRoute' => route('customers.show', $data->id),
                    'editRoute' => route('customers.edit', $data->id),
                    'deleteRoute' => route('customers.destroy', $data->id),
                    'itemName' => $data->customer_name,
                    'showPermission' => 'show_customers',
                    'editPermission' => 'edit_customers',
                    'deletePermission' => 'delete_customers',
                ])->render();
            })
            
            // Set kolom yang mengandung HTML (jangan di-escape)
            ->rawColumns([
                'customer_name', 
                'customer_email',
                'customer_phone',
                'city', 
                'total_sales', 
                'total_amount', 
                'status', 
                'action'
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Modules\People\Entities\Customer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Customer $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->withCount('sales')
            ->withSum('sales', 'total_amount')
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
                $query->whereHas('sales', function ($q) {
                    $q->where('date', '>=', now()->subMonths(6));
                });
            } elseif ($status === 'inactive') {
                $query->whereDoesntHave('sales', function ($q) {
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
            ->setTableId('customers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'processing' => true,
                'serverSide' => true,
                'drawCallback' => 'function() { 
                    window.scrollTo(0, 0); 
                    if (typeof initFlowbite === "function") {
                        initFlowbite();
                    }
                }'
            ]);
    }
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('#')
                ->searchable(false)
                ->orderable(false)
                ->width(30)
                ->addClass('text-center align-middle'),
            
            Column::make('customer_name')
                ->title('Nama Customer')
                ->searchable(true)
                ->orderable(true)
                ->addClass('align-middle'),
            
            Column::make('customer_email')
                ->title('Email')
                ->searchable(true)
                ->orderable(true)
                ->addClass('align-middle'),
            
            Column::make('customer_phone')
                ->title('No. Telepon')
                ->searchable(true)
                ->orderable(false)
                ->addClass('align-middle'),
            
            Column::make('city')
                ->title('Kota')
                ->searchable(true)
                ->orderable(true)
                ->addClass('align-middle text-center'),
            
            Column::computed('total_sales')
                ->title('Total Penjualan')
                ->searchable(false)
                ->orderable(false)
                ->addClass('align-middle text-center'),
            
            Column::computed('total_amount')
                ->title('Total Nilai')
                ->searchable(false)
                ->orderable(false)
                ->addClass('align-middle text-right'),
            
            Column::computed('status')
                ->title('Status')
                ->searchable(false)
                ->orderable(false)
                ->addClass('align-middle text-center'),
            
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false)
                ->addClass('align-middle text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Customers_' . date('YmdHis');
    }
}
