<?php

namespace Modules\Sale\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\Sale\Http\Requests\StorePosSaleRequest;

class PosController extends Controller
{
    public function index()
    {
        Cart::instance('sale')->destroy();

        $product_categories = Category::all();

        return view('sale::pos.index', compact('product_categories'));
    }

    public function store(StorePosSaleRequest $request)
{
    try {
        DB::beginTransaction();

        $due_amount = $request->total_amount - $request->paid_amount;
        $payment_status = $due_amount == $request->total_amount ? 'Unpaid' : ($due_amount > 0 ? 'Partial' : 'Paid');

        $sale = Sale::create([
            'date'                => now()->format('Y-m-d'),
            'reference'           => 'PSL',
            'user_id'             => auth()->id(),
            'tax_percentage'      => $request->tax_percentage,
            'discount_percentage' => $request->discount_percentage,
            'shipping_amount'     => (int) $request->shipping_amount,
            'paid_amount'         => (int) $request->paid_amount,
            'total_amount'        => (int) $request->total_amount,
            'due_amount'          => (int) $due_amount,
            'status'              => 'Completed',
            'payment_status'      => $payment_status,
            'payment_method'      => $request->payment_method,
            'bank_name'           => $request->payment_method == 'Transfer' ? $request->bank_name : null,
            'note'                => $request->note,
            'tax_amount'          => (int) Cart::instance('sale')->tax(),
            'discount_amount'     => (int) Cart::instance('sale')->discount(),
        ]);

        $totalHpp = 0;

        foreach (Cart::instance('sale')->content() as $item) {
            $sourceType = $item->options->source_type ?? null; // 'new','second','manual'
            if (!$sourceType) throw new \Exception("Item {$item->name} tidak memiliki source_type.");

            $itemHpp = 0;
            $productableId = null;
            $productableType = null;

            switch ($sourceType) {
                case 'new':
                    $product = \Modules\Product\Entities\Product::findOrFail($item->id);
                    if ($product->product_quantity < $item->qty) {
                        throw new \Exception('Stok produk ' . $product->product_name . ' tidak cukup.');
                    }
                    $product->decrement('product_quantity', $item->qty);
                    $itemHpp         = (int) $product->product_cost; // simpan rupiah utuh
                    $productableId   = $product->id;
                    $productableType = \Modules\Product\Entities\Product::class;
                    break;

                case 'second':
                    $second = \Modules\Product\Entities\ProductSecond::findOrFail($item->id);
                    if ($second->status === 'sold') {
                        throw new \Exception("Produk bekas {$second->name} sudah terjual.");
                    }
                    $second->update(['status' => 'sold']);
                    $itemHpp         = (int) round($second->purchase_price);
                    $productableId   = $second->id;
                    $productableType = \Modules\Product\Entities\ProductSecond::class;
                    break;

                case 'manual':
                    $itemHpp = 0;
                    break;

                default:
                    throw new \Exception("source_type {$sourceType} tidak dikenali.");
            }

            $subTotal       = (int) $item->price * (int) $item->qty;
            $subTotalProfit = $subTotal - ($itemHpp * (int) $item->qty);

            SaleDetails::create([
                'sale_id'                 => $sale->id,
                'product_id'              => $sourceType !== 'manual' ? $item->id : null,
                'product_name'            => $item->name,
                'item_name'               => $item->name,
                'product_code'            => $item->options->code ?? '-',
                'quantity'                => (int) $item->qty,
                'price'                   => (int) $item->price,
                'unit_price'              => (int) ($item->options->unit_price ?? $item->price),
                'sub_total'               => (int) ($item->options->sub_total ?? $subTotal),
                'product_discount_amount' => (int) ($item->options->product_discount ?? 0),
                'product_discount_type'   => $item->options->product_discount_type ?? 'fixed',
                'product_tax_amount'      => (int) ($item->options->product_tax ?? 0),

                // tambahan
                'hpp'                     => (int) $itemHpp,
                'subtotal_profit'         => (int) $subTotalProfit,
                'source_type'             => $sourceType,
                'productable_id'          => $productableId,
                'productable_type'        => $productableType,
            ]);

            $totalHpp += $itemHpp * (int) $item->qty;
        }

        $sale->update([
            'total_hpp'    => $totalHpp,
            'total_profit' => (int) $sale->total_amount - $totalHpp,
            'due_amount'   => max((int)$sale->total_amount - (int)$sale->paid_amount, 0),
        ]);

        Cart::instance('sale')->destroy();

        if ((int)$sale->paid_amount > 0) {
            SalePayment::create([
                'date'           => now()->format('Y-m-d'),
                'reference'      => 'INV/' . $sale->reference,
                'amount'         => (int) $sale->paid_amount,
                'sale_id'        => $sale->id,
                'payment_method' => $request->payment_method,
            ]);
        }

        DB::commit();
        session()->flash('swal-success', 'Transaksi Berhasil Disimpan!');
        return redirect()->route('app.pos.index');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

}
