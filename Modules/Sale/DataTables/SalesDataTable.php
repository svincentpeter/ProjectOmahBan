<?php

namespace Modules\Sale\DataTables;

use Modules\Sale\Entities\Sale;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SalesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            // Status (badge)
            ->addColumn('status', fn ($sale) =>
                view('sale::partials.status', ['data' => $sale])
            )
            // Payment status (badge)
            ->addColumn('payment_status', fn ($sale) =>
                view('sale::partials.payment-status', ['data' => $sale])
            )
            // Total, Paid, Due -> format Rupiah
            ->addColumn('total_amount', fn ($sale) => format_currency((int)$sale->total_amount))
            ->addColumn('paid_amount',  fn ($sale) => format_currency((int)$sale->paid_amount))
            ->addColumn('due_amount',   fn ($sale) => format_currency((int)$sale->due_amount))

            // Profit = Σ (sub_total – (hpp * qty))
            ->addColumn('total_profit', function ($sale) {
                $profit = $sale->saleDetails->sum(function ($d) {
                    $subTotal  = (int) $d->sub_total;     // sudah rupiah via accessor model
                    $hppTotal  = (int) $d->hpp * (int) $d->quantity; // hpp disimpan rupiah
                    return $subTotal - $hppTotal;
                });
                return format_currency($profit);
            })

            // Pembayaran (metode + bank jika ada)
            ->addColumn('payment_method', function ($sale) {
                $method = $sale->payment_method ?: '-';
                return !empty($sale->bank_name) ? $method.' ('.$sale->bank_name.')' : $method;
            })

            // Aksi
            ->addColumn('action', fn ($sale) =>
                view('sale::partials.actions', ['data' => $sale])
            );
    }

    public function query(Sale $model)
    {
        // penting supaya kolom profit tidak N+1
        return $model->newQuery()->with('saleDetails');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('sales-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // Bahasa Indonesia
            ->parameters([
                'language' => [
                    'url' => 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json',
                ],
            ])
            ->dom(
                "<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>>" .
                "tr" .
                "<'row'<'col-md-5'i><'col-md-7 mt-2'p>>"
            )
            ->orderBy(0)
            ->buttons(
                Button::make('excel')->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')->text('<i class="bi bi-printer-fill"></i> Cetak'),
                Button::make('reset')->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')->text('<i class="bi bi-arrow-repeat"></i> Muat Ulang')
            );
    }

    protected function getColumns()
    {
        return [
            Column::make('reference')
                ->title('Ref')
                ->className('text-center align-middle'),

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
                ->title('Dibayar')
                ->className('text-center align-middle'),

            Column::computed('due_amount')
                ->title('Kurang')
                ->className('text-center align-middle'),

            Column::computed('payment_status')
                ->title('Status Bayar')
                ->className('text-center align-middle'),

            Column::computed('payment_method')
                ->title('Pembayaran')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            // untuk default sorting terbaru bisa pakai created_at (disembunyikan)
            Column::make('created_at')->visible(false),
        ];
    }

    protected function filename(): string
    {
        return 'Penjualan_' . date('YmdHis');
    }
}
