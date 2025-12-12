<?php

namespace Modules\Expense\DataTables;

use Modules\Expense\Entities\Expense;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ExpensesDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('amount', function ($data) {
                return format_currency($data->amount);
            })
            ->addColumn('action', function ($data) {
                return view('expense::expenses.partials.actions', compact('data'));
            });
    }

    public function query(Expense $model) {
        $query = $model->newQuery()->with('category');

        // Apply filters
        $request = request();
        $from = null;
        $to = null;

        // Determine date range based on quick_filter
        switch ($request->get('quick_filter')) {
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
                break;
            default:
                $from = $request->filled('from') ? $request->from : now()->toDateString();
                $to = $request->filled('to') ? $request->to : now()->toDateString();
        }

        if ($from && $request->get('quick_filter') !== 'all') {
            $query->whereDate('date', '>=', $from);
        }

        if ($to && $request->get('quick_filter') !== 'all') {
            $query->whereDate('date', '<=', $to);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return $query;
    }

    public function html() {
        return $this->builder()
            ->setTableId('expenses-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(6)
            ;
    }

    protected function getColumns() {
        return [
            Column::make('date')
                ->className('text-center align-middle'),

            Column::make('reference')
                ->className('text-center align-middle'),

            Column::make('category.category_name')
                ->title('Category')
                ->className('text-center align-middle'),

            Column::computed('amount')
                ->className('text-center align-middle'),

            Column::make('details')
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
        return 'Expenses_' . date('YmdHis');
    }
}
