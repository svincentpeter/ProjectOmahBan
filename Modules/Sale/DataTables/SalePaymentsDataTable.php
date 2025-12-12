<?php

namespace Modules\Sale\DataTables;

use Modules\Sale\Entities\SalePayment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalePaymentsDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('amount_formatted', function ($data) { // Renamed to amount_formatted to match column name in index?
                return format_currency($data->amount);
            })
            // Map payment_method to show bank if exists
            ->editColumn('payment_method', function($data) {
                if (!empty($data->bank_name)) {
                    return $data->payment_method . ' (' . $data->bank_name . ')';
                }
                return $data->payment_method;
            })
            ->editColumn('date', function($data) {
                return \Carbon\Carbon::parse($data->date)->format('d/m/Y');
            })
            ->addColumn('action', function ($data) {
                return view('partials.datatable-actions', [
                    'id' => $data->id,
                    'itemName' => 'Pembayaran ' . $data->reference,
                    'editRoute' => route('sale-payments.edit', ['sale' => $data->sale_id, 'sale_payment' => $data->id]), // Verify route name: sale-payments.edit takes $sale_id, $salePayment
                        // Controller: public function edit($sale_id, SalePayment $salePayment)
                        // Route resource usually: sales.payments.edit ??
                        // Let's check existing partial or route usage.
                        // Controller actions in index view (manual JS) used: data-url="{{ route('sale-payments.destroy', $row->id) }}"? No, JS used dataset.url.
                        // Existing create used route('sale-payments.create', $sale->id).
                        // I will assume route 'sale-payments.edit' exists and takes sale_id and sale_payment params.
                        // Wait, resource routes usually are /sale-payments/{sale_payment}/edit if not nested OR /sales/{sale}/payments/{payment}/edit.
                        // Controller signature implies nested or two params? function edit($sale_id, SalePayment $salePayment)
                        // This implies /sales/{sale}/payments/{payment}/edit structure.
                        // The route name 'sale-payments.edit' likely maps to this.
                    'editPermission' => 'access_sale_payments',
                    'deleteRoute' => route('sale-payments.destroy', ['sale' => $data->sale_id, 'sale_payment' => $data->id]), // Destroy also takes $sale_id?
                        // Controller: public function destroy($sale_id = null, SalePayment $salePayment)
                        // It accepts $sale_id as first arg (optional) and model binding for second.
                    'deletePermission' => 'access_sale_payments',
                ])->render();
            })
            ->rawColumns(['action']);
    }

    public function query(SalePayment $model) {
        // Filter by Sale ID from URL
        $saleId = request()->route('sale'); // parameter name in route? 'sale' or 'sale_id'?
        // Controller index($sale_id, ...)
        // Resource route usually passes 'sale'.
        // Let's assume 'sale' parameter.
        // Actually, if using $dataTable->render(), the request context handles it if I use properly.
        // But better to get straight from request.
        // The previous code utilized ->bySale()? No, it was $model->newQuery()->bySale()->with('sale');
        // Wait, bySale() scope usually filters by request? Or is it a scope I assume exists?
        // Let's look at previous file content (Step 842): "return $model->newQuery()->bySale()->with('sale');"
        // If bySale() works, great. If not, explicitly:
        $query = $model->newQuery();
        if ($saleId = request()->route('sale')) {
             $query->where('sale_id', $saleId);
        } elseif ($saleId = request()->route('sale_id')) {
             $query->where('sale_id', $saleId);
        }
        return $query;
    }

    public function html() {
        return $this->builder()
            ->setTableId('sale-payments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc') // Date desc
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
            Column::make('date')->title('Tanggal')->className('align-top'),
            Column::make('reference')->title('Ref')->className('align-top'),
            Column::make('payment_method')->title('Metode')->className('align-top'),
            // Bank name merged into method or separate? I merged it in editColumn payment_method. Remove separate column.
            Column::computed('amount_formatted')->title('Jumlah')->className('text-right align-top'),
            Column::make('note')->title('Catatan')->className('align-top'),
            Column::computed('action')->title('Aksi')->exportable(false)->printable(false)->className('text-center align-top'),
        ];
    }

    protected function filename(): string {
        return 'SalePayments_' . date('YmdHis');
    }
}
