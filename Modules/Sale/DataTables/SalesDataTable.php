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

            // Tombol expand (row detail)
            ->addColumn('row_detail', function ($s) {
                $id = (int) $s->id;
                $url = route('sales.items', ['sale' => $id]);
                return '<button type="button" class="btn btn-sm btn-primary btn-expand" data-url="' . $url . '"><i class="bi bi-chevron-down"></i></button>';
            })

            // Tanggal
            ->editColumn('date', fn($s) => Carbon::parse($s->date)->locale('id')->translatedFormat('d M Y'))

            // Status & Payment status pakai partials
            ->addColumn('status', fn($s) => view('sale::partials.status', ['data' => $s]))
            ->addColumn('payment_status', fn($s) => view('sale::partials.payment-status', ['data' => $s]))

            // Uang
            ->addColumn('total_amount', fn($s) => format_currency((int) $s->total_amount))
            ->addColumn('paid_amount', fn($s) => format_currency((int) $s->paid_amount))
            ->addColumn('due_amount', fn($s) => format_currency((int) $s->due_amount))

            // Profit (pakai subtotal_profit jika ada, fallback hitung dari HPP)
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

            // Kasir
            ->addColumn('kasir', fn($s) => $s->user->name ?? '—')

            // Metode bayar terakhir (null-safe)
            ->addColumn('payment_method', function ($s) {
                $last = $s->salePayments->first(); // sudah diurut desc di with()
                $method = $last?->payment_method ?? ($s->payment_method ?? '-');
                $bank = $last?->bank_name ?? $s->bank_name;
                return $bank ? "{$method} ({$bank})" : $method;
            })

            // Indikator Input Manual
            ->addColumn('input_manual', function ($s) {
                if ((int) $s->has_manual_input === 1) {
                    $cnt = (int) ($s->manual_input_count ?? 0);
                    return '<span class="badge badge-warning">' . $cnt . ' item manual</span>';
                }
                return '<span class="text-muted">—</span>';
            })

            // Ringkasan Diskon / Edit Harga
            ->addColumn('price_adjustment', function ($s) {
                if ((int) $s->has_price_adjustment !== 1) {
                    return '<span class="badge badge-secondary badge-sm">-</span>';
                }

                $totalAdjustment = $s->saleDetails->where('is_price_adjusted', 1)->sum('price_adjustment_amount');

                $itemCount = $s->adjusted_items_count ?? $s->saleDetails->where('is_price_adjusted', 1)->count();

                if ($totalAdjustment > 0) {
                    return '<span class="badge badge-warning badge-sm" title="' . $itemCount . ' item dengan diskon"><i class="bi bi-tag-fill"></i> Rp ' . number_format($totalAdjustment, 0, ',', '.') . '</span>';
                } elseif ($totalAdjustment < 0) {
                    return '<span class="badge badge-success badge-sm" title="' . $itemCount . ' item dengan kenaikan harga"><i class="bi bi-arrow-up-circle"></i> +Rp ' . number_format(abs($totalAdjustment), 0, ',', '.') . '</span>';
                }
                return '<span class="badge badge-secondary badge-sm">-</span>';
            })

            // Aksi
            ->addColumn('action', fn($s) => view('sale::partials.actions', ['data' => $s]))

            ->rawColumns(['row_detail', 'status', 'payment_status', 'input_manual', 'price_adjustment', 'action'])

            // FILTER robust: dukung top-level & nested d.filter.*
            ->filter(function ($q) {
                $f = request('filter', []);
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

                // Preset
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

                // Bulan spesifik (YYYY-MM)
                if (!empty($bulan) && strpos($bulan, '-') !== false) {
                    [$y, $m] = explode('-', $bulan);
                    $q->whereYear('date', (int) $y)->whereMonth('date', (int) $m);
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

        return $q->select('id', 'reference', 'date', 'status', 'total_amount', 'paid_amount', 'due_amount', 'payment_status', 'payment_method', 'bank_name', 'user_id', 'has_price_adjustment', 'has_manual_input', 'manual_input_count');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('sales-table')
            ->columns($this->getColumns())

            // SAFE: kembalikan object; d.filter diisi dari UI
            ->minifiedAjax('', null, [
                'data' => /** @lang JavaScript */ 'function (d) {
                    var f = {
                        preset: $("#filter_preset").val() || "",
                        bulan:  $("#filter_bulan").val()   || "",
                        dari:   $("#filter_dari").val()    || "",
                        sampai: $("#filter_sampai").val()  || "",
                        has_adjustment: $("#filter_has_adjustment").length && $("#filter_has_adjustment").is(":checked") ? 1 : "",
                        has_manual:     $("#filter_has_manual").length     && $("#filter_has_manual").is(":checked")     ? 1 : ""
                    };
                    return $.extend({}, d || {}, { filter: f });
                }',
            ])
            ->parameters([
                'language' => ['url' => 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json'],
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'autoWidth' => false,
                'order' => [[2, 'desc']], // index 2 = kolom "Tanggal"
            ]);
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

            Column::computed('kasir')->title('Kasir')->className('text-center align-middle'),
            Column::computed('input_manual')->title('Input Manual')->className('text-center align-middle'),
            Column::computed('price_adjustment')->title('Diskon')->className('text-center align-middle'),

            Column::computed('action')->title('Aksi')->exportable(false)->printable(false)->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Penjualan_' . date('YmdHis');
    }
}
