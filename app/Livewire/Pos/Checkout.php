<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;

class Checkout extends Component
{
    // Event listeners Livewire
    public $listeners = ['productSelected', 'discountModalRefresh'];

    // Properti keranjang & data pendukung
    public $cart_instance;
    public $global_discount;
    public $global_tax;
    public $shipping;
    public $quantity;
    public $check_quantity;
    public $discount_type;
    public $item_discount;
    public $total_amount;

    // Properti checkout
    public $paid_amount;
    public $payment_method = 'Tunai';
    public $bank_name;
    public $note;

    // Validasi dasar
    protected $rules = [
        'payment_method'  => 'required|string|in:Tunai,Transfer',
        'paid_amount'     => 'required|numeric|min:0',
    ];

    public function mount($cartInstance)
    {
        $this->cart_instance   = $cartInstance;
        $this->global_discount = 0;
        $this->global_tax      = 0;
        $this->shipping        = 0.00;
        $this->check_quantity  = [];
        $this->quantity        = [];
        $this->discount_type   = [];
        $this->item_discount   = [];
        $this->total_amount    = Cart::instance($this->cart_instance)->total();
        $this->paid_amount     = $this->total_amount;
    }

    public function hydrate()
    {
        // Recalculate total tiap render/hydrate
        $this->total_amount = $this->calculateTotal();
    }

    public function render()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();

        return view('livewire.pos.checkout', [
            'cart_items' => $cart_items,
        ]);
    }
    /**
     * Simpan transaksi POS, termasuk bank_name untuk Transfer.
     */
    public function store()
    {
        // Validasi dasar
        $this->validate();

        // Validasi kondisional untuk bank_name
        if ($this->payment_method === 'Transfer') {
            $this->validate([
                'bank_name' => 'required|string|max:255',
            ]);
        }

        DB::transaction(function () {
            // Hitung due & status pembayaran
            $due = $this->total_amount - $this->paid_amount;
            $paymentStatus = $due <= 0
                ? 'Paid'
                : ($due < $this->total_amount ? 'Partial' : 'Unpaid');

            // Simpan header Sale
            $sale = Sale::create([
                'date'               => now()->toDateString(),
                'tax_percentage'     => Cart::instance($this->cart_instance)->tax(),
                'discount_percentage'=> Cart::instance($this->cart_instance)->discount(),
                'shipping_amount'    => $this->shipping,
                'total_amount'       => $this->total_amount,
                'paid_amount'        => $this->paid_amount,
                'due_amount'         => $due,
                'status'             => 'Completed',
                'payment_status'     => $paymentStatus,
                'payment_method'     => $this->payment_method,
                'bank_name'          => $this->payment_method === 'Transfer' ? $this->bank_name : null,
                'note'               => $this->note,
            ]);

            $totalHpp = 0;

            // Proses setiap item di keranjang
            foreach (Cart::instance($this->cart_instance)->content() as $item) {
                $source = $item->options->source_type; // 'product', 'product_second', atau 'manual'
                $hpp    = 0;
                $model  = null;

                if ($source === 'product') {
                    $model = Product::findOrFail($item->id);
                    if ($model->product_quantity < $item->qty) {
                        throw new \Exception("Stok produk {$model->product_name} tidak mencukupi.");
                    }
                    $model->decrement('product_quantity', $item->qty);
                    $hpp = $model->product_cost;
                }
                elseif ($source === 'product_second') {
                    $model = ProductSecond::findOrFail($item->id);
                    if ($model->status === 'sold') {
                        throw new \Exception("Produk bekas {$model->name} sudah terjual.");
                    }
                    $model->update(['status' => 'sold']);
                    $hpp = $model->purchase_price;
                }

                $subTotal  = $item->price * $item->qty;
                $subProfit = ($item->price - $hpp) * $item->qty;
                $totalHpp += $hpp * $item->qty;

                // Simpan SaleDetails
                SaleDetails::create([
                    'sale_id'             => $sale->id,
                    'item_name'              => $item->name,
                    'product_id'          => $model ? $model->id : null,
                    'productable_id'      => $model ? $model->id : null,
                    'productable_type'    => $model ? get_class($model) : null,
                    'source_type'         => $source,
                    'product_name'        => $item->name,
                    'product_code'        => $item->options->code ?? null,
                    'quantity'            => $item->qty,
                    'price'               => $item->price,
                    'unit_price'          => $item->options->unit_price,
                    'sub_total'           => $subTotal,
                    'hpp'                 => $hpp,
                    'subtotal_profit'     => $subProfit,
                    'product_discount_amount'=> $item->options->product_discount,
                    'product_discount_type'  => $item->options->product_discount_type,
                    'product_tax_amount'     => $item->options->product_tax,
                    'product_discount_type'  => $item->options->product_discount_type,
                    'product_tax_amount'     => $item->options->product_tax,
                ]);
            }

            // Update total_hpp & total_profit di header
            $sale->update([
                'total_hpp'    => $totalHpp,
                'total_profit' => $sale->total_amount - $totalHpp,
            ]);

            // Simpan entry payment jika ada pembayaran
            if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date'           => now()->toDateString(),
                    'reference'      => 'INV/' . $sale->reference,
                    'amount'         => $sale->paid_amount,
                    'sale_id'        => $sale->id,
                    'payment_method' => $this->payment_method,
                ]);
            }

            // Bersihkan keranjang
            Cart::instance($this->cart_instance)->destroy();
        });

        session()->flash('message', 'Transaksi berhasil disimpan!');
        return redirect()->route('app.pos.index');
    }

    /** Helper methods untuk manage keranjang **/

    public function calculateTotal()
    {
        return Cart::instance($this->cart_instance)->total() + $this->shipping;
    }

    public function resetCart()
    {
        Cart::instance($this->cart_instance)->destroy();
    }

    public function productSelected($product)
    {
        $cart = Cart::instance($this->cart_instance);

        $exists = $cart->search(fn($ci, $row) => $ci->id == $product['id']);
        if ($exists->isNotEmpty()) {
            session()->flash('message', 'Product already in cart!');
            return;
        }

        $cart->add([
            'id'      => $product['id'],
            'name'    => $product['product_name'],
            'qty'     => 1,
            'price'   => $this->calculate($product)['price'],
            'weight'  => 1,
            'options' => [
                'product_discount'      => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total'             => $this->calculate($product)['sub_total'],
                'code'                  => $product['product_code'],
                'stock'                 => $product['product_quantity'],
                'unit'                  => $product['product_unit'],
                'product_tax'           => $this->calculate($product)['product_tax'],
                'unit_price'            => $this->calculate($product)['unit_price'],
                'source_type'           => 'new', // atur sesuai kebutuhan
            ],
        ]);

        $this->check_quantity[$product['id']] = $product['product_quantity'];
        $this->quantity[$product['id']]       = 1;
        $this->discount_type[$product['id']]  = 'fixed';
        $this->item_discount[$product['id']]  = 0;
        $this->total_amount                   = $this->calculateTotal();
    }

    public function removeItem($rowId)
    {
        Cart::instance($this->cart_instance)->remove($rowId);
    }

    public function updatedGlobalTax()
    {
        Cart::instance($this->cart_instance)->setGlobalTax((int)$this->global_tax);
    }

    public function updatedGlobalDiscount()
    {
        Cart::instance($this->cart_instance)->setGlobalDiscount((int)$this->global_discount);
    }

    public function updateQuantity($rowId, $productId)
    {
        if ($this->check_quantity[$productId] < $this->quantity[$productId]) {
            session()->flash('message', 'Requested quantity exceeds stock.');
            return;
        }

        $cart = Cart::instance($this->cart_instance);
        $cart->update($rowId, $this->quantity[$productId]);

        $ci = $cart->get($rowId);
        $cart->update($rowId, [
            'options' => array_merge((array)$ci->options, [
                'sub_total' => $ci->price * $ci->qty,
            ]),
        ]);
    }

    public function updatedDiscountType($value, $productId)
    {
        $this->item_discount[$productId] = 0;
    }

    public function discountModalRefresh($productId, $rowId)
    {
        $this->updateQuantity($rowId, $productId);
    }

    public function setProductDiscount($rowId, $productId)
    {
        $ci = Cart::instance($this->cart_instance)->get($rowId);
        $amount = $this->discount_type[$productId] === 'fixed'
            ? $this->item_discount[$productId]
            : ($ci->price * ($this->item_discount[$productId] / 100));

        Cart::instance($this->cart_instance)->update($rowId, [
            'price'   => ($ci->price + $ci->options->product_discount) - $amount,
            'options' => array_merge((array)$ci->options, [
                'product_discount'       => $amount,
                'product_discount_type'  => $this->discount_type[$productId],
                'sub_total'              => ($ci->price + $ci->options->product_discount - $amount) * $ci->qty,
            ]),
        ]);

        session()->flash("discount_message_{$productId}", 'Discount applied.');
    }

    public function calculate($product)
    {
        $type = $product['product_tax_type'];
        $price = $product['product_price'];
        $tax   = $price * ($product['product_order_tax']/100);

        if ($type == 1) { // inclusive
            return [
                'price'      => $price + $tax,
                'unit_price' => $price,
                'product_tax'=> $tax,
                'sub_total'  => $price + $tax,
            ];
        } elseif ($type == 2) { // exclusive
            return [
                'price'      => $price,
                'unit_price' => $price - $tax,
                'product_tax'=> $tax,
                'sub_total'  => $price,
            ];
        }

        return [
            'price'      => $price,
            'unit_price' => $price,
            'product_tax'=> 0.00,
            'sub_total'  => $price,
        ];
    }
}
