<?php

namespace Modules\Sale\DataTables;

use Modules\Sale\Entities\Sale;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SalesDataTable extends DataTable
{
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('total_amount', function ($data) {
                return format_currency($data->total_amount);
            })
            ->addColumn('paid_amount', function ($data) {
                return format_currency($data->paid_amount);
            })
            ->addColumn('total_profit', function ($data) {
                return format_currency($data->total_profit);
            })
            ->addColumn('due_amount', function ($data) {
                return format_currency($data->due_amount);
            })
            ->addColumn('status', function ($data) {
                return view('sale::partials.status', compact('data'));
            })
            ->addColumn('payment_status', function ($data) {
                return view('sale::partials.payment-status', compact('data'));
            })
            ->addColumn('payment_method', function ($data) {
                $method = $data->payment_method;
                if (!empty($data->bank_name)) {
                    $method .= ' (' . $data->bank_name . ')';
                }
                return $method;
            })
            ->addColumn('action', function ($data) {
                return view('sale::partials.actions', compact('data'));
            });
    }

    public function query(Sale $model) {
        return $model->newQuery();
    }

    public function html() {
        return $this->builder()
            ->setTableId('sales-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(2)
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
            Column::make('reference')
                ->title('Ref')
                ->className('text-center align-middle'),

            // Kolom customer_name DIHAPUS SESUAI CATATAN
            // Column::make('customer_name')
            //     ->title('Customer')
            //     ->className('text-center align-middle'),

            Column::computed('status')
                ->title('Status')
                ->className('text-center align-middle'),

            Column::computed('total_amount')
                ->title('Total')
                ->className('text-center align-middle'),

            Column::computed('total_profit')
                ->title('Profit')
                ->className('text-center align-middle'),

            Column::computed('paid_amount')
                ->title('Paid')
                ->className('text-center align-middle'),

            Column::computed('due_amount')
                ->title('Due')
                ->className('text-center align-middle'),

            Column::computed('payment_status')
                ->title('Payment Status')
                ->className('text-center align-middle'),

            Column::computed('payment_method')
                ->title('Payment')
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
        return 'Sales_' . date('YmdHis');
    }
}
