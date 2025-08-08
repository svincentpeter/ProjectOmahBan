<?php

namespace App\Livewire\Pos;

// Menggunakan path modular yang benar sesuai proyek Anda
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
// Dependency lain yang dibutuhkan
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class Checkout extends Component
{
    // Properti keranjang & data pendukung
    public $cart_instance;
    public $global_discount = 0;
    public $global_tax = 0;
    public $shipping = 0.00;
    public $quantity = [];
    public $check_quantity = [];
    public $discount_type = [];
    public $item_discount = [];
    public $total_amount;

    // Properti checkout
    public $paid_amount;
    public $payment_method = 'Tunai';
    public $bank_name;
    public $note;
    public $change = 0;

    protected $rules = [
        'payment_method'  => 'required|string|in:Tunai,Transfer,Kredit',
        'paid_amount'     => 'required|numeric|min:0',
    ];

    // Listener terpusat yang menangani semua pembaruan keranjang
    #[On('cartUpdated')]
    #[On('discountModalRefresh')]
    public function refreshCart()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();
        foreach ($cart_items as $item) {
            $itemId = $item->id;
            if (!isset($this->quantity[$itemId])) {
                $this->quantity[$itemId] = $item->qty;
            }
            if (!isset($this->check_quantity[$itemId])) {
                $this->check_quantity[$itemId] = $item->options->stock ?? 999;
            }
            if (!isset($this->discount_type[$itemId])) {
                $this->discount_type[$itemId] = 'fixed';
            }
            if (!isset($this->item_discount[$itemId])) {
                $this->item_discount[$itemId] = 0;
            }
        }
        $this->total_amount = $this->calculateTotal();
    }

    #[On('productSelected')]
    public function productSelected(array $product)
    {
        $cart = Cart::instance($this->cart_instance);
        $exists = $cart->search(fn($cartItem) => $cartItem->id == $product['id']);

        if ($exists->isNotEmpty()) {
            $this->dispatch('showWarning', ['message' => 'Produk sudah ada di keranjang!']);
            return;
        }

        $calculated_price = $this->calculate($product);

        $cart->add([
            'id'      => $product['id'],
            'name'    => $product['product_name'],
            'qty'     => 1,
            'price'   => $calculated_price['price'],
            'weight'  => 1,
            'options' => [
                'source_type'           => 'new',
                'product_discount'      => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total'             => $calculated_price['sub_total'],
                'code'                  => $product['product_code'],
                'stock'                 => $product['product_quantity'],
                'unit'                  => $product['product_unit'],
                'product_tax'           => $calculated_price['product_tax'],
                'unit_price'            => $calculated_price['unit_price'],
                'hpp'                   => $product['product_cost'],
            ],
        ]);
        
        $this->dispatch('showSuccess', ['message' => 'Produk berhasil ditambahkan!']);
        $this->refreshCart();
    }
    
    public function mount($cartInstance)
    {
        $this->cart_instance = $cartInstance;
        $this->refreshCart();
    }

    public function hydrate()
    {
        $this->total_amount = $this->calculateTotal();
        if ($this->paid_amount === null || $this->paid_amount === '') {
            $this->paid_amount = $this->total_amount;
        }
        $this->change = max(0, (float)$this->paid_amount - $this->total_amount);
    }
    
    public function updatedPaidAmount($value)
    {
        $this->change = max(0, (float)$value - $this->total_amount);
    }

    public function render()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();
        return view('livewire.pos.checkout', ['cart_items' => $cart_items]);
    }
    
    public function proceed() 
    {
        if (Cart::instance('sale')->count() == 0) {
            $this->dispatch('showWarning', ['message' => 'Keranjang masih kosong!']);
            return;
        }
        $this->dispatch('showCheckoutModal');
    }
    
    public function store()
    {
        $this->validate();
        if ($this->payment_method !== 'Tunai') {
            $this->validate(['bank_name' => 'required|string|max:255']);
        }

        try {
            $sale = DB::transaction(function () {
                $cartItems = Cart::instance('sale')->content();
                $due_amount = $this->total_amount - $this->paid_amount;
                $paymentStatus = $due_amount <= 0 ? 'Paid' : 'Partial';

                $sale = Sale::create([
                    'date' => now()->toDateString(),
                    'reference' => 'SL-' . date('Ymd-His'),
                    'tax_percentage' => $this->global_tax,
                    'tax_amount' => Cart::instance('sale')->tax(),
                    'discount_percentage' => $this->global_discount,
                    'discount_amount' => Cart::instance('sale')->discount(),
                    'shipping_amount' => $this->shipping,
                    'total_amount' => $this->total_amount,
                    'paid_amount' => $this->paid_amount,
                    'due_amount' => $due_amount,
                    'status' => 'Completed',
                    'payment_status' => $paymentStatus,
                    'payment_method' => $this->payment_method,
                    'bank_name' => ($this->payment_method !== 'Tunai') ? $this->bank_name : null,
                    'note' => $this->note,
                    'total_hpp' => 0,
                    'total_profit' => 0,
                ]);

                $totalHpp = 0;
                foreach ($cartItems as $item) {
                    $source = $item->options->source_type;
                    $hpp = $item->options->hpp ?? 0;
                    $model = null;
                    
                    if ($source === 'new') {
                        $model = Product::find($item->id);
                        if ($model) {
                            $model->decrement('product_quantity', $item->qty);
                        }
                    } elseif ($source === 'second') {
                        $model = ProductSecond::find($item->id);
                        if ($model) {
                            $model->update(['status' => 'sold']);
                        }
                    }

                    $subTotalProfit = ($item->price - $hpp) * $item->qty;
                    $totalHpp += $hpp * $item->qty;

                    SaleDetails::create([
                        'sale_id' => $sale->id,
                        'product_id' => ($source !== 'manual') ? $item->id : null,
                        'source_type' => $source,
                        'product_name' => $item->name,
                        'product_code' => $item->options->code ?? null,
                        'quantity' => $item->qty,
                        'price' => $item->price,
                        'unit_price' => $item->price,
                        'sub_total' => $item->subtotal,
                        'hpp' => $hpp,
                        'subtotal_profit' => $subTotalProfit,
                    ]);
                }

                $sale->update([
                    'total_hpp' => $totalHpp,
                    'total_profit' => $this->total_amount - $totalHpp,
                ]);

                if ($sale->paid_amount > 0) {
                    SalePayment::create([
                        'date' => now()->toDateString(),
                        'reference' => 'INV/' . $sale->reference,
                        'amount' => $sale->paid_amount,
                        'sale_id' => $sale->id,
                        'payment_method' => $this->payment_method,
                    ]);
                }
                
                return $sale;
            });
            
            Cart::instance('sale')->destroy();
            session()->flash('swal-success', 'Transaksi Berhasil Disimpan!');
            return redirect()->route('sales.pos.pdf', $sale->id);

        } catch (\Exception $e) {
            $this->dispatch('showError', ['message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
        }
    }
    
    public function calculateTotal() { return Cart::instance($this->cart_instance)->total() + $this->shipping; }
    
    public function removeItem($rowId)
    {
        Cart::instance($this->cart_instance)->remove($rowId);
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($rowId, $productId)
    {
        if ($this->check_quantity[$productId] < $this->quantity[$productId]) {
            $this->dispatch('showWarning', ['message' => 'Kuantitas melebihi stok.']);
            return;
        }
        Cart::instance($this->cart_instance)->update($rowId, $this->quantity[$productId]);
        $this->dispatch('cartUpdated');
    }

    public function setProductDiscount($rowId, $productId)
    {
        $cart = Cart::instance($this->cart_instance);
        $item = $cart->get($rowId);

        $discount_amount = ($this->discount_type[$productId] == 'fixed')
            ? $this->item_discount[$productId]
            : ($item->price * $this->item_discount[$productId] / 100);
        
        $cart->update($rowId, [
            'price' => $item->price - $discount_amount
        ]);

        session()->flash('discount_message_'. $productId, 'Diskon diterapkan!');
    }

    public function calculate($product)
    {
        $price = $product['product_price'];
        $tax = 0.00;
        $unit_price = $price;
        $sub_total = $price;

        if ($product['product_tax_type'] == 1) { // Inclusive
            $tax = $price * ($product['product_order_tax'] / 100);
            $unit_price = $price;
            $sub_total = $price + $tax;
        } elseif ($product['product_tax_type'] == 2) { // Exclusive
            $tax = $price * ($product['product_order_tax'] / 100);
            $unit_price = $price;
            $sub_total = $price;
        }

        return [
            'price'       => $price,
            'unit_price'  => $unit_price,
            'product_tax' => $tax,
            'sub_total'   => $sub_total
        ];
    }
}