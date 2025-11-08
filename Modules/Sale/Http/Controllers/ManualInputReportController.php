<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sale\Entities\ManualInputLog;
use Modules\Sale\Entities\Sale;
use App\Models\User;
use DataTables;
use Carbon\Carbon;

class ManualInputReportController extends Controller
{
    /**
     * Dashboard Report
     */
    public function index()
    {
        // Statistics
        $stats = [
            'total_today' => ManualInputLog::whereDate('created_at', today())->count(),
            'total_this_month' => ManualInputLog::whereMonth('created_at', now()->month)->count(),
            'critical_pending' => ManualInputLog::where('variance_level', 'critical')
                ->where('approval_status', 'pending')
                ->count(),
            'total_value_today' => ManualInputLog::whereDate('created_at', today())
                ->sum('variance_amount'),
        ];
        
        // Top 5 Kasir dengan input manual terbanyak bulan ini
        $topCashiers = ManualInputLog::selectRaw('cashier_id, COUNT(*) as total')
            ->with('cashier')
            ->whereMonth('created_at', now()->month)
            ->groupBy('cashier_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        
        // Top 5 Alasan input manual
        $topReasons = ManualInputLog::selectRaw('mandatory_reason, COUNT(*) as total')
            ->whereNotNull('mandatory_reason')
            ->whereMonth('created_at', now()->month)
            ->groupBy('mandatory_reason')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        
        return view('sale::manual-input-report.index', compact('stats', 'topCashiers', 'topReasons'));
    }
    
    /**
     * DataTable API
     */
    public function dataTable(Request $request)
    {
        $query = ManualInputLog::with(['cashier', 'sale'])
            ->orderByDesc('created_at');
        
        // Filters
        if ($request->has('cashier_id') && $request->cashier_id) {
            $query->where('cashier_id', $request->cashier_id);
        }
        
        if ($request->has('input_type') && $request->input_type) {
            $query->where('input_type', $request->input_type);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('sale_reference', fn($d) => $d->sale->reference ?? '#' . $d->sale_id)
            ->addColumn('cashier_name', fn($d) => $d->cashier->name ?? '-')
            ->addColumn('input_type_badge', fn($d) => $this->getInputTypeBadge($d->input_type))
            ->addColumn('variance_display', fn($d) => $this->formatVariance($d))
            ->addColumn('reason_short', fn($d) => \Str::limit($d->mandatory_reason, 50))
            ->addColumn('action', fn($d) => view('sale::manual-input-report.actions', ['log' => $d]))
            ->rawColumns(['input_type_badge', 'variance_display', 'action'])
            ->make(true);
    }
    
    private function getInputTypeBadge($type): string
    {
        return match($type) {
            'manual_item' => '<span class="badge bg-primary">📦 Manual Item</span>',
            'manual_service' => '<span class="badge bg-info">💼 Manual Service</span>',
            'price_edit' => '<span class="badge bg-warning">💰 Edit Harga</span>',
            'discount' => '<span class="badge bg-success">🏷️ Diskon</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }
    
    private function formatVariance($log): string
    {
        $sign = $log->variance_amount >= 0 ? '+' : '';
        $color = $log->variance_percent > 0 ? 'danger' : 'success';
        
        return "<span class='text-$color fw-bold'>" .
            "$sign" . format_currency($log->variance_amount) . 
            " ({$log->variance_percent}%)" .
            "</span>";
    }
    
    /**
     * Export to Excel
     */
    public function export(Request $request)
    {
        $logs = ManualInputLog::with(['cashier', 'sale'])
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByDesc('created_at')
            ->get();
        
        // Return CSV untuk simplicity (bisa pakai Laravel Excel nanti)
        $filename = 'manual-input-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'ID Transaksi', 'Kasir', 'Tipe Input', 'Item', 'Harga Master', 'Harga Input', 'Deviasi %', 'Alasan']);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i'),
                    $log->sale->reference ?? $log->sale_id,
                    $log->cashier->name ?? '-',
                    $log->input_type,
                    $log->item_name,
                    $log->master_price,
                    $log->input_price,
                    $log->variance_percent . '%',
                    $log->mandatory_reason,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
