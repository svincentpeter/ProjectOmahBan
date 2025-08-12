<?php

namespace Modules\Sale\DataTables;

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

            // Tombol expand: URL final disisipkan sebagai data-url
            ->addColumn('row_detail', function ($s) {
                $id  = (int) $s->id;
                $url = route('sales.items', ['sale' => $id]);
                return '<button type="button" class="btn btn-sm btn-outline-secondary btn-row-detail" '
                    .'data-id="'.$id.'" data-url="'.$url.'" title="Lihat item">'
                    .'<i class="bi bi-caret-down-square"></i></button>';
            })

            ->editColumn('date', fn ($s) => Carbon::parse($s->date)->locale('id')->translatedFormat('d M Y'))
            ->addColumn('status', fn ($s) => view('sale::partials.status', ['data' => $s]))
            ->addColumn('payment_status', fn ($s) => view('sale::partials.payment-status', ['data' => $s]))
            ->addColumn('total_amount', fn ($s) => format_currency((int)$s->total_amount))
            ->addColumn('paid_amount',  fn ($s) => format_currency((int)$s->paid_amount))
            ->addColumn('due_amount',   fn ($s) => format_currency((int)$s->due_amount))

            // Profit = sum(sub_total) - sum(hpp * qty) dari relasi
            ->addColumn('total_profit', function ($s) {
                $profit = $s->saleDetails->sum(function ($d) {
                    $qty   = (int)($d->quantity ?? 0);
                    $sub   = (int)($d->sub_total ?? 0);
                    $hppT  = (float)($d->hpp ?? 0) * $qty;
                    return $sub - (int)round($hppT);
                });
                return format_currency((int)$profit);
            })

            ->addColumn('payment_method', function ($s) {
                $m = $s->payment_method ?: '-';
                return !empty($s->bank_name) ? "$m ($s->bank_name)" : $m;
            })
            ->addColumn('action', fn ($s) => view('sale::partials.actions', ['data' => $s]))
            ->rawColumns(['row_detail','status','payment_status','action'])

            // Filter server-side
            ->filter(function ($q) {
                $filter = $this->request()->get('filter', []);
                $preset = $filter['preset'] ?? null;
                $bulan  = $filter['bulan']  ?? null; // YYYY-MM
                $tahun  = $filter['tahun']  ?? null; // YYYY
                $dari   = $filter['dari']   ?? null; // YYYY-MM-DD
                $sampai = $filter['sampai'] ?? null; // YYYY-MM-DD

                if ($dari && $sampai) {
                    $q->whereBetween('date', [$dari, $sampai]);
                    return;
                }

                if ($preset) {
                    $now = Carbon::now();
                    switch ($preset) {
                        case 'today':
                            $q->whereDate('date', $now->toDateString()); return;
                        case 'this_week':
                            $q->whereBetween('date', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]); return;
                        case 'this_month':
                            $q->whereBetween('date', [$now->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString()]); return;
                        case 'last_month':
                            $start = $now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString();
                            $end   = $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();
                            $q->whereBetween('date', [$start, $end]); return;
                        case 'this_year':
                            $q->whereYear('date', $now->year); return;
                    }
                }

                if ($bulan) {
                    [$y, $m] = explode('-', $bulan);
                    $q->whereYear('date', (int)$y)->whereMonth('date', (int)$m);
                } elseif ($tahun) {
                    $q->whereYear('date', (int)$tahun);
                }
            });
    }

    public function query(Sale $model)
    {
        $q = $model->newQuery()->with([
            'saleDetails' => function ($r) {
                $r->select(
                    'id','sale_id','item_name','product_id',
                    'productable_id','productable_type','source_type',
                    'product_name','product_code','quantity','price',
                    'hpp','unit_price','sub_total','subtotal_profit'
                );
            }
        ]);

        // Kompatibel dengan preXhr (range > month > preset)
        $preset = request('preset');
        $month  = request('month'); // YYYY-MM
        $from   = request('from');
        $to     = request('to');

        if ($from && $to) {
            $q->whereBetween('date', [$from, $to]);
        } elseif ($month) {
            [$y,$m] = explode('-', $month);
            $q->whereYear('date', (int)$y)->whereMonth('date', (int)$m);
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
            'id','reference','date','status',
            'total_amount','paid_amount','due_amount',
            'payment_status','payment_method','bank_name','user_id'
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
                        bulan:  $("#filter_bulan").val(),
                        tahun:  $("#filter_tahun").val(),
                        dari:   $("#filter_dari").val(),
                        sampai: $("#filter_sampai").val()
                    };
                }',
            ])
            ->parameters([
                'language'   => ['url' => 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json'],
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'autoWidth'  => false,
                'order'      => [[2, 'desc']], // 0=expand, 1=Ref, 2=Tanggal
            ])
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>>tr<'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
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
            Column::computed('action')->title('Aksi')->exportable(false)->printable(false)->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Penjualan_' . date('YmdHis');
    }
}
