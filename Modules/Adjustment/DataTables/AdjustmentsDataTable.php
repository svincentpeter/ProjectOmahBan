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
                return view('adjustment::partials.actions', compact('adjustment'));
            })
            ->editColumn('date', function ($adjustment) {
                return '<div class="text-center">' . '<span class="badge badge-light">' . '<i class="bi bi-calendar3"></i> ' . \Carbon\Carbon::parse($adjustment->date)->format('d M Y') . '</span>' . '</div>';
            })
            ->editColumn('reference', function ($adjustment) {
                return '<div class="text-center">' . '<strong class="text-primary">' . e($adjustment->reference) . '</strong>' . '</div>';
            })
            ->addColumn('status', function ($adjustment) {
                $badgeClass = match ($adjustment->status) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary',
                };
                return '<div class="text-center">' . '<span class="badge badge-' . $badgeClass . '">' . '<i class="bi bi-' . ($adjustment->status === 'pending' ? 'clock-history' : ($adjustment->status === 'approved' ? 'check-circle' : 'x-circle')) . '"></i> ' . ucfirst($adjustment->status) . '</span>' . '</div>';
            })
            ->addColumn('requester', function ($adjustment) {
                return '<div class="text-center">' . '<small class="text-muted">' . '<i class="bi bi-person"></i> ' . e($adjustment->requester->name ?? '-') . '</small>' . '</div>';
            })
            ->addColumn('approver', function ($adjustment) {
                if ($adjustment->status === 'pending') {
                    return '<div class="text-center"><small class="text-muted font-italic">Menunggu</small></div>';
                }
                return '<div class="text-center">' . '<small class="text-success">' . '<i class="bi bi-person-check"></i> ' . e($adjustment->approver->name ?? '-') . '</small>' . '</div>';
            })
            ->addColumn('products_count', function ($adjustment) {
                $count = $adjustment->adjusted_products_count ?? 0;
                $badgeClass = $count > 5 ? 'success' : ($count > 2 ? 'info' : 'secondary');

                return '<div class="text-center">' . '<span class="badge badge-' . $badgeClass . ' badge-pill">' . '<i class="bi bi-box-seam"></i> ' . $count . ' Produk' . '</span>' . '</div>';
            })
            ->addColumn('types_summary', function ($adjustment) {
                $adjustedProducts = $adjustment->adjustedProducts;
                $addCount = $adjustedProducts->where('type', 'add')->count();
                $subCount = $adjustedProducts->where('type', 'sub')->count();

                $html = '<div class="text-center">';

                if ($addCount > 0) {
                    $html .= '<span class="badge badge-success mr-1">';
                    $html .= '<i class="bi bi-plus-circle"></i> ' . $addCount . ' Tambah';
                    $html .= '</span>';
                }

                if ($subCount > 0) {
                    $html .= '<span class="badge badge-danger">';
                    $html .= '<i class="bi bi-dash-circle"></i> ' . $subCount . ' Kurang';
                    $html .= '</span>';
                }

                $html .= '</div>';

                return $html ?: '<div class="text-center"><span class="text-muted font-italic">-</span></div>';
            })
            ->editColumn('note', function ($adjustment) {
                if (empty($adjustment->note)) {
                    return '<small class="text-muted font-italic">Tidak ada catatan</small>';
                }

                $note = \Illuminate\Support\Str::limit($adjustment->note, 50);
                return '<small class="text-dark">' . e($note) . '</small>';
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

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('adjustments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-5'B><'col-sm-12 col-md-4'f>>" . "<'row'<'col-sm-12'tr>>" . "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>")
            ->orderBy(0, 'desc')
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'processing' => true,
                'language' => [
                    'processing' => '<div class="spinner-border text-primary" role="status"><span class="sr-only">Memuat...</span></div>',
                    'search' => '',
                    'searchPlaceholder' => 'Cari referensi, status, requester...',
                    'lengthMenu' => 'Tampilkan _MENU_ data',
                    'info' => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    'infoEmpty' => 'Tidak ada data',
                    'infoFiltered' => '(disaring dari _MAX_ total data)',
                    'zeroRecords' => 'Data tidak ditemukan',
                    'emptyTable' => 'Belum ada data penyesuaian stok',
                    'paginate' => [
                        'first' => 'Pertama',
                        'last' => 'Terakhir',
                        'next' => 'Selanjutnya',
                        'previous' => 'Sebelumnya',
                    ],
                ],
            ])
            ->buttons(Button::make('excel')->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel')->className('btn btn-success btn-sm'), Button::make('print')->text('<i class="bi bi-printer-fill"></i> Print')->className('btn btn-info btn-sm'), Button::make('reset')->text('<i class="bi bi-x-circle"></i> Reset')->className('btn btn-secondary btn-sm'), Button::make('reload')->text('<i class="bi bi-arrow-repeat"></i> Muat Ulang')->className('btn btn-warning btn-sm'));
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
