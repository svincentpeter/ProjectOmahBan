<?php
// 📁 app/Http/Controllers/Owner/ManualInputController.php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ManualInputDetail;
use Modules\Sale\Entities\ManualInputLog;
use Modules\Sale\Entities\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManualInputController extends Controller
{
    /**
     * List transaksi dengan manual input
     */
    public function index(Request $request)
    {
        abort_unless(
            auth()
                ->user()
                ->hasRole(['Owner', 'Supervisor']),
            403,
        );

        $query = Sale::where('has_manual_input', 1)
            ->with(['user', 'saleDetails'])
            ->orderBy('created_at', 'desc');

        // 🔍 FILTERS
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('cashier_id')) {
            $query->where('user_id', $request->cashier_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'notified') {
                $query->where('is_manual_input_notified', 1);
            } elseif ($request->status === 'pending') {
                $query->where('is_manual_input_notified', 0);
            }
        }

        $sales = $query->paginate(20);

        // 📊 Stats untuk filter summary
        $stats = [
            'total' => Sale::where('has_manual_input', 1)->count(),
            'today' => Sale::whereDate('created_at', today())->where('has_manual_input', 1)->count(),
            'this_month' => Sale::whereMonth('created_at', now()->month)->where('has_manual_input', 1)->count(),
        ];

        return view('owner.manual-inputs.index', compact('sales', 'stats'));
    }

    /**
     * Detail transaksi manual input
     */
    public function show(Sale $sale)
    {
        abort_unless(
            auth()
                ->user()
                ->hasRole(['Owner', 'Supervisor']),
            403,
        );

        abort_unless($sale->has_manual_input, 404, 'Transaksi ini tidak memiliki input manual');

        // Load relationships
        $sale->load(['saleDetails', 'user', 'payments']);

        // Get manual input details untuk transaksi ini
        $manualDetails = ManualInputDetail::where('sale_id', $sale->id)->with('cashier')->get();

        // Get manual input logs (untuk approval tracking)
        $manualLogs = ManualInputLog::where('sale_id', $sale->id)
            ->with(['cashier', 'approver'])
            ->get();

        // Get notifikasi terkait
        $notifications = \App\Models\OwnerNotification::where('sale_id', $sale->id)->orderBy('created_at', 'desc')->get();

        return view('owner.manual-inputs.detail', compact('sale', 'manualDetails', 'manualLogs', 'notifications'));
    }

    /**
     * Summary & chart manual input
     */
    public function summary(Request $request)
    {
        abort_unless(
            auth()
                ->user()
                ->hasRole(['Owner', 'Supervisor']),
            403,
        );

        $period = $request->get('period', 'month'); // month, week, day

        // 📊 Chart data berdasarkan periode
        $chartData = $this->getChartDataByPeriod($period);

        // 🔝 Top items
        $topItems = $this->getTopItemsByPeriod($period);

        // 📋 Summary stats
        $stats = [
            'total_transactions' => Sale::where('has_manual_input', 1)->count(),
            'total_items' => ManualInputDetail::count(),
            'total_value' => ManualInputDetail::sum(DB::raw('quantity * price')),
            'unique_items' => ManualInputDetail::distinct('item_name')->count('item_name'),
        ];

        return view('owner.manual-inputs.summary', compact('chartData', 'topItems', 'stats', 'period'));
    }

    /**
     * Helper: Chart data by period
     */
    private function getChartDataByPeriod($period)
    {
        $dateFormat = match ($period) {
            'day' => '%Y-%m-%d %H:00:00',
            'week' => '%Y-%m-%d',
            'month' => '%Y-%m-%d',
            default => '%Y-%m-%d',
        };

        $dateFrom = match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfMonth(),
        };

        return DB::table('manual_input_details as mid')
            ->join('users as u', 'mid.cashier_id', '=', 'u.id')
            ->select(DB::raw("DATE_FORMAT(mid.created_at, '$dateFormat') as period"), 'u.name as cashier', DB::raw('COUNT(*) as total'))
            ->where('mid.created_at', '>=', $dateFrom)
            ->groupBy('period', 'u.name')
            ->orderBy('period')
            ->get();
    }

    /**
     * Helper: Top items by period
     */
    private function getTopItemsByPeriod($period)
    {
        $dateFrom = match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfMonth(),
        };

        return ManualInputDetail::select('item_name', 'item_type', DB::raw('COUNT(*) as frequency'), DB::raw('SUM(quantity) as total_qty'), DB::raw('AVG(price) as avg_price'))->where('created_at', '>=', $dateFrom)->groupBy('item_name', 'item_type')->orderByDesc('frequency')->limit(10)->get();
    }
}
