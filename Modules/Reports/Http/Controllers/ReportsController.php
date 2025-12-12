<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Modules\Expense\Entities\Expense;
use Modules\Reports\Http\Requests\ProfitLossRequest;
use Modules\Reports\Http\Requests\DailyReportRequest;
use Modules\SalesReturn\Entities\SaleReturn;
use Modules\SalesReturn\Entities\SaleReturnDetail;

// (Opsional next phase) use Modules\Sale\Entities\SaleReturn; use Modules\Purchase\Entities\Purchase; dst.

class ReportsController extends Controller
{
    /* =========================
     *  INDEX (Redirect to Daily)
     * ========================= */
    public function index()
    {
        return redirect()->route('reports.daily.index');
    }

    /* =========================
     *  RINGKAS / DAILY REPORT
     * ========================= */
    public function dailyIndex(Request $request)
    {
        $today = now()->toDateString();

        // View: reports::ringkas.daily   (kalau belum dipindah, sementara pakai reports::daily_report)
        $view = view()->exists('reports::ringkas.daily') ? 'reports::ringkas.daily' : 'reports::daily_report';

        return view($view, [
            'reportDate'        => $request->input('report_date', $today),
            'sales'             => collect(),
            'expenses'          => collect(),
            'totalOmset'        => 0,
            'totalPengeluaran'  => 0,
            'netIncome'         => 0,
            'receipts'          => collect(),
            'generated'         => false,
        ]);
    }

    public function generateDailyReport(DailyReportRequest $request)
    {
        $data = $request->validate([
            'report_date' => ['required', 'date', 'date_format:Y-m-d'],
        ]);
        $reportDate = Carbon::parse($data['report_date'])->toDateString();

        $sales = Sale::query()
            ->with([
                'saleDetails' => fn($q) => $q->select('id','sale_id','item_name','product_name','quantity','unit_price','sub_total'),
                'payments'    => fn($q) => $q->select('id','sale_id','amount','date','reference','payment_method','bank_name'),
            ])
           ->whereDate('date', $reportDate)
            ->orderBy('created_at')
            ->get(['id','date','reference','total_amount','created_at']);

        $expenses = Expense::query()
            ->with(['category:id,category_name','user:id,name'])
            ->whereDate('date', $reportDate)
            ->orderBy('created_at')
            ->get(['id','date','reference','category_id','details','amount','user_id','created_at']);

        $totalOmset       = (int) $sales->sum('total_amount');
        $totalPengeluaran = (int) $expenses->sum('amount');
        $netIncome        = $totalOmset - $totalPengeluaran;

        $receipts = SalePayment::query()
            ->select([
                'payment_method',
                DB::raw("COALESCE(bank_name, '—') AS bank_name"),
                DB::raw('SUM(amount) AS total'),
            ])
            ->whereDate('date', $reportDate)
            ->groupBy('payment_method', 'bank_name')
            ->orderBy('payment_method')->orderBy('bank_name')
            ->get();

        $view = view()->exists('reports::ringkas.daily') ? 'reports::ringkas.daily' : 'reports::daily_report';

        return view($view, [
            'reportDate'        => $reportDate,
            'sales'             => $sales,
            'expenses'          => $expenses,
            'totalOmset'        => $totalOmset,
            'totalPengeluaran'  => $totalPengeluaran,
            'netIncome'         => $netIncome,
            'receipts'          => $receipts,
            'generated'         => true,
        ]);
    }

    /* =========================
     *  PROFIT & LOSS (P&L)
     * ========================= */
    public function profitLossIndex(Request $request)
{
    $start = $request->input('start_date', now()->startOfMonth()->toDateString());
    $end   = $request->input('end_date', now()->toDateString());

    return view('reports::profit-loss.index', [
        'startDate'          => $start,
        'endDate'            => $end,
        'revenue'            => 0,
        'cogs'               => 0,
        'grossProfit'        => 0,
        'operatingExpenses'  => 0,
        'netProfit'          => 0,
        'generated'          => false,
    ]);
}


    public function generateProfitLossReport(ProfitLossRequest $request)
    {
        // 1) Validasi range
        $data = $request->validate([
            'start_date' => ['required', 'date', 'date_format:Y-m-d'],
            'end_date'   => ['required', 'date', 'date_format:Y-m-d'],
        ]);
        $startDate = Carbon::parse($data['start_date'])->toDateString();
        $endDate   = Carbon::parse($data['end_date'])->toDateString();
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // 2) Revenue (penjualan selesai)
        $revenue = (int) Sale::completed()
    ->between($startDate, $endDate)
    ->sum('total_amount');

        // 3) COGS / HPP
        $cogs    = (int) Sale::completed()
    ->between($startDate, $endDate)
    ->sum('total_hpp');

    // Total nominal return penjualan (nilai jual yang dikembalikan)
$returnRevenue = (int) SaleReturn::query()
    ->whereBetween('date', [$startDate, $endDate])
    ->sum('total_amount'); // sesuaikan field total bila berbeda

// Total HPP dari barang yang diretur
$returnCogs = (int) SaleReturnDetail::query()
    ->whereHas('saleReturn', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
    ->sum('hpp'); // sesuaikan field hpp bila berbeda

// Sesuaikan revenue & cogs
$revenue = max(0, $revenue - $returnRevenue);
$cogs    = max(0, $cogs    - $returnCogs);

        // 4) Gross Profit
        $grossProfit = $revenue - $cogs;

        // 5) Operating Expenses
        
$operatingExpenses = (int) Expense::between($startDate, $endDate)
    ->sum('amount');

        // 6) Net Profit (before tax)
        
$netProfit   = $grossProfit - $operatingExpenses;

        return view('reports::profit-loss.index', [
            'startDate'          => $startDate,
            'endDate'            => $endDate,
            'revenue'            => $revenue,
            'cogs'               => $cogs,
            'grossProfit'        => $grossProfit,
            'operatingExpenses'  => $operatingExpenses,
            'netProfit'          => $netProfit,
            'generated'          => true,
        ]);
    }

    public function ringkasCashier(Request $request)
{
    // 1) Ambil filter dengan default aman
    $from     = $request->input('from', now()->startOfMonth()->toDateString());
    $to       = $request->input('to',   now()->toDateString());
    $userId   = $request->input('user_id');
    $onlyPaid = $request->boolean('only_paid', true);

    // 2) Data kasir untuk dropdown
    $cashiers = User::query()->select('id','name')->orderBy('name')->get();

    // 3) Agregasi per kasir
    $rows = Sale::query()
        ->when($onlyPaid, fn($q) => $q->where('status', 'Completed')) // UBAH jika field status berbeda
        ->between($from, $to)
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->selectRaw('user_id, COUNT(*) as trx_count,
                     COALESCE(SUM(total_amount),0)  as omset,
                     COALESCE(SUM(total_hpp),0)     as total_hpp,
                     COALESCE(SUM(total_profit),0)  as total_profit')
        ->groupBy('user_id')
        ->with('user:id,name')
        ->orderBy('user_id')
        ->get();

    // 4) Kirim ke view
    return view('reports::ringkas.cashier', [
        'from'     => $from,
        'to'       => $to,
        'userId'   => $userId,
        'onlyPaid' => $onlyPaid,
        'cashiers' => $cashiers,
        'rows'     => $rows,
    ]);
}

    /* =========================
     *  STUB untuk folder lain
     *  (biar route tidak 404)
     * ========================= */
    public function salesIndex(Request $request)              { return $this->emptyIndex('reports::sales.index'); }
    public function generateSalesReport(Request $request)     { return $this->emptyResult('reports::sales.index'); }

    public function salesReturnIndex(Request $request)        { return $this->emptyIndex('reports::sales-return.index'); }
    public function generateSalesReturnReport(Request $request){ return $this->emptyResult('reports::sales-return.index'); }

    public function paymentsIndex(Request $request)           { return $this->emptyIndex('reports::payments.index'); }
    public function generatePaymentsReport(Request $request)  { return $this->emptyResult('reports::payments.index'); }

    public function purchasesIndex(Request $request)          { return $this->emptyIndex('reports::purchases.index'); }
    public function generatePurchasesReport(Request $request) { return $this->emptyResult('reports::purchases.index'); }

    public function purchasesReturnIndex(Request $request)    { return $this->emptyIndex('reports::purchases-return.index'); }
    public function generatePurchasesReturnReport(Request $request) { return $this->emptyResult('reports::purchases-return.index'); }

    private function emptyIndex(string $view)
    {
        return view(view()->exists($view) ? $view : 'reports::ringkas.daily', ['generated' => false]);
    }
    private function emptyResult(string $view)
    {
        return view(view()->exists($view) ? $view : 'reports::ringkas.daily', ['generated' => true]);
    }
}
