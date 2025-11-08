<?php
// 📁 app/Http/Controllers/Owner/DashboardController.php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\OwnerNotification;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\ManualInputLog;
use App\Models\ManualInputDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard utama owner/supervisor
     */
    public function index()
    {
        // ✅ Gate check
        abort_unless(
            auth()->user()->hasRole(['Owner', 'Supervisor']), 
            403, 
            'Akses ditolak: Hanya Owner/Supervisor'
        );
        
        $userId = auth()->id();
        
        // 📊 STATISTICS CARDS
        $stats = [
            // Notifikasi
            'unread_notifications' => OwnerNotification::where('user_id', $userId)
                ->unread()
                ->count(),
            
            'unreviewed_count' => OwnerNotification::where('user_id', $userId)
                ->unreviewed()
                ->count(),
            
            'critical_count' => OwnerNotification::where('user_id', $userId)
                ->unread()
                ->where('severity', 'critical')
                ->count(),
            
            // Transaksi manual input
            'manual_input_today' => Sale::whereDate('created_at', today())
                ->where('has_manual_input', 1)
                ->count(),
            
            'manual_input_this_week' => Sale::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->where('has_manual_input', 1)
                ->count(),
            
            'manual_input_this_month' => Sale::whereMonth('created_at', now()->month)
                ->where('has_manual_input', 1)
                ->count(),
            
            // Approval pending
            'pending_approvals' => ManualInputLog::where('approval_status', 'pending')
                ->count(),
            
            'critical_pending' => ManualInputLog::where('approval_status', 'pending')
                ->where('variance_level', 'critical')
                ->count(),
        ];
        
        // 📈 CHART DATA: Manual Input per Kasir (7 hari terakhir)
        $chartData = $this->getManualInputChartData();
        
        // 🔝 TOP 10 ITEMS MANUAL (yang sering diinput)
        $topManualItems = $this->getTopManualItems();
        
        // 📋 RECENT NOTIFICATIONS (5 terbaru)
        $recentNotifications = OwnerNotification::where('user_id', $userId)
            ->with('sale')
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // 🚨 URGENT APPROVALS (yang critical & pending)
        $urgentApprovals = ManualInputLog::where('variance_level', 'critical')
            ->where('approval_status', 'pending')
            ->with(['sale', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('owner.dashboard', compact(
            'stats',
            'chartData',
            'topManualItems',
            'recentNotifications',
            'urgentApprovals'
        ));
    }
    
    /**
     * 📈 Helper: Chart data manual input per kasir (7 hari terakhir)
     */
    private function getManualInputChartData()
    {
        $data = DB::table('manual_input_details as mid')
            ->join('users as u', 'mid.cashier_id', '=', 'u.id')
            ->select(
                'u.name as cashier_name',
                DB::raw('DATE(mid.created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('mid.created_at', '>=', now()->subDays(7))
            ->groupBy('mid.cashier_id', 'u.name', DB::raw('DATE(mid.created_at)'))
            ->orderBy('date', 'asc')
            ->get();
        
        // Transform untuk Chart.js format
        $labels = [];
        $datasets = [];
        
        // Group by cashier
        $grouped = $data->groupBy('cashier_name');
        
        foreach ($grouped as $cashierName => $records) {
            $dataPoints = [];
            
            foreach ($records as $record) {
                if (!in_array($record->date, $labels)) {
                    $labels[] = $record->date;
                }
                $dataPoints[$record->date] = $record->total;
            }
            
            $datasets[] = [
                'label' => $cashierName,
                'data' => array_values($dataPoints),
            ];
        }
        
        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }
    
    /**
     * 🔝 Helper: Top 10 item manual yang sering diinput
     */
    private function getTopManualItems()
    {
        return DB::table('manual_input_details')
            ->select(
                'item_name',
                'item_type',
                DB::raw('COUNT(*) as frequency'),
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('AVG(price) as avg_price')
            )
            ->whereMonth('created_at', now()->month)
            ->groupBy('item_name', 'item_type')
            ->orderByDesc('frequency')
            ->limit(10)
            ->get();
    }
}
