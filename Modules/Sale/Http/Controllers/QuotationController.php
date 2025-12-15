<?php

namespace Modules\Sale\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\Quotation;
use Modules\Sale\Entities\QuotationDetail;
use Modules\Sale\DataTables\QuotationsDataTable;
use Modules\Sale\Http\Requests\StoreQuotationRequest;
use Modules\Sale\Http\Requests\UpdateQuotationRequest;

class QuotationController extends Controller
{
    public function index(QuotationsDataTable $dataTable)
    {
        abort_if(Gate::denies('access_sales'), 403);

        return $dataTable->render('sale::quotations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_sales'), 403);

        Cart::instance('quotation')->destroy();
        
        $customers = \Modules\People\Entities\Customer::all();

        return view('sale::quotations.create', compact('customers'));
    }

    public function store(StoreQuotationRequest $request)
    {
        DB::beginTransaction();

        try {
            $cart = Cart::instance('quotation');

            if ($cart->count() == 0) {
                return redirect()->back()->with('error', 'Please add items to the quotation!');
            }

            $shipping_amount = $request->shipping_amount;
            $tax_percentage = $request->tax_percentage;
            $discount_percentage = $request->discount_percentage;

            $sub_total = 0;
            foreach ($cart->content() as $cart_item) {
                $sub_total += ($cart_item->price * $cart_item->qty) + $cart_item->options->product_tax - $cart_item->options->product_discount;
            }

            $tax_amount = ($sub_total * $tax_percentage) / 100;
            $discount_amount = ($sub_total * $discount_percentage) / 100;
            $total_amount = $sub_total + $tax_amount - $discount_amount + $shipping_amount;

            $quotation = Quotation::create([
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'customer_name' => \Modules\People\Entities\Customer::findOrFail($request->customer_id)->customer_name,
                'tax_percentage' => $tax_percentage,
                'tax_amount' => $tax_amount,
                'discount_percentage' => $discount_percentage,
                'discount_amount' => $discount_amount,
                'shipping_amount' => $shipping_amount,
                'total_amount' => $total_amount,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            foreach ($cart->content() as $item) {
                QuotationDetail::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item->id,
                    'productable_type' => 'Modules\Product\Entities\Product',
                    'productable_id' => $item->id,
                    'source_type' => 'new',
                    'product_name' => $item->name,
                    'product_code' => $item->options->code,
                    'quantity' => $item->qty,
                    'price' => $item->price,
                    'unit_price' => $item->price,
                    'sub_total' => $item->options->sub_total,
                    'product_discount_amount' => $item->options->product_discount,
                    'product_discount_type' => $item->options->product_discount_type,
                    'product_tax_amount' => $item->options->product_tax,
                ]);
            }

            Cart::instance('quotation')->destroy();

            DB::commit();

            toast('Quotation Created!', 'success');

            return redirect()->route('quotations.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Quotation $quotation)
    {
        abort_if(Gate::denies('access_sales'), 403);
        $quotation->load(['quotationDetails', 'customer']);
        return view('sale::quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        abort_if(Gate::denies('edit_sales'), 403);

        $cart = Cart::instance('quotation');
        $cart->destroy();

        foreach ($quotation->quotationDetails as $item) {
            $cart->add([
                'id'      => $item->product_id,
                'name'    => $item->product_name,
                'qty'     => $item->quantity,
                'price'   => $item->price,
                'weight'  => 1,
                'options' => [
                    'product_discount'      => $item->product_discount_amount,
                    'product_discount_type' => $item->product_discount_type,
                    'sub_total'             => $item->sub_total,
                    'code'                  => $item->product_code,
                    'stock'                 => Product::find($item->product_id)->product_quantity ?? 0,
                    'unit'                  => Product::find($item->product_id)->product_unit ?? 'pc',
                    'product_tax'           => $item->product_tax_amount,
                    'unit_price'            => $item->unit_price
                ]
            ]);
        }

        
        $customers = \Modules\People\Entities\Customer::all();

        return view('sale::quotations.edit', compact('quotation', 'customers'));
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        DB::beginTransaction();

        try {
            $cart = Cart::instance('quotation');

            if ($cart->count() == 0) {
                return redirect()->back()->with('error', 'Please add items to the quotation!');
            }

            $shipping_amount = $request->shipping_amount;
            $tax_percentage = $request->tax_percentage;
            $discount_percentage = $request->discount_percentage;

            $sub_total = 0;
            foreach ($cart->content() as $cart_item) {
                $sub_total += ($cart_item->price * $cart_item->qty) + $cart_item->options->product_tax - $cart_item->options->product_discount;
            }

            $tax_amount = ($sub_total * $tax_percentage) / 100;
            $discount_amount = ($sub_total * $discount_percentage) / 100;
            $total_amount = $sub_total + $tax_amount - $discount_amount + $shipping_amount;

            $quotation->update([
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'customer_name' => \Modules\People\Entities\Customer::findOrFail($request->customer_id)->customer_name,
                'tax_percentage' => $tax_percentage,
                'tax_amount' => $tax_amount,
                'discount_percentage' => $discount_percentage,
                'discount_amount' => $discount_amount,
                'shipping_amount' => $shipping_amount,
                'total_amount' => $total_amount,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            $quotation->quotationDetails()->delete();

            foreach ($cart->content() as $item) {
                QuotationDetail::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item->id,
                    'productable_type' => 'Modules\Product\Entities\Product',
                    'productable_id' => $item->id,
                    'source_type' => 'new',
                    'product_name' => $item->name,
                    'product_code' => $item->options->code,
                    'quantity' => $item->qty,
                    'price' => $item->price,
                    'unit_price' => $item->price,
                    'sub_total' => $item->options->sub_total,
                    'product_discount_amount' => $item->options->product_discount,
                    'product_discount_type' => $item->options->product_discount_type,
                    'product_tax_amount' => $item->options->product_tax,
                ]);
            }

            Cart::instance('quotation')->destroy();
            DB::commit();

            toast('Quotation Updated!', 'success');
            return redirect()->route('quotations.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Quotation $quotation)
    {
        abort_if(Gate::denies('delete_sales'), 403);
        $quotation->delete();
        toast('Quotation Deleted!', 'warning');
        return redirect()->route('quotations.index');
    }

    public function convertToSale(Quotation $quotation)
    {
        abort_if(Gate::denies('create_sales'), 403);

        $cart = Cart::instance('sale');
        $cart->destroy();

        foreach ($quotation->quotationDetails as $item) {
           $cart->add([
                'id'      => $item->product_id,
                'name'    => $item->product_name,
                'qty'     => $item->quantity,
                'price'   => $item->price,
                'weight'  => 1,
                'options' => [
                    'product_discount'      => $item->product_discount_amount,
                    'product_discount_type' => $item->product_discount_type,
                    'sub_total'             => $item->sub_total,
                    'code'                  => $item->product_code,
                    'stock'                 => Product::find($item->product_id)->product_quantity ?? 0,
                    'unit'                  => Product::find($item->product_id)->product_unit ?? 'pc',
                    'product_tax'           => $item->product_tax_amount,
                    'unit_price'            => $item->unit_price
                ]
            ]);
        }

        $quotation->update(['status' => 'Converted']);

        return redirect()->route('sales.create', [
            'from_quotation' => 1,
            'quotation_id' => $quotation->id
        ]);
    }
}
