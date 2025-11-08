<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sale\Entities\ManualInputLog;
use App\Models\User;
use DataTables;
use Carbon\Carbon;

class VarianceMonitoringController extends Controller
{
    /**
     * Dashboard - ringkasan deviasi
     */
    public function index()
    {
        // Top 10 deviasi terbesar
        $top_deviations = ManualInputLog::where('variance_level', '!=', 'minor')->with('cashier')->orderByDesc('variance_percent')->limit(10)->get();

        // Summary stats
        $summary = [
            'critical_count' => ManualInputLog::where('variance_level', 'critical')->where('approval_status', 'pending')->count(),
            'warning_count' => ManualInputLog::where('variance_level', 'warning')->where('approval_status', 'pending')->count(),
            'total_variance_value' => ManualInputLog::where('approval_status', 'pending')->sum('variance_amount'),
        ];

        // ✅ GET USERS UNTUK FILTER (All active users yang bisa jadi kasir)
        $cashiers = User::where('is_active', true)->orderBy('name')->get();

        return view('sale::variance-monitoring.index', compact('top_deviations', 'summary', 'cashiers'));
    }

    /**
     * API DataTable untuk detail deviasi
     */
    public function dataTable(Request $request)
    {
        $query = ManualInputLog::with(['cashier', 'sale'])->orderByDesc('created_at');

        // Filter kasir
        if ($request->has('cashier_id') && $request->cashier_id) {
            $query->where('cashier_id', $request->cashier_id);
        }

        // Filter tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter level deviasi
        if ($request->has('level') && $request->level) {
            $query->where('variance_level', $request->level);
        }

        // Filter status
        if ($request->has('approval_status') && $request->approval_status) {
            $query->where('approval_status', $request->approval_status);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('kasir_name', fn($d) => $d->cashier->name ?? '-')
            ->addColumn('service_name', fn($d) => $d->item_name)
            ->addColumn('sale_date', fn($d) => $d->sale?->date?->format('d M Y') ?? '-')
            ->addColumn('master_price_fmt', fn($d) => format_currency($d->master_price))
            ->addColumn('input_price_fmt', fn($d) => format_currency($d->input_price))
            ->addColumn('variance_display', fn($d) => $this->formatVariance($d))
            ->addColumn('reason_display', fn($d) => $d->reason_provided ? "<small class='text-muted'>{$d->reason_provided}</small>" : '<span class="text-danger">-</span>')
            ->addColumn('level_badge', fn($d) => $this->getLevelBadge($d->variance_level))
            ->addColumn('approval_status_badge', fn($d) => $this->getApprovalBadge($d->approval_status))
            ->addColumn(
                'action',
                fn($d) => '<a href="' .
                    route('sale.variance-monitoring.show', $d->id) .
                    '" class="btn btn-sm btn-info" title="Detail">
                    <i class="bi bi-eye"></i>
                </a>',
            )
            ->rawColumns(['variance_display', 'reason_display', 'level_badge', 'approval_status_badge', 'action'])
            ->make(true);
    }

    /**
     * Format display variance (untuk HTML)
     */
    private function formatVariance($data)
    {
        $sign = $data->variance_amount >= 0 ? '+' : '';
        $color = $data->variance_percent > 0 ? 'danger' : 'success';

        return "<span class='text-$color fw-bold'>" . "$sign" . format_currency($data->variance_amount) . " ({$data->variance_percent}%)" . '</span>';
    }

    /**
     * Get badge HTML untuk level deviasi
     */
    private function getLevelBadge($level)
    {
        return match ($level) {
            'critical' => '<span class="badge bg-danger">🚨 CRITICAL</span>',
            'warning' => '<span class="badge bg-warning text-dark">⚠️ WARNING</span>',
            default => '<span class="badge bg-info">ℹ️ Minor</span>',
        };
    }

    /**
     * Get badge HTML untuk approval status
     */
    private function getApprovalBadge($status)
    {
        return match ($status) {
            'approved' => '<span class="badge bg-success">✅ Approved</span>',
            'rejected' => '<span class="badge bg-danger">❌ Rejected</span>',
            default => '<span class="badge bg-warning text-dark">⏳ Pending</span>',
        };
    }

    /**
     * Export data deviasi ke Excel
     */
    public function export(Request $request)
    {
        $logs = ManualInputLog::with('cashier', 'sale')->orderByDesc('created_at')->get();

        // Return JSON untuk data preparation (bisa diubah jadi Excel export nanti)
        return response()->json($logs);
    }

    /**
     * Detail view satu deviasi
     */
    public function show($id)
    {
        $varianceLog = ManualInputLog::findOrFail($id);
        $varianceLog->load(['cashier', 'sale', 'saleDetail', 'approver']);

        return view('sale::variance-monitoring.detail', compact('varianceLog'));
    }

    /**
     * Approve deviasi
     */
    public function approve($id, Request $request)
    {
        $varianceLog = ManualInputLog::findOrFail($id);
        $varianceLog->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Deviasi harga berhasil disetujui.');
    }

    /**
     * Reject deviasi
     */
    public function reject($id, Request $request)
    {
        $varianceLog = ManualInputLog::findOrFail($id);
        $varianceLog->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('warning', 'Deviasi harga ditolak.');
    }
}
