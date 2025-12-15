<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sale\Entities\Sale;

class SaleApiController extends Controller
{
    /**
     * Get all sales with filters
     */
    public function index(Request $request)
    {
        $query = Sale::query()
            ->whereNull('deleted_at')
            ->select([
                'id',
                'reference',
                'date',
                'customer_id',
                'customer_name',
                'total_amount',
                'paid_amount',
                'due_amount',
                'status',
                'payment_status',
                'payment_method',
                'created_at'
            ]);

        // Date filter
        if ($request->has('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->where('date', '<=', $request->to);
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Customer filter
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Search by reference
        if ($request->has('search')) {
            $query->where('reference', 'like', "%{$request->search}%");
        }

        // Order
        $query->orderBy('date', 'desc')->orderBy('id', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 15);
        $sales = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sales->items(),
            'meta' => [
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'per_page' => $sales->perPage(),
                'total' => $sales->total(),
            ]
        ]);
    }

    /**
     * Get sale details
     */
    public function show($id)
    {
        $sale = Sale::with(['saleDetails', 'customer', 'user:id,name'])
            ->find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Sale not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sale
        ]);
    }

    /**
     * Get sales summary for date range
     */
    public function summary(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $query = Sale::query()
            ->whereNull('deleted_at')
            ->whereBetween('date', [$from, $to]);

        $summary = [
            'period' => [
                'from' => $from,
                'to' => $to
            ],
            'total_transactions' => $query->count(),
            'total_amount' => (int) $query->sum('total_amount'),
            'total_paid' => (int) $query->sum('paid_amount'),
            'total_due' => (int) $query->sum('due_amount'),
            'by_payment_status' => [
                'paid' => $query->clone()->where('payment_status', 'Paid')->count(),
                'partial' => $query->clone()->where('payment_status', 'Partial')->count(),
                'unpaid' => $query->clone()->where('payment_status', 'Unpaid')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get daily sales for chart data
     */
    public function dailySales(Request $request)
    {
        $days = $request->get('days', 7);
        
        $sales = Sale::query()
            ->whereNull('deleted_at')
            ->where('date', '>=', now()->subDays($days)->toDateString())
            ->selectRaw('DATE(date) as sale_date')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total_amount) as total')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sales
        ]);
    }
}
