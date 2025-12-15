<?php

namespace Modules\Sale\DataTables;

use Carbon\Carbon;
use Modules\Sale\Entities\Quotation;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class QuotationsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('reference', function ($q) {
                $date = Carbon::parse($q->date)->locale('id')->translatedFormat('d M Y');
                return '<div class="flex flex-col">
                            <span class="font-bold text-gray-900 dark:text-white">' . $q->reference . '</span>
                            <span class="text-xs text-gray-500">' . $date . '</span>
                        </div>';
            })
            ->editColumn('customer_name', function ($q) {
                return $q->customer_name;
            })
            ->editColumn('total_amount', function ($q) {
                return format_currency($q->total_amount);
            })
            ->editColumn('status', function ($q) {
                $color = match ($q->status) {
                    'Pending' => 'blue',
                    'Sent' => 'yellow',
                    'Accepted' => 'green',
                    'Rejected' => 'red',
                    'Converted' => 'purple',
                    default => 'gray'
                };
                return '<span class="bg-' . $color . '-100 text-' . $color . '-800 text-xs font-medium px-2.5 py-0.5 rounded border border-' . $color . '-400">' . $q->status . '</span>';
            })
            ->addColumn('action', function ($data) {
                $customActions = '';

                // Convert to Sale Action
                if ($data->status !== 'Converted' && \Illuminate\Support\Facades\Gate::allows('create_sales')) {
                    $customActions .= '
                        <li>
                            <a href="' . route('quotations.convert-to-sale', $data->id) . '"
                               class="flex items-center gap-2 px-4 py-2.5 hover:bg-green-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                                <i class="bi bi-cart-check text-green-600 dark:text-green-400"></i>
                                <span>Convert to Sale</span>
                            </a>
                        </li>
                    ';
                }

                return view('partials.datatable-actions', [
                    'id' => $data->id,
                    'itemName' => 'Quotation ' . $data->reference,
                    'showRoute' => route('quotations.show', $data->id),
                    'editRoute' => route('quotations.edit', $data->id),
                    'deleteRoute' => route('quotations.destroy', $data->id),
                    'showPermission' => 'access_sales', // Reuse access_sales for show as specific show_quotations might not exist
                    'editPermission' => 'edit_sales',
                    'deletePermission' => 'delete_sales',
                    'customActions' => $customActions
                ])->render();
            })
            ->rawColumns(['reference', 'status', 'action']);
    }

    public function query(Quotation $model)
    {
        return $model->newQuery();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('quotations-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc');
    }

    protected function getColumns()
    {
        return [
            Column::make('reference')->title('Reference')->className('align-middle'),
            Column::make('customer_name')->title('Customer')->className('align-middle'),
            Column::make('status')->title('Status')->className('align-middle'),
            Column::make('total_amount')->title('Total')->className('align-middle text-right'),
            Column::computed('action')->title('Action')->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Quotations_' . date('YmdHis');
    }
}
