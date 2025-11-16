<?php

namespace Modules\Purchase\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Purchase\Entities\PurchaseSecond;
use Modules\Purchase\Entities\PurchaseSecondDetail;
use Modules\Purchase\Http\Requests\StorePurchaseSecondRequest;
use Modules\Purchase\Http\Requests\UpdatePurchaseSecondRequest;
use Modules\Product\Entities\ProductSecond;
use Modules\Purchase\DataTables\PurchaseSecondDataTable;

class PurchaseSecondController extends Controller
{
    /**
     * Display a listing of purchase seconds with filters
     */
    public function index(PurchaseSecondDataTable $dataTable)
    {
        abort_if(Gate::denies('access_purchases'), 403);

        // ==== Filter sama seperti sebelumnya ====
        $quickFilter = request('quick_filter', 'all');
        $startDate = request('from');
        $endDate = request('to');
        $customerName = request('customer');
        $paymentStatus = request('payment_status');

        $summaryQuery = PurchaseSecond::query();

        switch ($quickFilter) {
            case 'yesterday':
                $summaryQuery->whereDate('date', now()->subDay());
                break;
            case 'this_week':
                $summaryQuery->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $summaryQuery->whereMonth('date', now()->month)->whereYear('date', now()->year);
                break;
            case 'last_month':
                $summaryQuery->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
                break;
        }

        if ($startDate && $endDate) {
            $summaryQuery->whereBetween('date', [$startDate, $endDate]);
        }

        if ($customerName) {
            $summaryQuery->where('customer_name', 'like', "%{$customerName}%");
        }

        if ($paymentStatus) {
            $summaryQuery->where('payment_status', $paymentStatus);
        }

        $summary = [
            'total_purchases' => (clone $summaryQuery)->count(), // ✅ TAMBAHAN
            'total_amount' => (clone $summaryQuery)->sum('total_amount'),
            'total_paid' => (clone $summaryQuery)->sum('paid_amount'),
            'total_due' => (clone $summaryQuery)->sum('due_amount'),
        ];

        return $dataTable->render('purchase::second.index', compact('summary'));
    }

    /**
     * Show the form for creating a new purchase second
     */
    public function create()
    {
        abort_if(Gate::denies('create_purchases'), 403);

        // Get available second products (status = available or for_sale)
        $products = ProductSecond::whereIn('status', ['available', 'for_sale'])
            ->orderBy('name')
            ->get();

        return view('purchase::second.create', compact('products'));
    }

    /**
     * Store a newly created purchase second in storage
     */
    public function store(StorePurchaseSecondRequest $request)
    {
        abort_if(Gate::denies('create_purchases'), 403);

        DB::beginTransaction();

        try {
            // Get cart from session
            $cart = session()->get('cart_purchase_second', []);

            if (empty($cart)) {
                return redirect()->back()->with('error', 'Keranjang kosong! Tambahkan produk terlebih dahulu.');
            }

            // Create Purchase Second
            $purchase = PurchaseSecond::create([
                'date' => $request->date,
                'reference' => $request->reference ?? PurchaseSecond::nextReference(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->payment_method === 'Transfer' ? $request->bank_name : null,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->due_amount,
                'payment_status' => $request->due_amount == 0 ? 'Lunas' : 'Belum Lunas',
                'note' => $request->note,
                'user_id' => auth()->id(),
            ]);

            // Create Purchase Second Details
            foreach ($cart as $item) {
                $product = ProductSecond::findOrFail($item['id']);

                PurchaseSecondDetail::create([
                    'purchase_second_id' => $purchase->id,
                    'product_second_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->uniquecode,
                    'condition_notes' => $product->conditionnotes,
                    'quantity' => 1, // Always 1 for second products
                    'unit_price' => $item['price'],
                    'sub_total' => $item['price'],
                ]);

                // Update product status to 'sold' or 'in_stock'
                if ($request->status === 'Completed') {
                    $product->update(['status' => 'in_stock']);
                }
            }

            // Clear cart session
            session()->forget('cart_purchase_second');

            DB::commit();

            toast('Pembelian Bekas berhasil disimpan!', 'success');

            return redirect()->route('purchases.second.index');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase second
     */
    public function show(PurchaseSecond $purchaseSecond)
    {
        abort_if(Gate::denies('show_purchases'), 403);

        $purchaseSecond->load(['purchaseSecondDetails.productSecond', 'user']);

        return view('purchase::second.show', compact('purchaseSecond'));
    }

    /**
     * Show the form for editing the specified purchase second
     */
    public function edit(PurchaseSecond $purchaseSecond)
    {
        abort_if(Gate::denies('edit_purchases'), 403);

        // Only allow edit if status is Pending
        if ($purchaseSecond->status === 'Completed') {
            return redirect()->back()->with('error', 'Pembelian dengan status Completed tidak dapat diedit.');
        }

        // Load cart from existing purchase details
        $cart = [];
        foreach ($purchaseSecond->purchaseSecondDetails as $detail) {
            $cart[] = [
                'id' => $detail->product_second_id,
                'name' => $detail->product_name,
                'code' => $detail->product_code,
                'price' => $detail->unit_price,
                'condition_notes' => $detail->condition_notes,
            ];
        }

        session()->put('cart_purchase_second', $cart);

        // Get available second products
        $products = ProductSecond::whereIn('status', ['available', 'for_sale'])
            ->orderBy('name')
            ->get();

        return view('purchase::second.edit', compact('purchaseSecond', 'products'));
    }

    /**
     * Update the specified purchase second in storage
     */
    public function update(UpdatePurchaseSecondRequest $request, PurchaseSecond $purchaseSecond)
    {
        abort_if(Gate::denies('edit_purchases'), 403);

        // Only allow update if status is Pending
        if ($purchaseSecond->status === 'Completed' && $request->status === 'Pending') {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status Completed ke Pending.');
        }

        DB::beginTransaction();

        try {
            // Get cart from session
            $cart = session()->get('cart_purchase_second', []);

            if (empty($cart)) {
                return redirect()->back()->with('error', 'Keranjang kosong! Tambahkan produk terlebih dahulu.');
            }

            // Update Purchase Second
            $purchaseSecond->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->payment_method === 'Transfer' ? $request->bank_name : null,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->due_amount,
                'payment_status' => $request->due_amount == 0 ? 'Lunas' : 'Belum Lunas',
                'note' => $request->note,
            ]);

            // Delete old details
            $purchaseSecond->purchaseSecondDetails()->delete();

            // Create new details
            foreach ($cart as $item) {
                $product = ProductSecond::findOrFail($item['id']);

                PurchaseSecondDetail::create([
                    'purchase_second_id' => $purchaseSecond->id,
                    'product_second_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->uniquecode,
                    'condition_notes' => $product->conditionnotes,
                    'quantity' => 1,
                    'unit_price' => $item['price'],
                    'sub_total' => $item['price'],
                ]);

                // Update product status
                if ($request->status === 'Completed') {
                    $product->update(['status' => 'in_stock']);
                }
            }

            // Clear cart session
            session()->forget('cart_purchase_second');

            DB::commit();

            toast('Pembelian Bekas berhasil diupdate!', 'success');

            return redirect()->route('purchases.second.index');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase second from storage
     */
    public function destroy(PurchaseSecond $purchaseSecond)
    {
        abort_if(Gate::denies('delete_purchases'), 403);

        DB::beginTransaction();

        try {
            // If status is Completed, restore product status to 'available'
            if ($purchaseSecond->status === 'Completed') {
                foreach ($purchaseSecond->purchaseSecondDetails as $detail) {
                    $product = ProductSecond::find($detail->product_second_id);
                    if ($product) {
                        $product->update(['status' => 'available']);
                    }
                }
            }

            // Delete details (cascade akan handle ini, tapi explicit lebih aman)
            $purchaseSecond->purchaseSecondDetails()->delete();

            // Delete purchase
            $purchaseSecond->delete();

            DB::commit();

            toast('Pembelian Bekas berhasil dihapus!', 'success');

            return redirect()->route('purchases.second.index');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
