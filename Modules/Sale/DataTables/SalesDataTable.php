<?php

namespace Modules\Sale\DataTables;

use Carbon\Carbon;
use Modules\Sale\Entities\Sale;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SalesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('row_detail', function ($s) {
                $id = (int) $s->id;
                $url = route('sales.items', ['sale' => $id]);
                return '<button type="button" class="btn-expand text-gray-500 hover:text-blue-600 transition-colors" data-url="' . $url . '"><i class="bi bi-chevron-down"></i></button>';
            })
            // KOMBINASI: Ref + Date + Badges (Manual/Discount)
            ->editColumn('reference', function ($s) {
                $date = Carbon::parse($s->date)->locale('id')->translatedFormat('d M Y');
                $ref = '<div class="text-sm font-bold text-gray-900 dark:text-white">' . $s->reference . '</div>';
                $dateHtml = '<div class="text-xs text-gray-500 mt-0.5">' . $date . '</div>';
                
                // Indicators
                $badges = '';
                if ((int) $s->has_manual_input === 1) {
                    $badges .= '<span class="inline-flex mr-1 text-[10px] bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded border border-yellow-200" title="Ada item manual input"><i class="bi bi-pencil-square mr-1"></i>Manual</span>';
                }
                if ((int) $s->has_price_adjustment === 1) {
                    $badges .= '<span class="inline-flex text-[10px] bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded border border-blue-200" title="Ada perubahan harga/diskon"><i class="bi bi-tag-fill mr-1"></i>Adj</span>';
                }
                
                return '<div class="flex flex-col">' . $ref . $dateHtml . ($badges ? '<div class="mt-1 flex flex-wrap gap-1">' . $badges . '</div>' : '') . '</div>';
            })
            // KOMBINASI: Status & Payment Status
            ->addColumn('status_col', function ($s) {
                $orderStatus = view('sale::partials.status', ['data' => $s])->render();
                $payStatus = view('sale::partials.payment-status', ['data' => $s])->render();
                return '<div class="flex flex-col gap-1.5 items-start justify-center h-full">' . $orderStatus . $payStatus . '</div>';
            })
            // KOMBINASI: Total + Sisa Tagihan + Profit (Tooltip)
            ->editColumn('total_amount', function ($s) {
                $total = format_currency((int) $s->total_amount);
                $due = (int) $s->due_amount;
                $profit = format_currency((int) $s->total_profit);
                
                $html = '<div class="font-bold text-gray-900 dark:text-white">' . $total . '</div>';
                
                if ($due > 0) {
                    $html .= '<div class="text-xs text-red-500 font-medium mt-0.5" title="Belum Dibayar">Kurang: ' . format_currency($due) . '</div>';
                } else {
                    $html .= '<div class="text-xs text-emerald-500 font-medium mt-0.5"><i class="bi bi-check-all"></i> Lunas</div>';
                }

                // Profit info (Small/Tooltip)
                $html .= '<div class="text-[10px] text-gray-400 mt-1" title="Profit Transaksi">Profit: ' . $profit . '</div>';
                
                return $html;
            })
            // KOMBINASI: Kasir + Metode Bayar
            ->addColumn('cashier_info', function ($s) {
                $user = $s->user->name ?? '—';
                $last = $s->salePayments->first(); // sorted desc
                $method = $last?->payment_method ?? ($s->payment_method ?? '-');
                $bank = $last?->bank_name ?? $s->bank_name;
                $paymentText = $bank ? "{$method} ({$bank})" : $method;
                
                return '<div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate max-w-[120px]" title="' . $user . '">' . $user . '</span>
                    <span class="text-xs text-gray-500 mt-0.5 truncate max-w-[120px]" title="' . $paymentText . '"><i class="bi bi-wallet2 mr-1"></i>' . $paymentText . '</span>
                </div>';
            })
            
            ->addColumn('action', function ($data) {
                $customActions = '';
                
                // Cetak Struk POS
                $customActions .= '
                    <li>
                        <a href="'.route('sales.pos.pdf', $data->id).'" target="_blank"
                           class="flex items-center gap-2 px-4 py-2.5 hover:bg-green-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                            <i class="bi bi-file-earmark-pdf text-green-600 dark:text-green-400"></i>
                            <span>Cetak Struk POS</span>
                        </a>
                    </li>
                ';

                // Lihat Pembayaran
                if (\Illuminate\Support\Facades\Gate::allows('access_sale_payments')) {
                     $customActions .= '
                        <li>
                            <a href="'.route('sale-payments.index', $data->id).'" 
                               class="flex items-center gap-2 px-4 py-2.5 hover:bg-yellow-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                                <i class="bi bi-cash-coin text-yellow-600 dark:text-yellow-400"></i>
                                <span>Lihat Pembayaran</span>
                            </a>
                        </li>
                    ';
                }

                // Tambah Pembayaran (if due > 0)
                if ((int)$data->due_amount > 0) {
                     $customActions .= '
                        <li>
                            <a href="'.route('sale-payments.create', $data->id).'" 
                               class="flex items-center gap-2 px-4 py-2.5 hover:bg-green-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                                <i class="bi bi-plus-circle-dotted text-green-600 dark:text-green-400"></i>
                                <span>Tambah Pembayaran</span>
                            </a>
                        </li>
                    ';
                }

                return view('partials.datatable-actions', [
                    'id' => $data->id,
                    'itemName' => 'Penjualan ' . $data->reference,
                    // Note: original partial didn't have delete, but controller has destroy. adding it.
                    'showRoute' => route('sales.show', $data->id),
                    'editRoute' => route('sales.edit', $data->id),
                    'deleteRoute' => route('sales.destroy', $data->id), 
                    'showPermission' => 'show_sales',
                    'editPermission' => 'edit_sales',
                    'deletePermission' => 'delete_sales',
                    'customActions' => $customActions
                ])->render();
            })
            
            ->rawColumns(['row_detail', 'reference', 'status_col', 'total_amount', 'cashier_info', 'action'])
            
            // ... filters ... (Keep existing logic)
            ->filter(function ($q) {
                 $f = request('filter', []);
                \Illuminate\Support\Facades\Log::info('Datatable Filter:', (array)$f);

                $get = function ($key, $default = null) use ($f) {
                    return $f[$key] ?? request($key, $default);
                };

                $preset = $get('preset');
                $bulan = $get('bulan') ?? $get('month');
                $dari = $get('dari') ?? $get('from');
                $sampai = $get('sampai') ?? $get('to');
                $hasAdjustment = $get('has_adjustment');
                $hasManual = $get('has_manual');

                if ((int) $hasAdjustment === 1) {
                    $q->where('has_price_adjustment', 1);
                }
                if ((int) $hasManual === 1) {
                    $q->where('has_manual_input', 1);
                }

                // Range tanggal prioritas tertinggi
                if (!empty($dari) && !empty($sampai)) {
                    $q->whereBetween('date', [$dari, $sampai]);
                    return;
                }
                
                if (!empty($preset)) {
                    $now = Carbon::now();
                    switch ($preset) {
                        case 'today':
                            $q->whereDate('date', $now->toDateString());
                            return;
                        case 'this_week':
                            $q->whereBetween('date', [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()]);
                            return;
                        case 'this_month':
                            $q->whereMonth('date', $now->month)->whereYear('date', $now->year);
                            return;
                        case 'this_year':
                            $q->whereYear('date', $now->year);
                            return;
                    }
                }
                
                // Default: Current Month if no other filter? 
                // Original logic had "month" filter.
                 if (!empty($bulan) && strpos($bulan, '-') !== false) {
                    [$y, $m] = explode('-', $bulan);
                    $q->whereYear('date', (int) $y)->whereMonth('date', (int) $m);
                }
                
                // If text search (global search)
                if (request('search.value')) {
                    $q->where(function($query) {
                        $keyword = request('search.value');
                        $query->where('reference', 'like', "%{$keyword}%")
                              ->orWhere('customer_name', 'like', "%{$keyword}%")
                              ->orWhereHas('user', function($q) use($keyword){
                                  $q->where('name', 'like', "%{$keyword}%");
                              });
                    });
                }
            });
    }

    public function query(Sale $model)
    {
        $q = $model
            ->newQuery()
            ->with([
                'user:id,name',

                // ✅ BENAR: atur kolom di relasi anak via closure
                'saleDetails' => function ($q) {
                    $q->select([
                        'id',
                        'sale_id', // wajib untuk menghubungkan ke Sale
                        'product_name',
                        'product_code',
                        'quantity',
                        'price',
                        'sub_total',
                        'subtotal_profit',
                        'hpp', // disiapkan untuk fallback hitung profit
                        'is_price_adjusted',
                        'price_adjustment_amount',
                        'price_adjustment_note',
                        'adjusted_by', // ✅ WAJIB agar relasi adjuster bisa resolve
                        'adjusted_at',
                    ]);
                },

                // ✅ BENAR: nested eager load kolom relasi bersarang
                'saleDetails.adjuster:id,name',

                'salePayments' => function ($q) {
                    $q->select('id', 'sale_id', 'payment_method', 'bank_name', 'date')->orderByDesc('date')->orderByDesc('id');
                },
            ])

            // ✅ Konsisten dengan nama relasi yang dipakai di atas (saleDetails)
            ->withCount([
                'saleDetails as adjusted_items_count' => function ($d) {
                    $d->where('is_price_adjusted', 1);
                },
            ]);

        return $q->select('id', 'reference', 'date', 'status', 'total_amount', 'paid_amount', 'due_amount', 'total_profit', 'total_hpp', 'payment_status', 'payment_method', 'bank_name', 'user_id', 'has_price_adjustment', 'has_manual_input', 'manual_input_count');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('sales-table')
            ->columns($this->getColumns())
            ->minifiedAjax() 
            ->orderBy(2, 'desc') // Order by Date desc
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
            Column::computed('row_detail')->title('')->exportable(false)->printable(false)->width(24)->className('text-center align-middle pr-0'),
            
            Column::make('reference')
                ->title('Transaksi')
                ->className('align-top min-w-[150px]'),
                
            Column::computed('total_amount') // Financials (Total, Due, Profit)
                ->title('Nilai Transaksi')
                ->className('text-right align-top min-w-[120px]'),
                
            Column::computed('status_col') // Merged Statuses
                ->title('Status')
                ->className('align-top min-w-[100px]'),
                
            Column::computed('cashier_info') // Kasir & Method
                ->title('Kasir & Info')
                ->className('align-top min-w-[120px]'),

            Column::computed('action')->title('Aksi')->exportable(false)->printable(false)->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Penjualan_' . date('YmdHis');
    }
}
