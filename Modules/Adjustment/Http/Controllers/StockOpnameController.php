<?php

namespace Modules\Adjustment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\{Auth, DB, Gate, Log};
use Modules\Adjustment\Entities\{StockOpname, StockOpnameItem, StockOpnameLog};
use Modules\Product\Entities\{Product, Category};
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    /**
     * INDEX - List semua stok opname
     */
    public function index()
    {
        abort_if(Gate::denies('access_stock_opname'), 403);

        $stats = [
            'total' => StockOpname::count(),
            'draft' => StockOpname::draft()->count(),
            'in_progress' => StockOpname::inProgress()->count(),
            'completed' => StockOpname::completed()->count(),
        ];

        return view('adjustment::stock-opname.index', compact('stats'));
    }

    /**
     * DATATABLE AJAX
     */
    public function datatable(Request $request)
    {
        abort_if(Gate::denies('access_stock_opname'), 403);

        $query = StockOpname::with(['pic', 'supervisor'])->latest('created_at');

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter tanggal
        if ($from = $request->input('date_from')) {
            $query->whereDate('opname_date', '>=', $from);
        }
        if ($to = $request->input('date_to')) {
            $query->whereDate('opname_date', '<=', $to);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('pic_name', function ($so) {
                return $so->pic ? $so->pic->name : '-';
            })
            ->addColumn('completion', function ($so) {
                return $so->completion_percentage; // Dari accessor di model
            })
            ->addColumn('status_badge', function ($so) {
                return $so->status_badge; // Dari accessor di model (raw HTML)
            })
            ->addColumn('actions', function ($so) {
                // Construct Custom Actions
                $customActions = '';

                // 1. Lanjutkan Hitung (Draft/In Progress)
                if (auth()->user()->can('edit_stock_opname') && in_array($so->status, ['draft', 'in_progress'])) {
                    $customActions .= '
                        <li>
                            <a href="'.route('stock-opnames.counting', $so->id).'" 
                               class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                                <i class="bi bi-calculator text-amber-600 dark:text-amber-400"></i>
                                <span>Lanjutkan Hitung</span>
                            </a>
                        </li>';
                }

                // 2. Selesaikan Opname (In Progress & 100%)
                if (auth()->user()->can('edit_stock_opname') && $so->status === 'in_progress' && $so->completion_percentage >= 100) {
                    // Note: We need a form for this. standard actions partial doesn't support random forms easily inside the dropdown 
                    // unless we use a link that triggers a form submission via JS. 
                    // Let's use a class trigger 'btn-complete-opname' similar to delete.
                    // We'll put the form in the 'customActions' hidden or just use JS to create form dynamically.
                    // Better: Use a simple link with class and data attribute.
                    $customActions .= '
                        <li>
                            <a href="javascript:void(0)" 
                               class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors btn-complete-opname"
                               data-id="'.$so->id.'"
                               data-url="'.route('stock-opnames.complete', $so->id).'">
                                <i class="bi bi-check-circle text-emerald-600 dark:text-emerald-400"></i>
                                <span>Selesaikan</span>
                            </a>
                        </li>';
                }
                
                // 3. Delete Action (Draft Only)
                // We can use the standard 'deleteRoute' param of the partial, but we need to check condition ($so->status === 'draft')
                $deleteRoute = ($so->status === 'draft') ? route('stock-opnames.destroy', $so->id) : null;

                return view('partials.datatable-actions', [
                    'id' => $so->id,
                    'itemName' => $so->reference,
                    'showRoute' => route('stock-opnames.show', $so->id),
                    'showPermission' => 'show_stock_opname',
                    'deleteRoute' => $deleteRoute,
                    'deletePermission' => 'delete_stock_opname',
                    'customActions' => $customActions
                ])->render();
            })
            ->rawColumns(['status_badge', 'actions']) // Allow HTML
            ->make(true);
    }

    /**
     * CREATE - Form buat opname baru
     */
    public function create()
    {
        abort_if(Gate::denies('create_stock_opname'), 403);

        $categories = Category::orderBy('category_name')->get();

        return view('adjustment::stock-opname.create', compact('categories'));
    }

    /**
 * EDIT - Form edit opname (hanya draft)
 */
public function edit(StockOpname $stockOpname)
{
    abort_if(Gate::denies('edit_stock_opname'), 403);

    if ($stockOpname->status !== 'draft') {
        toast('❌ Hanya opname dengan status DRAFT yang bisa diedit!', 'error');
        return redirect()->route('stock-opnames.show', $stockOpname);
    }

    $stockOpname->load(['items.product']);
    $categories = Category::orderBy('category_name')->get();

    return view('adjustment::stock-opname.edit', compact('stockOpname', 'categories'));
}

/**
 * UPDATE - Update opname (hanya draft)
 */
public function update(Request $request, StockOpname $stockOpname)
{
    abort_if(Gate::denies('edit_stock_opname'), 403);

    if ($stockOpname->status !== 'draft') {
        toast('❌ Hanya opname dengan status DRAFT yang bisa diupdate!', 'error');
        return back();
    }

    $validated = $request->validate([
        'opname_date' => 'required|date',
        'scope_type' => 'required|in:all,category,custom',
        'category_ids' => 'required_if:scope_type,category|array',
        'category_ids.*' => 'exists:categories,id',
        'product_ids' => 'required_if:scope_type,custom|array',
        'product_ids.*' => 'exists:products,id',
        'notes' => 'nullable|string|max:500',
    ]);

    try {
        $stockOpname = DB::transaction(function() use ($validated, $stockOpname) {
            // Check jika scope berubah
            $scopeChanged = $stockOpname->scope_type !== $validated['scope_type'];

            // Update header
            $stockOpname->update([
                'opname_date' => $validated['opname_date'],
                'scope_type' => $validated['scope_type'],
                'scope_ids' => $validated['category_ids'] ?? null,
                'notes' => $validated['notes'],
            ]);

            // Jika scope berubah, reset items
            if ($scopeChanged) {
                // Hapus items lama
                $stockOpname->items()->delete();

                // Ambil produk baru
                $products = $this->getProductsByScope(
                    $validated['scope_type'],
                    $validated['category_ids'] ?? [],
                    $validated['product_ids'] ?? []
                );

                if ($products->isEmpty()) {
                    throw new \Exception('Tidak ada produk yang ditemukan untuk scope ini!');
                }

                // Buat items baru
                foreach ($products as $product) {
                    StockOpnameItem::create([
                        'stock_opname_id' => $stockOpname->id,
                        'product_id' => $product->id,
                        'system_qty' => $product->product_quantity ?? 0,
                        'actual_qty' => null,
                    ]);
                }

                // Log perubahan scope
                StockOpnameLog::logActivity(
                    $stockOpname->id,
                    StockOpnameLog::ACTION_ITEM_UPDATED,
                    [
                        'description' => "Scope diubah, {$products->count()} item baru dibuat"
                    ]
                );
            } else {
                // Hanya log update biasa
                StockOpnameLog::logActivity(
                    $stockOpname->id,
                    'updated',
                    [
                        'description' => 'Data opname diupdate (tanggal/catatan)'
                    ]
                );
            }

            return $stockOpname;
        });

        toast('✅ Stock Opname berhasil diupdate!', 'success');
        return redirect()->route('stock-opnames.show', $stockOpname->id);

    } catch (\Throwable $e) {
        Log::error('StockOpname Update Error: ' . $e->getMessage());
        toast('❌ Error: ' . $e->getMessage(), 'error');
        return back()->withInput();
    }
}


    /**
     * STORE - Simpan opname baru
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('create_stock_opname'), 403);

        $validated = $request->validate([
            'opname_date' => 'required|date',
            'scope_type' => 'required|in:all,category,custom',
            'category_ids' => 'required_if:scope_type,category|array',
            'category_ids.*' => 'exists:categories,id',
            'product_ids' => 'required_if:scope_type,custom|array',
            'product_ids.*' => 'exists:products,id',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $opname = DB::transaction(function () use ($validated) {
                // Buat header
                $opname = StockOpname::create([
                    'reference' => StockOpname::generateReference(),
                    'opname_date' => $validated['opname_date'],
                    'status' => 'draft',
                    'scope_type' => $validated['scope_type'],
                    'scope_ids' => $validated['category_ids'] ?? null,
                    'pic_id' => Auth::id(),
                    'notes' => $validated['notes'],
                ]);

                // Ambil produk sesuai scope
                $products = $this->getProductsByScope($validated['scope_type'], $validated['category_ids'] ?? [], $validated['product_ids'] ?? []);

                // Validasi: pastikan ada produk
                if ($products->isEmpty()) {
                    throw new \Exception('Tidak ada produk yang ditemukan untuk scope ini!');
                }

                // Buat item untuk setiap produk (snapshot stok saat ini)
                foreach ($products as $product) {
                    StockOpnameItem::create([
                        'stock_opname_id' => $opname->id,
                        'product_id' => $product->id,
                        'system_qty' => $product->product_quantity ?? 0,
                        'actual_qty' => null, // Belum dihitung
                    ]);
                }

                // Log menggunakan helper method
                StockOpnameLog::logActivity($opname->id, StockOpnameLog::ACTION_CREATED, [
                    'new_status' => 'draft',
                    'description' => "Stock opname dibuat dengan {$products->count()} item",
                ]);

                return $opname; // ← RETURN dari transaction
            });

            toast('✅ Stock Opname berhasil dibuat! Silakan mulai penghitungan.', 'success');
            return redirect()->route('stock-opnames.counting', $opname->id); // ← Langsung pakai $opname
        } catch (\Throwable $e) {
            Log::error('StockOpname Store Error: ' . $e->getMessage());
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * COUNTING PAGE - Halaman input hasil hitungan fisik
     */
    public function counting(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('edit_stock_opname'), 403);

        if (!in_array($stockOpname->status, ['draft', 'in_progress'])) {
            toast('❌ Stock opname ini sudah selesai!', 'error');
            return redirect()->route('stock-opnames.show', $stockOpname);
        }

        // Update status ke in_progress jika masih draft
        if ($stockOpname->status === 'draft') {
            $stockOpname->update(['status' => 'in_progress']);

            // ✅ Pakai helper method
            StockOpnameLog::logActivity($stockOpname->id, StockOpnameLog::ACTION_STARTED, [
                'old_status' => 'draft',
                'new_status' => 'in_progress',
                'description' => 'Memulai penghitungan stok fisik',
            ]);
        }

        $stockOpname->load(['items.product.category']);

        return view('adjustment::stock-opname.counting', compact('stockOpname'));
    }

    /**
     * UPDATE COUNT - Update hasil hitungan per item (AJAX)
     */
    public function updateCount(Request $request, StockOpnameItem $item)
    {
        abort_if(Gate::denies('edit_stock_opname'), 403);

        $validated = $request->validate([
            'actual_qty' => 'required|integer|min:0',
            'variance_reason' => 'nullable|string|max:500',
        ]);

        try {
            $item->update([
                'actual_qty' => $validated['actual_qty'],
                'variance_reason' => $validated['variance_reason'],
                'counted_at' => now(),
                'counted_by' => Auth::id(),
            ]);

            // ✅ Log activity (opsional: bisa jadi banyak log jika ribuan item)
            // Jika mau log per item:
            StockOpnameLog::logActivity($item->stock_opname_id, StockOpnameLog::ACTION_ITEM_COUNTED, [
                'description' => "Item '{$item->product->product_name}' dihitung: {$validated['actual_qty']} unit (Variance: {$item->variance_qty})",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Hitungan berhasil disimpan!',
                'variance' => $item->variance_qty,
                'variance_type' => $item->variance_type,
                'completion' => $item->stockOpname->completion_percentage, // ✅ Tambahkan untuk update progress bar
            ]);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * COMPLETE - Selesaikan opname & generate adjustments
     */
    public function complete(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('edit_stock_opname'), 403);

        if ($stockOpname->status !== 'in_progress') {
            toast('❌ Hanya opname yang sedang berjalan yang bisa diselesaikan!', 'error');
            return back();
        }

        // Cek apakah semua item sudah dihitung
        $uncounted = $stockOpname->items()->whereNull('actual_qty')->count();
        if ($uncounted > 0) {
            toast("❌ Masih ada {$uncounted} item yang belum dihitung!", 'error');
            return back();
        }

        try {
            DB::transaction(function () use ($stockOpname) {
                // Generate adjustments untuk variance
                $adjustments = $stockOpname->generateAdjustments();

                // Update status
                $stockOpname->update(['status' => 'completed']);

                // ✅ Log completion
                StockOpnameLog::logActivity($stockOpname->id, StockOpnameLog::ACTION_COMPLETED, [
                    'old_status' => 'in_progress',
                    'new_status' => 'completed',
                    'description' => count($adjustments) . ' adjustment dibuat untuk variance',
                ]);

                // ✅ Log jika ada adjustments
                if (count($adjustments) > 0) {
                    StockOpnameLog::logActivity($stockOpname->id, StockOpnameLog::ACTION_ADJUSTMENT_GENERATED, [
                        'description' => 'Adjustment: ' . implode(', ', array_map(fn($a) => $a->reference, $adjustments)),
                    ]);
                }
            });

            toast('✅ Stock Opname selesai! Adjustment telah dibuat untuk variance.', 'success');
            return redirect()->route('stock-opnames.show', $stockOpname);
        } catch (\Throwable $e) {
            Log::error('StockOpname Complete Error: ' . $e->getMessage());
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * SHOW - Detail hasil opname
     */
    public function show(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('show_stock_opname'), 403);

        $stockOpname->load(['items.product.category', 'items.adjustment', 'logs.user', 'pic', 'supervisor']);

        // Group items by variance type
        $summary = [
            'match' => $stockOpname->items->where('variance_type', 'match')->count(),
            'surplus' => $stockOpname->items->where('variance_type', 'surplus')->count(),
            'shortage' => $stockOpname->items->where('variance_type', 'shortage')->count(),
            'pending' => $stockOpname->items->where('variance_type', 'pending')->count(),
        ];

        return view('adjustment::stock-opname.show', compact('stockOpname', 'summary'));
    }

    /**
     * HELPER: Get products by scope
     */
    private function getProductsByScope(string $type, array $categoryIds, array $productIds)
    {
        $query = Product::query()
            ->select(['id', 'product_name', 'product_code', 'product_quantity', 'category_id'])
            ->with('category:id,category_name') // ✅ Eager load kategori
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('product_name'); // ✅ Order by name

        return match ($type) {
            'all' => $query->get(),
            'category' => $query->whereIn('category_id', $categoryIds)->get(),
            'custom' => $query->whereIn('id', $productIds)->get(),
            default => collect([]),
        };
    }

    /**
     * DESTROY - Hapus stock opname (hanya draft)
     */
    public function destroy(StockOpname $stockOpname)
    {
        abort_if(Gate::denies('delete_stock_opname'), 403);

        if ($stockOpname->status !== 'draft') {
            toast('❌ Hanya draft yang bisa dihapus!', 'error');
            return back();
        }

        try {
            DB::transaction(function () use ($stockOpname) {
                // ✅ Log sebelum delete
                StockOpnameLog::logActivity($stockOpname->id, StockOpnameLog::ACTION_DELETED, [
                    'description' => "Stock opname {$stockOpname->reference} dihapus",
                ]);

                // Hapus semua item
                $stockOpname->items()->delete();

                // Hapus logs (atau biarkan untuk audit trail?)
                // REKOMENDASI: JANGAN hapus logs, biarkan untuk audit
                // $stockOpname->logs()->delete(); // ← COMMENT atau hapus baris ini

                // Hapus header (soft delete)
                $stockOpname->delete();
            });

            toast('✅ Stock opname berhasil dihapus!', 'success');
            return redirect()->route('stock-opnames.index');
        } catch (\Throwable $e) {
            Log::error('StockOpname Destroy Error: ' . $e->getMessage());
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back();
        }
    }
}
