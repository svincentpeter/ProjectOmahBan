<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sale\Entities\SaleDetails; // Data laporan laba dari SaleDetails
use Modules\Product\Entities\Product;  // Data produk dari Product
use Carbon\Carbon; // Library untuk mengelola tanggal

class ReportController extends Controller
{
    public function profitReport(Request $request)
    {
        // Tetapkan tanggal default: bulan ini
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Query utama untuk mengambil data profit
        $details = SaleDetails::whereHas('sale', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })->get();

        // Menghitung total
        $totalPenjualan = $details->sum('sub_total');
        $totalHpp = $details->sum(function ($detail) {
            return $detail->hpp * $detail->quantity;
        });
        $totalLaba = $details->sum('subtotal_profit');

        // Menghitung rincian laba per jenis sumber
        $labaBreakdown = $details->groupBy('source_type')->map(function ($items, $type) {
            return [
                'total_penjualan' => $items->sum('sub_total'),
                'total_laba'      => $items->sum('subtotal_profit'),
                'count'           => $items->sum('quantity')
            ];
        });

        return view('sale::reports.profit', compact(
            'totalPenjualan',
            'totalHpp',
            'totalLaba',
            'labaBreakdown',
            'startDate',
            'endDate'
        ));
    }

    // ===================== Tambahan laporan stok menipis =====================
    public function lowStockReport(Request $request)
    {
        // Ambil batas stok minimum, default = 5 (bisa dibuat dinamis jika mau)
        $limit = $request->input('limit', 5);

        // Ambil produk dengan stok <= limit
        $products = Product::where('product_quantity', '<=', $limit)->get();

        return view('sale::reports.low_stock', compact('products', 'limit'));
    }
}
