<?php

namespace Modules\Sale\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\Sale\Http\Requests\StorePosSaleRequest;


class PosController extends Controller
{

    public function index() {
        Cart::instance('sale')->destroy();

        $product_categories = Category::all();

        return view('sale::pos.index', compact('product_categories'));
    }


    public function store(StorePosSaleRequest $request)
{
    try {
        DB::beginTransaction();

        $due_amount = $request->total_amount - $request->paid_amount;
        $payment_status = 'Paid';
        if ($due_amount == $request->total_amount) {
            $payment_status = 'Unpaid';
        } elseif ($due_amount > 0) {
            $payment_status = 'Partial';
        }

        $sale = Sale::create([
            'date' => now()->format('Y-m-d'),
            'reference' => 'PSL',
            'tax_percentage' => $request->tax_percentage,
            'discount_percentage' => $request->discount_percentage,
            'shipping_amount' => $request->shipping_amount * 100,
            'paid_amount' => $request->paid_amount * 100,
            'total_amount' => $request->total_amount * 100,
            'due_amount' => $due_amount * 100,
            'status' => 'Completed',
            'payment_status' => $payment_status,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->payment_method == 'Transfer' ? $request->bank_name : null,
            'note' => $request->note,
            'tax_amount' => Cart::instance('sale')->tax() * 100,
            'discount_amount' => Cart::instance('sale')->discount() * 100,
        ]);

        $totalHpp = 0;

        foreach (Cart::instance('sale')->content() as $cart_item) {
            $sourceType = $cart_item->options->source_type ?? null; // 'new', 'second', 'manual'
            $itemHpp = 0;
            $productableId = null;
            $productableType = null;
            $itemName = $cart_item->name;

            if (!$sourceType) {
                throw new \Exception("Item {$itemName} tidak memiliki source_type.");
            }

            switch ($sourceType) {
                case 'new':
                    $product = Product::findOrFail($cart_item->id);
                    if ($product->product_quantity < $cart_item->qty) {
                        throw new \Exception('Stok produk ' . $product->product_name . ' tidak cukup.');
                    }
                    $product->decrement('product_quantity', $cart_item->qty);
                    $itemHpp = $product->product_cost;
                    $productableId = $product->id;
                    $productableType = Product::class;
                    break;

                case 'second':
                    // Ubah import jika ProductSecond di dalam module Product
                    $product = \Modules\Product\Entities\ProductSecond::findOrFail($cart_item->id);
                    if ($product->status === 'sold') {
                        throw new \Exception("Produk bekas {$product->name} sudah terjual.");
                    }
                    $product->update(['status' => 'sold']);
                    $itemHpp = $product->purchase_price;
                    $productableId = $product->id;
                    $productableType = \Modules\Product\Entities\ProductSecond::class;
                    break;

                case 'manual':
                    $itemHpp = 0;
                    $productableId = null;
                    $productableType = null;
                    break;

                default:
                    throw new \Exception("source_type {$sourceType} tidak dikenali.");
            }

            $subTotal = $cart_item->price * $cart_item->qty;
            $subTotalProfit = ($cart_item->price - $itemHpp) * $cart_item->qty;

            SaleDetails::create([
                'sale_id'                => $sale->id,
                'product_id'             => $sourceType !== 'manual' ? $cart_item->id : null,
                'product_name'           => $itemName,
                'product_code'           => $cart_item->options->code ?? null,
                'quantity'               => $cart_item->qty,
                'price'                  => $cart_item->price * 100,
                'unit_price'             => $cart_item->options->unit_price * 100 ?? $cart_item->price * 100,
                'sub_total'              => $cart_item->options->sub_total * 100 ?? $subTotal * 100,
                'product_discount_amount'=> $cart_item->options->product_discount * 100 ?? 0,
                'product_discount_type'  => $cart_item->options->product_discount_type ?? 'fixed',
                'product_tax_amount'     => $cart_item->options->product_tax * 100 ?? 0,
                // field tambahan
                'hpp'                    => $itemHpp * 100,
                'subtotal_profit'        => $subTotalProfit * 100,
                'source_type'            => $sourceType,
                'productable_id'         => $productableId,
                'productable_type'       => $productableType,
            ]);

            $totalHpp += ($itemHpp * $cart_item->qty);
        }

        // Update profit ke tabel sale (jika field ada)
        $sale->update([
            'total_hpp'    => $totalHpp * 100,
            'total_profit' => ($sale->total_amount - ($totalHpp * 100)),
        ]);

        Cart::instance('sale')->destroy();

        if ($sale->paid_amount > 0) {
            SalePayment::create([
                'date'           => now()->format('Y-m-d'),
                'reference'      => 'INV/' . $sale->reference,
                'amount'         => $sale->paid_amount,
                'sale_id'        => $sale->id,
                'payment_method' => $request->payment_method,
            ]);
        }

        DB::commit();

        session()->flash('swal-success', 'Transaksi Berhasil Disimpan!');
        return redirect()->route('app.pos.index');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

}
