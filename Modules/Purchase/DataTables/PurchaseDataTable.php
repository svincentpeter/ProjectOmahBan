<?php

namespace Modules\Purchase\DataTables;

use Modules\Purchase\Entities\Purchase;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PurchaseDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('date', function ($data) {
                return $data->date->format('d/m/Y');
            })
            ->addColumn('total_amount', function ($data) {
                return format_currency($data->total_amount);
            })
            ->addColumn('paid_amount', function ($data) {
                return format_currency($data->paid_amount);
            })
            ->addColumn('due_amount', function ($data) {
                return format_currency($data->due_amount);
            })
            ->addColumn('status', function ($data) {
                return view('purchase::baru.partials.status', compact('data'));
            })
            ->addColumn('payment_status', function ($data) {
                return view('purchase::baru.partials.payment-status', compact('data'));
            })
            ->addColumn('action', function ($data) {
                // Custom Actions: Show Payments & Add Payment
                $customActions = '';
                
                if (auth()->user()->can('access_purchase_payments')) {
                    // Show Payments
                    $customActions .= '
                        <li>
                            <a href="'.route('purchase-payments.index', $data->id).'" 
                               class="flex items-center gap-2 px-4 py-2.5 hover:bg-yellow-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                                <i class="bi bi-cash-coin text-yellow-600 dark:text-yellow-400"></i>
                                <span>Show Payments</span>
                            </a>
                        </li>
                    ';

                    // Add Payment (if due > 0)
                    if ($data->due_amount > 0) {
                        $customActions .= '
                            <li>
                                <a href="'.route('purchase-payments.create', $data->id).'" 
                                   class="flex items-center gap-2 px-4 py-2.5 hover:bg-green-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                                    <i class="bi bi-plus-circle-dotted text-green-600 dark:text-green-400"></i>
                                    <span>Add Payment</span>
                                </a>
                            </li>
                        ';
                    }
                }

                return view('partials.datatable-actions', [
                    'id' => $data->id,
                    'itemName' => 'Pembelian ' . $data->date->format('d/m/Y'),
                    'showRoute' => route('purchases.show', $data->id),
                    'editRoute' => route('purchases.edit', $data->id),
                    'deleteRoute' => route('purchases.destroy', $data->id),
                    'showPermission' => 'show_purchases',
                    'editPermission' => 'edit_purchases',
                    'deletePermission' => 'delete_purchases',
                    'customActions' => $customActions
                ])->render();
            })
            ->rawColumns(['status', 'payment_status', 'action']);
    }

    public function query(Purchase $model) {
        $query = $model->newQuery()->with(['supplier', 'user']);

        // === FILTER BY QUICK FILTER ===
        $from = null;
        $to = null;

        switch (request('quick_filter')) {
            case 'yesterday':
                $from = $to = now()->subDay()->toDateString();
                break;
            case 'this_week':
                $from = now()->startOfWeek()->toDateString();
                $to = now()->toDateString();
                break;
            case 'this_month':
                $from = now()->startOfMonth()->toDateString();
                $to = now()->toDateString();
                break;
            case 'last_month':
                $from = now()->subMonth()->startOfMonth()->toDateString();
                $to = now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'all':
                // No date filter
                break;
            default:
                // Default: Today atau custom range dari request
                $from = request('from') ? request('from') : null; 
                $to = request('to') ? request('to') : null;
        }

        // Apply date filters
        if(!$from && request('quick_filter') == 'today') {
             $from = now()->toDateString();
             $to = now()->toDateString();
        }

        if ($from && request('quick_filter') !== 'all') {
            $query->whereDate('date', '>=', $from);
        }
        if ($to && request('quick_filter') !== 'all') {
            $query->whereDate('date', '<=', $to);
        }

        // === FILTER BY SUPPLIER ===
        if (request('supplier_id')) {
            $query->where('supplier_id', request('supplier_id'));
        }

        // === FILTER BY PAYMENT STATUS ===
        if (request('payment_status')) {
            $query->where('payment_status', request('payment_status'));
        }

        return $query;
    }

    public function html() {
        return $this->builder()
            ->setTableId('purchases-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(10) // order by created_at (hidden)
            ->parameters([
                'drawCallback' => 'function() { 
                    window.scrollTo(0, 0); 
                    if (typeof initFlowbite === "function") {
                        initFlowbite();
                    }
                }'
            ]);
    }

    protected function getColumns() {
        return [
            Column::make('DT_RowIndex')
                ->title('#')
                ->orderable(false)
                ->searchable(false)
                ->className('text-center align-middle'),

            Column::make('date')
                ->title('Tanggal')
                ->className('text-center align-middle'),

            Column::make('reference')
                ->className('text-center align-middle font-bold text-purple-600'),

            Column::make('supplier_name')
                ->title('Supplier')
                ->className('text-center align-middle'),

            Column::computed('total_amount')
                ->title('Total')
                ->className('text-center align-middle font-bold text-slate-700'),

            Column::computed('paid_amount')
                ->title('Terbayar')
                ->className('text-center align-middle text-green-600'),

            Column::computed('due_amount')
                ->title('Sisa')
                ->className('text-center align-middle text-red-600'),

            Column::computed('status')
                ->className('text-center align-middle'),

            Column::computed('payment_status')
                ->title('Status Bayar')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Purchase_' . date('YmdHis');
    }
}
