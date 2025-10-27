<?php

namespace Modules\Sale\DataTables; // atau Modules\Sale\DataTables

use Carbon\Carbon;
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

            // Tombol expand
            ->addColumn('row_detail', function ($s) {
                $id = (int) $s->id;
                $url = route('sales.items', ['sale' => $id]);
                return '<button type="button" class="btn btn-sm btn-primary btn-expand" data-url="' . $url . '">' . '<i class="bi bi-chevron-down"></i>' . '</button>';
            })

            ->editColumn('date', fn($s) => Carbon::parse($s->date)->locale('id')->translatedFormat('d M Y'))
            ->addColumn('status', fn($s) => view('sale::partials.status', ['data' => $s]))
            ->addColumn('payment_status', fn($s) => view('sale::partials.payment-status', ['data' => $s]))
            ->addColumn('total_amount', fn($s) => format_currency((int) $s->total_amount))
            ->addColumn('paid_amount', fn($s) => format_currency((int) $s->paid_amount))
            ->addColumn('due_amount', fn($s) => format_currency((int) $s->due_amount))

            // Profit
            ->addColumn('total_profit', function ($s) {
                $profit = $s->saleDetails->sum(function ($d) {
                    if (isset($d->subtotal_profit)) {
                        return (int) $d->subtotal_profit;
                    }
                    $qty = (int) ($d->quantity ?? 0);
                    $sub = (int) ($d->sub_total ?? 0);
                    $hpp = (int) ($d->hpp ?? 0);
                    return $sub - $hpp * $qty;
                });
                return format_currency((int) $profit);
            })

            // Pembayaran terakhir
            ->addColumn('payment_method', function ($s) {
                $last = $s->salePayments->first();
                $method = $last->payment_method ?? ($s->payment_method ?? '-');
                $bank = $last->bank_name ?? $s->bank_name;
                return $bank ? "{$method} ({$bank})" : $method;
            })

            // ✅ KOLOM DISKON
            ->addColumn('price_adjustment', function ($s) {
                if (!$s->has_price_adjustment) {
                    return '<span class="badge badge-secondary badge-sm">-</span>';
                }

                $totalAdjustment = $s->saleDetails->where('is_price_adjusted', 1)->sum('price_adjustment_amount');

                $itemCount = $s->saleDetails->where('is_price_adjusted', 1)->count();

                if ($totalAdjustment > 0) {
                    return '<span class="badge badge-warning badge-sm" title="' . $itemCount . ' item dengan diskon">' . '<i class="bi bi-tag-fill"></i> Rp ' . number_format($totalAdjustment, 0, ',', '.') . '</span>';
                } elseif ($totalAdjustment < 0) {
                    return '<span class="badge badge-success badge-sm" title="' . $itemCount . ' item dengan kenaikan harga">' . '<i class="bi bi-arrow-up-circle"></i> +Rp ' . number_format(abs($totalAdjustment), 0, ',', '.') . '</span>';
                }

                return '<span class="badge badge-secondary badge-sm">-</span>';
            })

            ->addColumn('action', fn($s) => view('sale::partials.actions', ['data' => $s]))
            ->rawColumns(['row_detail', 'status', 'payment_status', 'price_adjustment', 'action'])

            // ✅ FILTER - HAPUS use ($request)
            ->filter(function ($q) {
                // ← FIX: Hapus use ($request)
                // Get filter params
                $preset = request('preset');
                $bulan = request('bulan');
                $dari = request('dari');
                $sampai = request('sampai');
                $hasAdjustment = request('has_adjustment');

                // Filter adjustment
                if ($hasAdjustment === '1') {
                    $q->where('has_price_adjustment', 1);
                }

                // Filter range tanggal (prioritas tertinggi)
                if (!empty($dari) && !empty($sampai)) {
                    $q->whereBetween('date', [$dari, $sampai]);
                    return;
                }

                // Filter preset
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
                        case 'last_month':
                            $prev = $now->copy()->subMonth();
                            $q->whereMonth('date', $prev->month)->whereYear('date', $prev->year);
                            return;
                        case 'this_year':
                            $q->whereYear('date', $now->year);
                            return;
                    }
                }

                // Filter bulan spesifik
                if (!empty($bulan)) {
                    [$y, $m] = explode('-', $bulan);
                    $q->whereYear('date', (int) $y)->whereMonth('date', (int) $m);
                }
            });
    }

    public function query(Sale $model)
    {
        $q = $model->newQuery()->with([
            // ✅ TAMBAH kolom tracking harga di saleDetails
            'saleDetails:id,sale_id,product_name,product_code,quantity,price,sub_total,subtotal_profit,is_price_adjusted,price_adjustment_amount,price_adjustment_note',
            // Pembayaran diurutkan terbaru dulu
            'salePayments' => function ($q) {
                $q->select('id', 'sale_id', 'payment_method', 'bank_name', 'date')->orderByDesc('date')->orderByDesc('id');
            },
        ]);

        // Kompatibel dengan preXhr
        $preset = request('preset');
        $month = request('month');
        $from = request('from');
        $to = request('to');

        if ($from && $to) {
            $q->whereBetween('date', [$from, $to]);
        } elseif ($month) {
            [$y, $m] = explode('-', $month);
            $q->whereYear('date', (int) $y)->whereMonth('date', (int) $m);
        } elseif ($preset) {
            if ($preset === 'today') {
                $q->whereDate('date', now()->toDateString());
            } elseif ($preset === 'this_week') {
                $q->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
            } elseif ($preset === 'this_month') {
                $q->whereYear('date', now()->year)->whereMonth('date', now()->month);
            } elseif ($preset === 'last_month') {
                $q->whereYear('date', now()->subMonth()->year)->whereMonth('date', now()->subMonth()->month);
            } elseif ($preset === 'this_year') {
                $q->whereYear('date', now()->year);
            }
        }

        return $q->select(
            'id',
            'reference',
            'date',
            'status',
            'total_amount',
            'paid_amount',
            'due_amount',
            'payment_status',
            'payment_method',
            'bank_name',
            'user_id',
            'has_price_adjustment', // ✅ TAMBAH KOLOM INI
        );
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('sales-table')
            ->columns($this->getColumns())
            ->ajax([
                'type' => 'GET',
                'data' => 'function(d){
                    d.filter = {
                        preset: $("#filter_preset").val(),
                        bulan: $("#filter_bulan").val(),
                        tahun: $("#filter_tahun").val(),
                        dari: $("#filter_dari").val(),
                        sampai: $("#filter_sampai").val(),
                        has_adjustment: $("#filter_has_adjustment").val() // ✅ TAMBAH INI
                    };
                }',
            ])
            ->parameters([
                'language' => ['url' => 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json'],
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'autoWidth' => false,
                'order' => [[2, 'desc']],
            ])
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>>tr<'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->buttons(Button::make('excel')->text('<i class="bi bi-file-excel"></i> Excel'), Button::make('print')->text('<i class="bi bi-printer"></i> Cetak'), Button::make('reset')->text('<i class="bi bi-arrow-clockwise"></i> Reset'), Button::make('reload')->text('<i class="bi bi-arrow-repeat"></i> Muat Ulang'));
    }

    protected function getColumns()
    {
        return [
            Column::computed('row_detail')->title('')->exportable(false)->printable(false)->width(32)->className('text-center align-middle'),
            Column::make('reference')->title('Ref')->className('text-center align-middle'),
            Column::make('date')->title('Tanggal')->className('text-center align-middle'),
            Column::computed('status')->title('Status')->className('text-center align-middle'),
            Column::computed('total_amount')->title('Total')->className('text-center align-middle'),
            Column::computed('total_profit')->title('Profit')->className('text-center align-middle'),
            Column::computed('paid_amount')->title('Dibayar')->className('text-center align-middle'),
            Column::computed('due_amount')->title('Kurang')->className('text-center align-middle'),
            Column::computed('payment_status')->title('Status Bayar')->className('text-center align-middle'),
            Column::computed('payment_method')->title('Pembayaran')->className('text-center align-middle'),

            // ✅ KOLOM BARU
            Column::computed('price_adjustment')->title('Diskon')->className('text-center align-middle'),

            Column::computed('action')->title('Aksi')->exportable(false)->printable(false)->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Penjualan_' . date('YmdHis');
    }
}
