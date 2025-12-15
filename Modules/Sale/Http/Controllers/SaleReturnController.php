<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Sale\DataTables\SaleReturnDataTable;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleReturn;
use Modules\Sale\Entities\SaleReturnDetail;
use Modules\Sale\Http\Requests\StoreSaleReturnRequest;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;

class SaleReturnController extends Controller
{
    /**
     * Display a listing of sale returns
     */
    public function index(SaleReturnDataTable $dataTable)
    {
        abort_if(Gate::denies('access_sale_returns'), 403);

        // Stats for header cards
        $stats = [
            'pending' => SaleReturn::pending()->count(),
            'approved' => SaleReturn::approved()->count(),
            'completed' => SaleReturn::completed()->count(),
            'total_refund_this_month' => SaleReturn::completed()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('refund_amount'),
        ];

        return $dataTable->render('sale::returns.index', compact('stats'));
    }

    /**
     * Show form for creating a new sale return
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('create_sale_returns'), 403);

        $sale = null;
        if ($request->has('sale_id')) {
            $sale = Sale::with(['saleDetails', 'customer'])->find($request->sale_id);
        }

        // Get recent sales for selection
        $recentSales = Sale::with('customer')
            ->completed()
            ->orderByDesc('date')
            ->limit(50)
            ->get();

        return view('sale::returns.create', compact('sale', 'recentSales'));
    }

    /**
     * Store a newly created sale return
     */
    public function store(StoreSaleReturnRequest $request)
    {
        abort_if(Gate::denies('create_sale_returns'), 403);

        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($request->sale_id);

            // Create sale return header
            $saleReturn = SaleReturn::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'date' => $request->date ?? now(),
                'status' => SaleReturn::STATUS_PENDING,
                'refund_method' => $request->refund_method ?? 'Cash',
                'reason' => $request->reason,
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);

            // Add return details
            $totalAmount = 0;
            foreach ($request->items as $item) {
                if (empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                $detail = SaleReturnDetail::create([
                    'sale_return_id' => $saleReturn->id,
                    'sale_detail_id' => $item['sale_detail_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'product_code' => $item['product_code'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'sub_total' => $item['quantity'] * $item['unit_price'],
                    'source_type' => $item['source_type'] ?? 'new',
                    'reason' => $item['reason'] ?? null,
                    'condition' => $item['condition'] ?? 'good',
                    'restock' => isset($item['restock']) ? (bool) $item['restock'] : true,
                    'productable_type' => $item['productable_type'] ?? null,
                    'productable_id' => $item['productable_id'] ?? null,
                ]);

                $totalAmount += $detail->sub_total;
            }

            // Update totals
            $saleReturn->update([
                'total_amount' => $totalAmount,
                'refund_amount' => $request->refund_amount ?? $totalAmount,
            ]);

            DB::commit();

            toast('Retur penjualan berhasil dibuat!', 'success');
            return redirect()->route('sale-returns.show', $saleReturn->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Return Store Error', ['error' => $e->getMessage()]);
            toast('Gagal membuat retur: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * Display the specified sale return
     */
    public function show(SaleReturn $saleReturn)
    {
        abort_if(Gate::denies('access_sale_returns'), 403);

        $saleReturn->load([
            'sale.saleDetails',
            'sale.customer',
            'details',
            'creator',
            'approver',
            'customer',
        ]);

        return view('sale::returns.show', compact('saleReturn'));
    }

    /**
     * Show form for editing the specified sale return
     */
    public function edit(SaleReturn $saleReturn)
    {
        abort_if(Gate::denies('edit_sale_returns'), 403);

        if ($saleReturn->status !== SaleReturn::STATUS_PENDING) {
            toast('Retur yang sudah diproses tidak dapat diedit.', 'warning');
            return redirect()->route('sale-returns.show', $saleReturn->id);
        }

        $saleReturn->load(['sale.saleDetails', 'details', 'customer']);

        return view('sale::returns.edit', compact('saleReturn'));
    }

    /**
     * Update the specified sale return
     */
    public function update(StoreSaleReturnRequest $request, SaleReturn $saleReturn)
    {
        abort_if(Gate::denies('edit_sale_returns'), 403);

        if ($saleReturn->status !== SaleReturn::STATUS_PENDING) {
            toast('Retur yang sudah diproses tidak dapat diedit.', 'warning');
            return redirect()->route('sale-returns.show', $saleReturn->id);
        }

        try {
            DB::beginTransaction();

            // Update header
            $saleReturn->update([
                'date' => $request->date ?? $saleReturn->date,
                'refund_method' => $request->refund_method ?? $saleReturn->refund_method,
                'reason' => $request->reason,
                'note' => $request->note,
            ]);

            // Delete existing details and recreate
            $saleReturn->details()->delete();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                if (empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                $detail = SaleReturnDetail::create([
                    'sale_return_id' => $saleReturn->id,
                    'sale_detail_id' => $item['sale_detail_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'product_code' => $item['product_code'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'sub_total' => $item['quantity'] * $item['unit_price'],
                    'source_type' => $item['source_type'] ?? 'new',
                    'reason' => $item['reason'] ?? null,
                    'condition' => $item['condition'] ?? 'good',
                    'restock' => isset($item['restock']) ? (bool) $item['restock'] : true,
                    'productable_type' => $item['productable_type'] ?? null,
                    'productable_id' => $item['productable_id'] ?? null,
                ]);

                $totalAmount += $detail->sub_total;
            }

            // Update totals
            $saleReturn->update([
                'total_amount' => $totalAmount,
                'refund_amount' => $request->refund_amount ?? $totalAmount,
            ]);

            DB::commit();

            toast('Retur penjualan berhasil diperbarui!', 'success');
            return redirect()->route('sale-returns.show', $saleReturn->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Return Update Error', ['error' => $e->getMessage()]);
            toast('Gagal memperbarui retur: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified sale return (soft delete)
     */
    public function destroy(SaleReturn $saleReturn)
    {
        abort_if(Gate::denies('delete_sale_returns'), 403);

        if ($saleReturn->status !== SaleReturn::STATUS_PENDING) {
            toast('Retur yang sudah diproses tidak dapat dihapus.', 'warning');
            return back();
        }

        $saleReturn->delete();

        toast('Retur penjualan berhasil dihapus!', 'success');
        return redirect()->route('sale-returns.index');
    }

    /**
     * Approve a pending sale return
     */
    public function approve(Request $request, SaleReturn $saleReturn)
    {
        abort_if(Gate::denies('approve_sale_returns'), 403);

        if (!$saleReturn->canBeApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Retur ini tidak dapat disetujui.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update status
            $saleReturn->update([
                'status' => SaleReturn::STATUS_APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Process stock restoration for items marked for restock
            foreach ($saleReturn->details as $detail) {
                if (!$detail->restock) {
                    continue;
                }

                if ($detail->source_type === 'new' && $detail->productable_id) {
                    // Restore stock for new products
                    $product = Product::find($detail->productable_id);
                    if ($product) {
                        $product->increment('product_quantity', $detail->quantity);
                        Log::info('Stock restored', [
                            'product_id' => $product->id,
                            'quantity' => $detail->quantity,
                            'sale_return_id' => $saleReturn->id,
                        ]);
                    }
                } elseif ($detail->source_type === 'second' && $detail->productable_id) {
                    // For second-hand products, we may need different logic
                    // depending on how second-hand stock is managed
                    $productSecond = ProductSecond::find($detail->productable_id);
                    if ($productSecond) {
                        // Mark as available again if it was sold
                        $productSecond->update(['status' => 'available']);
                        Log::info('Second product status restored', [
                            'product_second_id' => $productSecond->id,
                            'sale_return_id' => $saleReturn->id,
                        ]);
                    }
                }
            }

            // Mark as completed after stock processing
            $saleReturn->update(['status' => SaleReturn::STATUS_COMPLETED]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retur berhasil disetujui dan stok telah dipulihkan.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale Return Approve Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui retur: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a pending sale return
     */
    public function reject(Request $request, SaleReturn $saleReturn)
    {
        abort_if(Gate::denies('approve_sale_returns'), 403);

        if (!$saleReturn->canBeRejected()) {
            return response()->json([
                'success' => false,
                'message' => 'Retur ini tidak dapat ditolak.',
            ], 422);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $saleReturn->update([
            'status' => SaleReturn::STATUS_REJECTED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'note' => $saleReturn->note . "\n\nAlasan penolakan: " . ($request->rejection_reason ?? '-'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retur berhasil ditolak.',
        ]);
    }

    /**
     * Get items from a sale for return form (AJAX)
     */
    public function getSaleItems(Sale $sale)
    {
        $sale->load(['saleDetails', 'customer']);

        $items = $sale->saleDetails->map(function ($detail) {
            return [
                'id' => $detail->id,
                'product_name' => $detail->product_name,
                'product_code' => $detail->product_code,
                'quantity_sold' => $detail->quantity,
                'unit_price' => $detail->price,
                'sub_total' => $detail->sub_total,
                'source_type' => $detail->source_type,
                'productable_type' => $detail->productable_type,
                'productable_id' => $detail->productable_id,
            ];
        });

        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'reference' => $sale->reference,
                'date' => $sale->date->format('d M Y'),
                'customer_name' => $sale->customer_display_name,
                'total_amount' => $sale->total_amount,
            ],
            'items' => $items,
        ]);
    }
}
