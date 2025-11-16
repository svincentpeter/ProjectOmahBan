<?php

namespace Modules\Purchase\DataTables;

use Modules\Purchase\Entities\PurchaseSecond;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class PurchaseSecondDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn() // kolom nomor urut (#)
            ->editColumn('date', function ($data) {
                return optional($data->date)->format('d/m/Y');
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
                return view('purchase::second.partials.status', compact('data'));
            })
            ->addColumn('payment_status', function ($data) {
                return view('purchase::second.partials.payment-status', compact('data'));
            })
            ->addColumn('action', function ($data) {
                return view('purchase::second.partials.actions', compact('data'));
            })
            ->rawColumns(['status', 'payment_status', 'action']);
    }

    public function query(PurchaseSecond $model)
    {
        $query = $model->newQuery()->with('user');

        // === ambil filter dari request (sama kayak controller lamamu) ===
        $quickFilter   = request('quick_filter', 'all');
        $startDate     = request('from');
        $endDate       = request('to');
        $customerName  = request('customer');
        $paymentStatus = request('payment_status');

        switch ($quickFilter) {
            case 'yesterday':
                $query->whereDate('date', now()->subDay());
                break;
            case 'this_week':
                $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
                break;
            case 'last_month':
                $query->whereMonth('date', now()->subMonth()->month)
                      ->whereYear('date', now()->subMonth()->year);
                break;
        }

        if ($startDate && $endDate) {
            // kalau kamu memang punya scope between(), pakai itu
            if (method_exists($model, 'scopeBetween')) {
                $query->between($startDate, $endDate);
            } else {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        }

        if ($customerName) {
            if (method_exists($model, 'scopeByCustomer')) {
                $query->byCustomer($customerName);
            } else {
                $query->where('customer_name', 'like', "%{$customerName}%");
            }
        }

        if ($paymentStatus) {
            if (method_exists($model, 'scopeByPaymentStatus')) {
                $query->byPaymentStatus($paymentStatus);
            } else {
                $query->where('payment_status', $paymentStatus);
            }
        }

        return $query->latest('date');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('purchases-second-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>>" .
                  "tr" .
                  "<'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(1, 'desc')
            ->buttons(
                Button::make('excel')->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')->text('<i class="bi bi-arrow-repeat"></i> Reload')
            );
    }

    protected function getColumns()
    {
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
                ->title('Reference')
                ->className('text-center align-middle'),

            Column::make('customer_name')
                ->title('Customer')
                ->className('align-middle'),

            Column::computed('total_amount')
                ->title('Total')
                ->className('text-right align-middle'),

            Column::computed('paid_amount')
                ->title('Terbayar')
                ->className('text-right align-middle'),

            Column::computed('due_amount')
                ->title('Sisa')
                ->className('text-right align-middle'),

            Column::computed('status')
                ->title('Status')
                ->className('text-center align-middle'),

            Column::computed('payment_status')
                ->title('Status Bayar')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'PurchaseSecond_' . date('YmdHis');
    }
}
