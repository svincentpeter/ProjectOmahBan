<?php

namespace Modules\Purchase\Http\Controllers;

use Modules\Purchase\DataTables\PurchasePaymentsDataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchasePayment;

class PurchasePaymentsController extends Controller
{

    public function index($purchase_id, PurchasePaymentsDataTable $dataTable) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return $dataTable->render('purchase::payments.index', compact('purchase'));
    }


    public function create($purchase_id) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return view('purchase::payments.create', compact('purchase'));
    }


    public function store(Request $request) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:1000',
            'purchase_id' => 'required|exists:purchases,id',
            'payment_method' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Sanitize amount (remove formatting if any)
            $amount = preg_replace('/[^0-9]/', '', $request->amount);

            PurchasePayment::create([
                'date' => $request->date,
                'reference' => $request->reference ?: PurchasePayment::generateReference(),
                'amount' => (int) $amount,
                'note' => $request->note,
                'purchase_id' => $request->purchase_id,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name,
                'user_id' => auth()->id(),
            ]);

            // Use the new recalcPaymentStatus method
            $purchase = Purchase::findOrFail($request->purchase_id);
            $purchase->recalcPaymentStatus();
        });

        toast('Pembayaran berhasil ditambahkan!', 'success');

        return redirect()->route('purchases.index');
    }


    public function edit($purchase_id, PurchasePayment $purchasePayment) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return view('purchase::payments.edit', compact('purchasePayment', 'purchase'));
    }


    public function update(Request $request, PurchasePayment $purchasePayment) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:1000',
            'purchase_id' => 'required|exists:purchases,id',
            'payment_method' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $purchasePayment) {
            $amount = preg_replace('/[^0-9]/', '', $request->amount);

            $purchasePayment->update([
                'date' => $request->date,
                'reference' => $request->reference ?: $purchasePayment->reference,
                'amount' => (int) $amount,
                'note' => $request->note,
                'purchase_id' => $request->purchase_id,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name,
            ]);

            // recalcPaymentStatus is called automatically via model boot events
        });

        toast('Pembayaran berhasil diperbarui!', 'info');

        return redirect()->route('purchases.index');
    }


    public function destroy(PurchasePayment $purchasePayment) {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = $purchasePayment->purchase;
        $purchasePayment->delete();

        // Recalc after delete - model event should handle this but let's be explicit
        if ($purchase) {
            $purchase->recalcPaymentStatus();
        }

        toast('Pembayaran berhasil dihapus!', 'warning');

        return redirect()->route('purchases.index');
    }
}

