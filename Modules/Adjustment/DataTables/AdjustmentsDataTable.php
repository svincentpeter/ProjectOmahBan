<?php

namespace Modules\Adjustment\DataTables;

use Modules\Adjustment\Entities\Adjustment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AdjustmentsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($adjustment) {
                return view('adjustment::adjustments.partials.actions', compact('adjustment'));
            })
            ->editColumn('date', function ($adjustment) {
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-zinc-100 text-zinc-700"><i class="bi bi-calendar3 me-1"></i>' . \Carbon\Carbon::parse($adjustment->date)->format('d M Y') . '</span>';
            })
            ->editColumn('reference', function ($adjustment) {
                return '<span class="font-bold text-blue-600">' . e($adjustment->reference) . '</span>';
            })
            ->addColumn('status', function ($adjustment) {
                $styles = match ($adjustment->status) {
                    'pending' => 'bg-amber-100 text-amber-700',
                    'approved' => 'bg-emerald-100 text-emerald-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    default => 'bg-zinc-100 text-zinc-600',
                };
                $icon = match ($adjustment->status) {
                    'pending' => 'bi-hourglass-split',
                    'approved' => 'bi-check-circle-fill',
                    'rejected' => 'bi-x-circle-fill',
                    default => 'bi-circle',
                };
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ' . $styles . '"><i class="bi ' . $icon . ' me-1"></i>' . ucfirst($adjustment->status) . '</span>';
            })
            ->addColumn('requester', function ($adjustment) {
                return '<span class="text-zinc-600 text-sm font-medium"><i class="bi bi-person me-1"></i>' . e($adjustment->requester->name ?? '-') . '</span>';
            })
            ->addColumn('approver', function ($adjustment) {
                if ($adjustment->status === 'pending') {
                    return '<span class="text-zinc-400 text-sm italic">Menunggu</span>';
                }
                return '<span class="text-emerald-600 text-sm font-medium"><i class="bi bi-person-check me-1"></i>' . e($adjustment->approver->name ?? '-') . '</span>';
            })
            ->addColumn('products_count', function ($adjustment) {
                $count = $adjustment->adjusted_products_count ?? 0;
                $styles = $count > 5 ? 'bg-emerald-100 text-emerald-700' : ($count > 2 ? 'bg-blue-100 text-blue-700' : 'bg-zinc-100 text-zinc-600');
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ' . $styles . '"><i class="bi bi-box-seam me-1"></i>' . $count . ' Produk</span>';
            })
            ->addColumn('types_summary', function ($adjustment) {
                $adjustedProducts = $adjustment->adjustedProducts;
                $addCount = $adjustedProducts->where('type', 'add')->count();
                $subCount = $adjustedProducts->where('type', 'sub')->count();

                $html = '<div class="flex items-center gap-1 flex-wrap">';

                if ($addCount > 0) {
                    $html .= '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">';
                    $html .= '<i class="bi bi-plus-circle me-1"></i>' . $addCount . ' Tambah';
                    $html .= '</span>';
                }

                if ($subCount > 0) {
                    $html .= '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">';
                    $html .= '<i class="bi bi-dash-circle me-1"></i>' . $subCount . ' Kurang';
                    $html .= '</span>';
                }

                $html .= '</div>';

                return $html ?: '<span class="text-zinc-400 italic">-</span>';
            })
            ->editColumn('note', function ($adjustment) {
                if (empty($adjustment->note)) {
                    return '<span class="text-zinc-400 text-sm italic">Tidak ada catatan</span>';
                }

                $note = \Illuminate\Support\Str::limit($adjustment->note, 50);
                return '<span class="text-zinc-700 text-sm">' . e($note) . '</span>';
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', 'like', "%{$keyword}%");
            })
            ->filterColumn('requester', function ($query, $keyword) {
                $query->whereHas('requester', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['action', 'date', 'reference', 'status', 'requester', 'approver', 'products_count', 'types_summary', 'note']);
    }

    public function query(Adjustment $model)
    {
        $query = $model
            ->newQuery()
            ->withCount('adjustedProducts')
            ->with(['adjustedProducts', 'requester', 'approver'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter untuk owner: tampilkan semua; untuk staff: hanya milik sendiri
        if (!auth()->user()->hasRole('owner')) {
            $query->where('requester_id', auth()->id());
        }

        // Apply custom filters from request
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('type')) {
            $query->whereHas('adjustedProducts', function($q) {
                $q->where('type', request('type'));
            });
        }

        if (request()->filled('requester_id')) {
            $query->where('requester_id', request('requester_id'));
        }

        if (request()->filled('date_from')) {
            $query->whereDate('date', '>=', request('date_from'));
        }

        if (request()->filled('date_to')) {
            $query->whereDate('date', '<=', request('date_to'));
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('adjustments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'order' => [[0, 'desc']],
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('date')->title('Tanggal')->width('12%')->className('align-middle'),

            Column::make('reference')->title('Referensi')->width('12%')->className('align-middle'),

            Column::computed('status')->title('Status')->width('10%')->searchable(true)->orderable(true)->className('align-middle'),

            Column::computed('requester')->title('Pengaju')->width('12%')->searchable(true)->orderable(false)->className('align-middle'),

            Column::computed('approver')->title('Approver')->width('12%')->searchable(false)->orderable(false)->className('align-middle'),

            Column::computed('products_count')->title('Jumlah Produk')->width('10%')->searchable(false)->orderable(false)->className('align-middle'),

            Column::computed('types_summary')->title('Tipe')->width('12%')->searchable(false)->orderable(false)->className('align-middle'),

            Column::make('note')->title('Catatan')->width('12%')->className('align-middle'),

            Column::computed('action')->title('Aksi')->exportable(false)->printable(false)->width('8%')->className('text-center align-middle')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Penyesuaian_Stok_' . date('d-m-Y_His');
    }
}
