<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // REVISI: Tetap untuk date handling di chart

use Gloudemans\Shoppingcart\Facades\Cart;

// Imports untuk index() saja (REVISI: Hanya yang dipakai di modules Anda: Product, Sale)
use Modules\Sale\Entities\Sale;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\ProductSecond;

// Imports untuk chart methods (REVISI: Hanya modules ada: Sale, Purchase, Expense; HAPUS PurchasesReturn/SalesReturn)
use Modules\Expense\Entities\Expense;
use Modules\Purchase\Entities\Purchase;
// HAPUS: use Modules\PurchasesReturn\Entities\PurchaseReturn;  // Gak ada module
// HAPUS: use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;  // Gak ada
// HAPUS: use Modules\SalesReturn\Entities\SaleReturn;  // Gak ada
// HAPUS: use Modules\SalesReturn\Entities\SaleReturnPayment;  // Gak ada
// Asumsi: SalePayment dan PurchasePayment ada di Sale/Purchase modules (jika gak, hapus di paymentChart)

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'unit')->count();
        $categories = Category::count();

        // REVISI: Tetap, ?? 0 untuk safety jika no sales
        $sales = DB::table('sales')->select(DB::raw('SUM(total_amount) as total_amount'))->where('status', 'Completed')->first();
        $revenue = $sales ? $sales->total_amount : 0;

        // ✅ Permission check untuk data sensitive (REVISI: Tetap, pakai Sale/Product modules)
        if (auth()->user()->can('report.view_profit')) {
            // Owner & Supervisor bisa lihat profit
            $profit = DB::table('sale_details')->join('sales', 'sales.id', '=', 'sale_details.sale_id')->select(DB::raw('SUM((sale_details.price - COALESCE(products.product_cost, 0)) * sale_details.quantity) as total_profit'))->leftJoin('products', 'products.id', '=', 'sale_details.product_id')->where('sales.status', 'Completed')->value('total_profit') ?? 0;
        } else {
            $profit = 0; // Kasir tidak bisa lihat profit
        }

        $cart_instance = Cart::instance('sale');
        $cart_data = $cart_instance->content();
        $cart_count = $cart_instance->count();
        $cart_total = $cart_instance->total();

        // REVISI: Tetap Eloquent dari Sale module
        $recent_sales = Sale::latest('created_at')->take(5)->get();

        $productsecond = ProductSecond::count();

        // ✅ Low stock alert (hanya untuk Owner & Supervisor) - pakai Product module
        if (auth()->user()->can('inventory.view_products')) {
            $lowStockProducts = Product::with('unit')->whereColumn('product_quantity', '<=', 'product_stock_alert')->orderBy('product_quantity', 'ASC')->take(5)->get();
        } else {
            $lowStockProducts = collect(); // Empty collection untuk Kasir
        }

        return view('home', [
            'products' => $products,
            'categories' => $categories,
            'revenue' => $revenue,
            'profit' => $profit,
            'cart_count' => $cart_count,
            'cart_data' => $cart_data,
            'cart_total' => $cart_total,
            'recent_sales' => $recent_sales,
            'productsecond' => $productsecond,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    public function currentMonthChart()
    {
        abort_if(!request()->ajax(), 404);

        // REVISI: Pakai modules ada: Sale, Purchase, Expense
        $currentMonthSales = Sale::where('status', 'Completed')->whereMonth('date', date('m'))->whereYear('date', date('Y'))->sum('total_amount');
        $currentMonthPurchases = Purchase::where('status', 'Completed')->whereMonth('date', date('m'))->whereYear('date', date('Y'))->sum('total_amount');
        $currentMonthExpenses = Expense::whereMonth('date', date('m'))->whereYear('date', date('Y'))->sum('amount');

        return response()->json([
            'sales' => $currentMonthSales,
            'purchases' => $currentMonthPurchases,
            'expenses' => $currentMonthExpenses,
        ]);
    }

    public function salesPurchasesChart()
    {
        abort_if(!request()->ajax(), 404);

        // REVISI: Pakai modules Sale/Purchase
        $salesData = $this->salesChartDataArray();
        $purchasesData = $this->purchasesChartDataArray();

        return response()->json([
            'sales' => $salesData['data'],
            'purchases' => $purchasesData['data'],
            'days' => $salesData['days'],
        ]);
    }

    public function paymentChart()
    {
        abort_if(!request()->ajax(), 404);

        $dates = collect();
        foreach (range(-11, 0) as $i) {
            $date = Carbon::now()->addMonths($i)->format('m-Y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::now()->subYear()->format('Y-m-d');

        // REVISI: HAPUS SaleReturnPayment & PurchaseReturnPayment (gak ada modules); Asumsi SalePayment & PurchasePayment ada di Sale/Purchase
        $sale_payments = DB::table('sale_payments') // Ganti ke DB::table jika model gak ada
            ->where('date', '>=', $date_range)
            ->select([DB::raw("DATE_FORMAT(date, '%m-%Y') as month"), DB::raw('SUM(amount) as amount')])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('amount', 'month');

        // HAPUS: $sale_return_payments = ...  // Gak ada SalesReturn module

        $purchase_payments = DB::table('purchase_payments') // Ganti ke DB::table jika model gak ada
            ->where('date', '>=', $date_range)
            ->select([DB::raw("DATE_FORMAT(date, '%m-%Y') as month"), DB::raw('SUM(amount) as amount')])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('amount', 'month');

        // HAPUS: $purchase_return_payments = ...  // Gak ada PurchasesReturn module

        $expenses = Expense::where('date', '>=', $date_range)
            ->select([DB::raw("DATE_FORMAT(date, '%m-%Y') as month"), DB::raw('SUM(amount) as amount')])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('amount', 'month');

        // REVISI: Adjust merge (tanpa return payments)
        $payment_received = $sale_payments; // Cuma sales
        $payment_sent = $purchase_payments->merge($expenses); // Purchases + expenses

        $dates_received = $dates->merge($payment_received);
        $dates_sent = $dates->merge($payment_sent);

        $received_payments = $dates_received->values()->toArray(); // REVISI: Sederhana ke array
        $sent_payments = $dates_sent->values()->toArray();
        $months = $dates_received->keys()->values()->toArray();

        return response()->json([
            'payment_sent' => $sent_payments,
            'payment_received' => $received_payments,
            'months' => $months,
        ]);
    }

    // REVISI: Private array untuk Sale module
    private function salesChartDataArray()
    {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-Y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);

        $sales = Sale::where('status', 'Completed')
            ->where('date', '>=', $date_range)
            ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%Y')"))
            ->orderBy('date')
            ->get([DB::raw("DATE_FORMAT(date,'%d-%m-%Y') as date"), DB::raw('SUM(total_amount) AS count')])
            ->pluck('count', 'date');

        $dates = $dates->merge($sales);

        return [
            'data' => $dates->values()->toArray(),
            'days' => $dates->keys()->values()->toArray(),
        ];
    }

    // REVISI: Private array untuk Purchase module
    private function purchasesChartDataArray()
    {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-Y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);

        $purchases = Purchase::where('status', 'Completed')
            ->where('date', '>=', $date_range)
            ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%Y')"))
            ->orderBy('date')
            ->get([DB::raw("DATE_FORMAT(date,'%d-%m-%Y') as date"), DB::raw('SUM(total_amount) AS count')])
            ->pluck('count', 'date');

        $dates = $dates->merge($purchases);

        return [
            'data' => $dates->values()->toArray(),
            'days' => $dates->keys()->values()->toArray(),
        ];
    }
}
