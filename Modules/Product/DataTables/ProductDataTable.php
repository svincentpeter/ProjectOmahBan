<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\Product;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{
    public function dataTable($query)
{
    return datatables()
        ->eloquent($query)
        ->addColumn('action', function ($data) {
            // tombol edit/hapus dsb (HTML)
            return view('product::products.partials.actions', compact('data'))->render();
        })
        ->addColumn('product_price', fn($d) => format_currency($d->product_price))
        ->addColumn('product_cost',  fn($d) => format_currency($d->product_cost))
        ->addColumn('stok_sisa',     fn($d) => $d->product_quantity.' '.$d->product_unit)
        ->addColumn('merk',          fn($d) => $d->brand? $d->brand->name : '-')
        ->rawColumns(['action']); // <-- hanya kolom aksi yang raw
}


    public function query(Product $model)
    {
        // Eager loading relasi 'category' dan 'brand' untuk performa query yang lebih cepat
        return $model->newQuery()->with(['category', 'brand']);
    }

    public function html()
    {
        return $this->builder()
                    ->setTableId('product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
                    ->orderBy(0, 'asc') // Urutkan berdasarkan kolom pertama (Nama Barang)
                    ->buttons(
                        Button::make('excel')->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                        Button::make('print')->text('<i class="bi bi-printer-fill"></i> Cetak'),
                        Button::make('reset')->text('<i class="bi bi-x-circle"></i> Reset'),
                        Button::make('reload')->text('<i class="bi bi-arrow-repeat"></i> Muat Ulang')
                    );
    }

    protected function getColumns()
    {
        // Mendefinisikan semua kolom yang Anda minta, sesuai urutan.
        return [
            Column::make('product_name')
                ->title('Nama Barang')
                ->className('align-middle'),

            Column::computed('merk')
                ->title('Merk')
                ->className('text-center align-middle'),

            Column::make('product_year')
                ->title('Tahun')
                ->className('text-center align-middle'),

            Column::make('product_size')
                ->title('Ukuran')
                ->className('text-center align-middle'),

            Column::make('ring')
                ->title('Ring')
                ->className('text-center align-middle'),

            Column::computed('product_cost')
                ->title('Modal')
                ->className('text-center align-middle'),

            Column::computed('product_price')
                ->title('Harga Jual')
                ->className('text-center align-middle'),

            Column::make('stok_awal')
                ->title('Stok Awal')
                ->className('text-center align-middle'),

            Column::computed('stok_sisa')
                ->title('Stok Sisa')
                ->className('text-center align-middle'),

            Column::make('product_stock_alert')
                ->title('Stok Min.')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Daftar_Produk_' . date('YmdHis');
    }
}