<?php

namespace Modules\Sale\DataTables;

use Carbon\Carbon;
use Modules\Sale\Entities\SaleReturn;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SaleReturnDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            // Reference + Date + Original Sale
            ->editColumn('reference', function ($sr) {
                $date = Carbon::parse($sr->date)->locale('id')->translatedFormat('d M Y');
                $ref = '<div class="text-sm font-bold text-gray-900 dark:text-white">' . $sr->reference . '</div>';
                $dateHtml = '<div class="text-xs text-gray-500 mt-0.5">' . $date . '</div>';
                
                // Original sale reference
                $saleRef = $sr->sale ? '<div class="text-xs text-blue-600 mt-1"><i class="bi bi-link-45deg"></i> ' . $sr->sale->reference . '</div>' : '';
                
                return '<div class="flex flex-col">' . $ref . $dateHtml . $saleRef . '</div>';
            })
            // Customer
            ->addColumn('customer', function ($sr) {
                return '<span class="text-sm text-gray-700 dark:text-gray-300">' . $sr->customer_display_name . '</span>';
            })
            // Status Badge
            ->editColumn('status', function ($sr) {
                return '<span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full ' . $sr->status_badge_class . '">' . $sr->status . '</span>';
            })
            // Refund Amount
            ->editColumn('refund_amount', function ($sr) {
                $total = format_currency((int) $sr->total_amount);
                $refund = format_currency((int) $sr->refund_amount);
                
                $html = '<div class="font-bold text-gray-900 dark:text-white">' . $refund . '</div>';
                if ($sr->total_amount != $sr->refund_amount) {
                    $html .= '<div class="text-xs text-gray-500 mt-0.5">dari ' . $total . '</div>';
                }
                
                return $html;
            })
            // Refund Method
            ->editColumn('refund_method', function ($sr) {
                $icon = match($sr->refund_method) {
                    'Cash' => 'bi-cash',
                    'Credit' => 'bi-credit-card',
                    'Store Credit' => 'bi-wallet2',
                    default => 'bi-cash',
                };
                return '<span class="text-sm"><i class="bi ' . $icon . ' mr-1"></i>' . $sr->refund_method . '</span>';
            })
            // Creator + Approver
            ->addColumn('created_info', function ($sr) {
                $creator = $sr->creator->name ?? '-';
                $approver = $sr->approver ? $sr->approver->name : null;
                
                $html = '<div class="text-sm text-gray-700 dark:text-gray-300">' . $creator . '</div>';
                if ($approver) {
                    $html .= '<div class="text-xs text-gray-500 mt-0.5"><i class="bi bi-check-circle"></i> ' . $approver . '</div>';
                }
                
                return $html;
            })
            // Actions
            ->addColumn('action', function ($sr) {
                $customActions = '';
                
                // Approve button (if pending)
                if ($sr->status === 'Pending') {
                    $customActions .= '
                        <li>
                            <button type="button" onclick="approveReturn(' . $sr->id . ')"
                               class="w-full flex items-center gap-2 px-4 py-2.5 hover:bg-green-50 dark:hover:bg-gray-600 transition-colors text-left">
                                <i class="bi bi-check-circle text-green-600"></i>
                                <span>Setujui</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" onclick="rejectReturn(' . $sr->id . ')"
                               class="w-full flex items-center gap-2 px-4 py-2.5 hover:bg-red-50 dark:hover:bg-gray-600 transition-colors text-left">
                                <i class="bi bi-x-circle text-red-600"></i>
                                <span>Tolak</span>
                            </button>
                        </li>
                    ';
                }

                return view('partials.datatable-actions', [
                    'id' => $sr->id,
                    'itemName' => 'Retur ' . $sr->reference,
                    'showRoute' => route('sale-returns.show', $sr->id),
                    'editRoute' => $sr->status === 'Pending' ? route('sale-returns.edit', $sr->id) : null,
                    'deleteRoute' => $sr->status === 'Pending' ? route('sale-returns.destroy', $sr->id) : null,
                    'showPermission' => 'access_sale_returns',
                    'editPermission' => 'edit_sale_returns',
                    'deletePermission' => 'delete_sale_returns',
                    'customActions' => $customActions
                ])->render();
            })
            ->rawColumns(['reference', 'customer', 'status', 'refund_amount', 'refund_method', 'created_info', 'action'])
            ->filter(function ($q) {
                $f = request('filter', []);
                
                // Status filter
                if (!empty($f['status'])) {
                    $q->where('status', $f['status']);
                }
                
                // Date range
                if (!empty($f['dari']) && !empty($f['sampai'])) {
                    $q->whereBetween('date', [$f['dari'], $f['sampai']]);
                }
                
                // Preset filters
                if (!empty($f['preset'])) {
                    $now = Carbon::now();
                    switch ($f['preset']) {
                        case 'today':
                            $q->whereDate('date', $now->toDateString());
                            break;
                        case 'this_week':
                            $q->whereBetween('date', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $q->whereMonth('date', $now->month)->whereYear('date', $now->year);
                            break;
                    }
                }
                
                // Global search
                if (request('search.value')) {
                    $keyword = request('search.value');
                    $q->where(function($query) use ($keyword) {
                        $query->where('reference', 'like', "%{$keyword}%")
                              ->orWhereHas('sale', function($q) use ($keyword) {
                                  $q->where('reference', 'like', "%{$keyword}%");
                              })
                              ->orWhereHas('customer', function($q) use ($keyword) {
                                  $q->where('customer_name', 'like', "%{$keyword}%");
                              });
                    });
                }
            });
    }

    public function query(SaleReturn $model)
    {
        return $model
            ->newQuery()
            ->with([
                'sale:id,reference',
                'customer:id,customer_name',
                'creator:id,name',
                'approver:id,name',
            ])
            ->withCount('details')
            ->select([
                'id', 'reference', 'sale_id', 'customer_id', 'date',
                'status', 'total_amount', 'refund_amount', 'refund_method',
                'reason', 'created_by', 'approved_by', 'approved_at'
            ]);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('sale-returns-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
            ->parameters([
                'drawCallback' => 'function() { 
                    window.scrollTo(0, 0); 
                    if (typeof initFlowbite === "function") {
                        initFlowbite();
                    }
                }'
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('reference')
                ->title('Retur')
                ->className('align-top min-w-[150px]'),
                
            Column::computed('customer')
                ->title('Customer')
                ->className('align-top min-w-[120px]'),
                
            Column::computed('refund_amount')
                ->title('Nilai Refund')
                ->className('text-right align-top min-w-[120px]'),
                
            Column::make('status')
                ->title('Status')
                ->className('align-top text-center min-w-[100px]'),
                
            Column::make('refund_method')
                ->title('Metode')
                ->className('align-top min-w-[100px]'),
                
            Column::computed('created_info')
                ->title('Dibuat Oleh')
                ->className('align-top min-w-[120px]'),

            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'SaleReturns_' . date('YmdHis');
    }
}
