<?php

namespace Modules\Purchase\Http\Controllers;

use Modules\Purchase\DataTables\PurchaseDataTable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Product\Entities\Product;
use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchaseDetail;
use Modules\People\Entities\Supplier;
use Gloudemans\Shoppingcart\Facades\Cart;
use Modules\Purchase\Http\Requests\StorePurchaseRequest;
use Modules\Purchase\Http\Requests\UpdatePurchaseRequest;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases with filtering
     * Mirip dengan ExpenseController
     */
    public function index(Request $request, PurchaseDataTable $dataTable)
    {
        abort_if(Gate::denies('access_purchases'), 403);

        // Calculate Stats (Duplicate logic matching DataTable query for consistency)
        $query = Purchase::query();

        // === FILTER BY QUICK FILTER ===
        $from = null;
        $to = null;

        switch ($request->get('quick_filter')) {
            case 'yesterday':
                $from = $to = now()->subDay()->toDateString();
                break;
            case 'this_week':
                $from = now()->startOfWeek()->toDateString();
                $to = now()->toDateString();
                break;
            case 'this_month':
                $from = now()->startOfMonth()->toDateString();
                $to = now()->toDateString();
                break;
            case 'last_month':
                $from = now()->subMonth()->startOfMonth()->toDateString();
                $to = now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'all':
                // No date filter
                break;
            default:
                // Default: Today atau custom range dari request
                if (!$request->has('quick_filter') && !$request->has('from')) {
                     // Default if needed, but keeping simple for now
                }
                $from = $request->filled('from') ? $request->from : null;
                $to = $request->filled('to') ? $request->to : null;
        }

        // Apply date filters
        if(!$from && $request->get('quick_filter') == 'today') {
             $from = now()->toDateString();
             $to = now()->toDateString();
        }

        if ($from && $request->get('quick_filter') !== 'all') {
            $query->whereDate('date', '>=', $from);
        }
        if ($to && $request->get('quick_filter') !== 'all') {
            $query->whereDate('date', '<=', $to);
        }

        // === FILTER BY SUPPLIER ===
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // === FILTER BY PAYMENT STATUS ===
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // === CALCULATE SUMMARY ===
        $total_purchases = $query->count();
        $total_amount = $query->sum('total_amount');
        $total_paid = $query->sum('paid_amount');
        $total_due = $query->sum('due_amount');

        // Get suppliers for filter dropdown
        $suppliers = Supplier::orderBy('supplier_name')->get();

        return $dataTable->render('purchase::baru.index', compact('suppliers', 'total_purchases', 'total_amount', 'total_paid', 'total_due', 'from', 'to'));
    }

    /**
     * Show the form for creating a new purchase
     */
    public function create()
    {
        abort_if(Gate::denies('create_purchases'), 403);

        // Clear cart sebelum create baru
        Cart::instance('purchase')->destroy();

        return view('purchase::baru.create');
    }

    /**
     * Store a newly created purchase (SIMPLIFIED untuk UMKM)
     */
    public function store(StorePurchaseRequest $request)
    {
        $this->syncCartWithJson($request);

        DB::transaction(function () use ($request) {
            // Hitung payment status sederhana
            $due_amount = $request->total_amount - $request->paid_amount;
            $payment_status = $due_amount == 0 ? 'Lunas' : 'Belum Lunas';

            // Create purchase - TANPA tax, discount, shipping
            $purchase = Purchase::create([
                'date' => $request->date,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => Supplier::findOrFail($request->supplier_id)->supplier_name,
                'total_amount' => $request->total_amount, // Langsung Rupiah
                'paid_amount' => $request->paid_amount, // Langsung Rupiah
                'due_amount' => $due_amount, // Langsung Rupiah
                'status' => $request->status,
                'payment_status' => $payment_status,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name, // BARU
                'note' => $request->note,
                'user_id' => auth()->id(), // BARU - Audit trail
            ]);

            // Create purchase details dari Cart
            foreach (Cart::instance('purchase')->content() as $cart_item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'unit_price' => $cart_item->price, // Langsung Rupiah
                    'sub_total' => $cart_item->subtotal, // Langsung Rupiah
                ]);

                // Update stock produk jika status Completed
                if ($request->status == 'Completed') {
                    $product = Product::lockForUpdate()->findOrFail($cart_item->id);
                    $product->update([
                        'product_quantity' => $product->product_quantity + $cart_item->qty,
                    ]);

                    // Catat ke stock_movements untuk audit trail
                    DB::table('stock_movements')->insert([
                        'product_id' => $product->id,
                        'ref_type' => 'purchase',
                        'ref_id' => $purchase->id,
                        'type' => 'in',
                        'quantity' => $cart_item->qty,
                        'description' => 'Purchase #' . ($purchase->reference ?? $purchase->id),
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Clear cart setelah simpan
            Cart::instance('purchase')->destroy();
        });

        toast('Pembelian berhasil disimpan!', 'success');
        return redirect()->route('purchases.index');
    }

    /**
     * Display the specified purchase
     */
    public function show(Purchase $purchase)
    {
        abort_if(Gate::denies('show_purchases'), 403);

        $purchase->load(['purchaseDetails.product', 'supplier', 'user']);

        return view('purchase::baru.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified purchase
     */
    public function edit(Purchase $purchase)
    {
        abort_if(Gate::denies('edit_purchases'), 403);

        $purchase_details = $purchase->purchaseDetails;

        // Clear cart dan populate dengan data existing
        Cart::instance('purchase')->destroy();
        $cart = Cart::instance('purchase');

        foreach ($purchase_details as $purchase_detail) {
            $cart->add([
                'id' => $purchase_detail->product_id,
                'name' => $purchase_detail->product_name,
                'qty' => $purchase_detail->quantity,
                'price' => $purchase_detail->unit_price, // Langsung Rupiah
                'weight' => 1,
                'options' => [
                    'sub_total' => $purchase_detail->sub_total, // Langsung Rupiah
                    'code' => $purchase_detail->product_code,
                    'stock' => Product::findOrFail($purchase_detail->product_id)->product_quantity,
                    'unit_price' => $purchase_detail->unit_price,
                ],
            ]);
        }

        return view('purchase::baru.edit', compact('purchase'));
    }

    /**
     * Update the specified purchase (SIMPLIFIED untuk UMKM)
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        $this->syncCartWithJson($request);

        DB::transaction(function () use ($request, $purchase) {
            // Hitung payment status sederhana
            $due_amount = $request->total_amount - $request->paid_amount;
            $payment_status = $due_amount == 0 ? 'Lunas' : 'Belum Lunas';

            // Restore stock jika purchase sebelumnya Completed
            foreach ($purchase->purchaseDetails as $purchase_detail) {
                if ($purchase->status == 'Completed') {
                    $product = Product::lockForUpdate()->findOrFail($purchase_detail->product_id);
                    $product->update([
                        'product_quantity' => $product->product_quantity - $purchase_detail->quantity,
                    ]);

                    // Catat restore stok ke stock_movements
                    DB::table('stock_movements')->insert([
                        'product_id' => $product->id,
                        'ref_type' => 'purchase',
                        'ref_id' => $purchase->id,
                        'type' => 'out',
                        'quantity' => $purchase_detail->quantity,
                        'description' => 'Purchase Restore (Edit) #' . ($purchase->reference ?? $purchase->id),
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                // Delete old purchase details
                $purchase_detail->delete();
            }

            // Update purchase - TANPA tax, discount, shipping
            $purchase->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => Supplier::findOrFail($request->supplier_id)->supplier_name,
                'total_amount' => $request->total_amount, // Langsung Rupiah
                'paid_amount' => $request->paid_amount, // Langsung Rupiah
                'due_amount' => $due_amount, // Langsung Rupiah
                'status' => $request->status,
                'payment_status' => $payment_status,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name, // BARU
                'note' => $request->note,
                // user_id tidak diupdate, tetap user yang create
            ]);

            // Create new purchase details dari Cart
            foreach (Cart::instance('purchase')->content() as $cart_item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'unit_price' => $cart_item->price, // Langsung Rupiah
                    'sub_total' => $cart_item->subtotal, // Langsung Rupiah
                ]);

                // Update stock produk jika status Completed
                if ($request->status == 'Completed') {
                    $product = Product::lockForUpdate()->findOrFail($cart_item->id);
                    $product->update([
                        'product_quantity' => $product->product_quantity + $cart_item->qty,
                    ]);

                    // Catat ke stock_movements untuk audit trail
                    DB::table('stock_movements')->insert([
                        'product_id' => $product->id,
                        'ref_type' => 'purchase',
                        'ref_id' => $purchase->id,
                        'type' => 'in',
                        'quantity' => $cart_item->qty,
                        'description' => 'Purchase (Updated) #' . ($purchase->reference ?? $purchase->id),
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Clear cart setelah update
            Cart::instance('purchase')->destroy();
        });

        toast('Pembelian berhasil diperbarui!', 'info');
        return redirect()->route('purchases.index');
    }

    /**
     * Remove the specified purchase from storage
     */
    public function destroy(Purchase $purchase)
    {
        abort_if(Gate::denies('delete_purchases'), 403);

        // Restore stock jika purchase Completed sebelum delete
        DB::transaction(function () use ($purchase) {
            if ($purchase->status == 'Completed') {
                foreach ($purchase->purchaseDetails as $purchase_detail) {
                    $product = Product::lockForUpdate()->findOrFail($purchase_detail->product_id);
                    $product->update([
                        'product_quantity' => $product->product_quantity - $purchase_detail->quantity,
                    ]);

                    // Catat restore stok ke stock_movements
                    DB::table('stock_movements')->insert([
                        'product_id' => $product->id,
                        'ref_type' => 'purchase',
                        'ref_id' => $purchase->id,
                        'type' => 'out',
                        'quantity' => $purchase_detail->quantity,
                        'description' => 'Purchase Restore (Deleted) #' . ($purchase->reference ?? $purchase->id),
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $purchase->delete();
        });

        toast('Pembelian berhasil dihapus!', 'warning');
        return redirect()->route('purchases.index');
    }

    /**
     * Sync Cart instance with JSON data from request (Shim for Client-Side Cart)
     */
    private function syncCartWithJson($request)
    {
        if ($request->has('cart_json') && !empty($request->cart_json)) {
            $cart = Cart::instance('purchase');
            $cart->destroy();
            $items = json_decode($request->cart_json, true);

            if (is_array($items)) {
                foreach ($items as $item) {
                    $cart->add([
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'weight' => 1,
                        'options' => [
                            'code' => $item['code'],
                            'stock' => $item['stock'] ?? 0,
                        ]
                    ]);
                }
            }
        }
    }
}
