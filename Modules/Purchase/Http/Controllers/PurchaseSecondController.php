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
    /**
     * Display a listing of purchase seconds with filters
     */
    public function index(PurchaseSecondDataTable $dataTable)
    {
        abort_if(Gate::denies('access_purchases'), 403);

        $query = PurchaseSecond::query();

        // === FILTER BY QUICK FILTER ===
        $from = null;
        $to = null;

        switch (request('quick_filter')) {
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
                break;
            default:
                $from = request('from') ? request('from') : null;
                $to = request('to') ? request('to') : null;
        }

        // Apply date filters
        if(!$from && request('quick_filter') == 'today') {
             $from = now()->toDateString();
             $to = now()->toDateString();
        }

        if ($from && request('quick_filter') !== 'all') {
            $query->whereDate('date', '>=', $from);
        }
        if ($to && request('quick_filter') !== 'all') {
            $query->whereDate('date', '<=', $to);
        }

        // === FILTER BY CUSTOMER ===
        if (request('customer')) {
            $query->where('customer_name', 'like', '%' . request('customer') . '%');
        }

        // === FILTER BY PAYMENT STATUS ===
        if (request('payment_status')) {
            $query->where('payment_status', request('payment_status'));
        }

        // === CALCULATE SUMMARY ===
        $total_purchases = $query->count();
        $total_amount = $query->sum('total_amount');
        $total_paid = $query->sum('paid_amount');
        $total_due = $query->sum('due_amount');

        $summary = compact('total_purchases', 'total_amount', 'total_paid', 'total_due');

        return $dataTable->render('purchase::second.index', compact('summary', 'from', 'to'));
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
    /**
     * Store a newly created purchase second in storage
     */
    public function store(StorePurchaseSecondRequest $request)
    {
        abort_if(Gate::denies('create_purchases'), 403);

        DB::beginTransaction();

        try {
            // Get cart from request json
            $cart = json_decode($request->cart_json, true);

            if (empty($cart)) {
                toast('Keranjang kosong! Tambahkan produk terlebih dahulu.', 'error');
                return redirect()->back();
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

            DB::commit();

            toast('Pembelian Bekas berhasil disimpan!', 'success');

            return redirect()->route('purchases.second.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toast('Terjadi kesalahan: ' . $e->getMessage(), 'error');
            return redirect()->back();
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
            toast('Pembelian dengan status Completed tidak dapat diedit.', 'warning');
            return redirect()->back();
        }

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
            toast('Tidak dapat mengubah status Completed ke Pending.', 'error');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Get cart from request json
            $cart = json_decode($request->cart_json, true);

            if (empty($cart)) {
                toast('Keranjang kosong! Tambahkan produk terlebih dahulu.', 'error');
                return redirect()->back();
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
                // Determine product id (checking if it exists or if it's passed differently)
                $productId = $item['id'];
                $product = ProductSecond::findOrFail($productId);

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

            DB::commit();

            toast('Pembelian Bekas berhasil diupdate!', 'success');

            return redirect()->route('purchases.second.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toast('Terjadi kesalahan: ' . $e->getMessage(), 'error');
            return redirect()->back();
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
            toast('Terjadi kesalahan: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }
}
