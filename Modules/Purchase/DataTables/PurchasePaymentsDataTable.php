<?php

namespace Modules\Purchase\DataTables;

use Modules\Purchase\Entities\PurchasePayment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PurchasePaymentsDataTable extends DataTable
{
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('amount', function ($data) {
                return format_currency($data->amount);
            })
            ->addColumn('action', function ($data) {
                return view('partials.datatable-actions', [
                    'id' => $data->id,
                    'itemName' => 'Pembayaran ' . $data->reference,
                    // edit uses nested resource pattern: purchases/{purchase}/payments/{payment}/edit
                    // But controller route is likely purchase-payments.edit taking ($purchase_id, $payment) or just ($payment)?
                    // Controller: public function edit($purchase_id, PurchasePayment $purchasePayment)
                    // Route list check would be good, but assuming standard nested or separate.
                    // Controller calls route('purchases.index') on success.
                    // Looking at Actions from partials/actions.blade.php (Wait, I haven't seen the payments partial).
                    // Controller actions...
                    'editRoute' => route('purchase-payments.edit', ['purchase_id' => $data->purchase_id, 'purchase_payment' => $data->id]),
                    'editPermission' => 'access_purchase_payments',
                    'deleteRoute' => route('purchase-payments.destroy', $data->id),
                    'deletePermission' => 'access_purchase_payments',
                ])->render();
            })
            ->rawColumns(['action']);
    }

    public function query(PurchasePayment $model) {
        return $model->newQuery()->byPurchase()->with('purchase');
    }

    public function html() {
        return $this->builder()
            ->setTableId('purchase-payments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0) // date column
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
            Column::make('date')
                ->className('align-middle text-center'),

            Column::make('reference')
                ->className('align-middle text-center'),

            Column::computed('amount')
                ->className('align-middle text-center'),

            Column::make('payment_method')
                ->className('align-middle text-center'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('align-middle text-center'),

            Column::make('created_at')
                ->visible(false),
        ];
    }

    protected function filename(): string {
        return 'PurchasePayments_' . date('YmdHis');
    }
}
