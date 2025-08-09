<?php

namespace App\Livewire\Pos;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;

class Checkout extends Component
{
    // ========= Properti keranjang & data pendukung =========
    public $cart_instance;
    public $global_discount = 0;
    public $global_tax = 0;
    public $shipping = 0.00;

    public $quantity = [];
    public $check_quantity = [];
    public $discount_type = [];
    public $item_discount = [];
    public $total_amount;

    // ========= Properti checkout =========
    public $paid_amount;
    public $payment_method = 'Tunai';
    public $bank_name;
    public $note;
    public $change = 0;
    public $sale = null;
    public $sale_details = null;

    protected $rules = [
        'payment_method' => 'required|string|in:Tunai,Transfer,Kredit',
        'paid_amount'    => 'nullable|numeric|min:0',
    ];

    // ========= Listener =========
    #[On('cartUpdated')]
    #[On('discountModalRefresh')]
    public function refreshCart()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();
        foreach ($cart_items as $item) {
            $itemId = $item->id;

            if (!isset($this->quantity[$itemId]))        $this->quantity[$itemId]        = $item->qty;
            if (!isset($this->check_quantity[$itemId]))  $this->check_quantity[$itemId]  = $item->options->stock ?? 999;
            if (!isset($this->discount_type[$itemId]))   $this->discount_type[$itemId]   = 'fixed';
            if (!isset($this->item_discount[$itemId]))   $this->item_discount[$itemId]   = 0;
        }
        $this->total_amount = $this->calculateTotal();
    }

    #[On('productSelected')]
    public function productSelected(array $product)
    {
        $cart   = Cart::instance($this->cart_instance);
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

    // ========= Helper kecil =========
    private function sanitizeMoney($value): int
    {
        if (is_null($value) || $value === '') return 0;
        if (is_numeric($value)) return (int) $value;
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        return (int) ($clean ?: 0);
    }

    // PATCH: minta angka murni dari cart tanpa pemisah
    private function cartTotalInt(): int
    {
        // total($decimals, $decimalSeparator, $thousandSeparator)
        $total = Cart::instance($this->cart_instance)->total(0, '', '');
        return (int) $total + (int) $this->shipping;
    }

    // ========= Lifecycle: menjaga total & kembalian =========
    public function hydrate()
    {
        $effectiveTotal = $this->sale
            ? (int) $this->sale->total_amount
            : (int) $this->cartTotalInt(); // PATCH: gunakan helper

        $this->total_amount = $effectiveTotal;

        if ($this->paid_amount === null || $this->paid_amount === '') {
            $this->paid_amount = $effectiveTotal;
        }

        $this->change = max(0, $this->sanitizeMoney($this->paid_amount) - $effectiveTotal);
    }

    public function updatedPaidAmount($value)
    {
        $effectiveTotal = $this->sale
            ? (int) $this->sale->total_amount
            : (int) $this->cartTotalInt(); // PATCH

        $this->change = max(0, $this->sanitizeMoney($value) - $effectiveTotal);
    }

    public function render()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();
        return view('livewire.pos.checkout', ['cart_items' => $cart_items]);
    }

    // ========= UI actions =========
    public function proceed()
    {
        if (Cart::instance('sale')->count() == 0) {
            $this->dispatch('showWarning', ['message' => 'Keranjang masih kosong!']);
            return;
        }
        $this->dispatch('showCheckoutModal');
    }

    // (opsional jika dipakai alur lama)
    public function store()
    {
        $this->validate();
        if ($this->payment_method !== 'Tunai') {
            $this->validate(['bank_name' => 'required|string|max:255']);
        }

        try {
            $sale = DB::transaction(function () {
                $cartItems     = Cart::instance('sale')->content();
                $due_amount    = $this->total_amount - $this->sanitizeMoney($this->paid_amount);
                $paymentStatus = $due_amount <= 0 ? 'Paid' : 'Partial';

                $sale = Sale::create([
                    'date'                 => now()->toDateString(),
                    'reference'            => 'SL-' . date('Ymd-His'),
                    'tax_percentage'       => $this->global_tax,
                    'tax_amount'           => Cart::instance('sale')->tax(),
                    'discount_percentage'  => $this->global_discount,
                    'discount_amount'      => Cart::instance('sale')->discount(),
                    'shipping_amount'      => $this->shipping,
                    'total_amount'         => $this->total_amount,
                    'paid_amount'          => $this->sanitizeMoney($this->paid_amount),
                    'due_amount'           => max(0, $due_amount),
                    'status'               => $paymentStatus === 'Paid' ? 'Completed' : 'Draft',
                    'payment_status'       => $paymentStatus,
                    'payment_method'       => $this->payment_method ?: 'Tunai',
                    'bank_name'            => ($this->payment_method !== 'Tunai') ? $this->bank_name : null,
                    'note'                 => $this->note,
                    'total_hpp'            => 0,
                    'total_profit'         => 0,
                ]);

                $totalHpp = 0;
                foreach ($cartItems as $item) {
                    $source = $item->options->source_type;
                    $hpp    = $item->options->hpp ?? 0;

                    if ($source === 'new') {
                        if ($model = Product::find($item->id)) {
                            $model->decrement('product_quantity', $item->qty);
                        }
                    } elseif ($source === 'second') {
                        if ($model = ProductSecond::find($item->id)) {
                            $model->update(['status' => 'sold']);
                        }
                    }

                    $subTotalProfit = ($item->price - $hpp) * $item->qty;
                    $totalHpp      += $hpp * $item->qty;

                    SaleDetails::create([
                        'sale_id'         => $sale->id,
                        'product_id'      => ($source !== 'manual') ? $item->id : null,
                        'source_type'     => $source,
                        'product_name'    => $item->name,
                        'product_code'    => $item->options->code ?? null,
                        'quantity'        => $item->qty,
                        'price'           => $item->price,
                        'unit_price'      => $item->price,
                        'sub_total'       => $item->subtotal,
                        'hpp'             => $hpp,
                        'subtotal_profit' => $subTotalProfit,
                    ]);
                }

                $sale->update([
                    'total_hpp'    => $totalHpp,
                    'total_profit' => $this->total_amount - $totalHpp,
                ]);

                if ($sale->paid_amount > 0) {
                    SalePayment::create([
                        'date'           => now()->toDateString(),
                        'reference'      => 'INV/' . $sale->reference,
                        'amount'         => $sale->paid_amount,
                        'sale_id'        => $sale->id,
                        'payment_method' => $this->payment_method ?: 'Tunai',
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

    public function calculateTotal()
    {
        // PATCH: gunakan helper supaya pasti angka murni
        return $this->cartTotalInt();
    }

    public function removeItem($rowId)
    {
        Cart::instance($this->cart_instance)->remove($rowId);
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($rowId, $productId, $sourceType = 'new')
    {
        if ($sourceType === 'new') {
            $product = Product::findOrFail($productId);

            if (($this->quantity[$productId] ?? 1) > $product->product_quantity) {
                $this->dispatch('showWarning', ['message' => 'Kuantitas melebihi stok.']);
                $this->quantity[$productId] = Cart::instance('sale')->get($rowId)->qty;
                return;
            }
        }

        Cart::instance('sale')->update($rowId, $this->quantity[$productId] ?? 1);
        $this->dispatch('cartUpdated');
    }

    public function setProductDiscount($rowId, $productId)
    {
        $cart = Cart::instance($this->cart_instance);
        $item = $cart->get($rowId);

        $discount_amount = ($this->discount_type[$productId] == 'fixed')
            ? $this->item_discount[$productId]
            : ($item->price * $this->item_discount[$productId] / 100);

        $cart->update($rowId, ['price' => $item->price - $discount_amount]);
        session()->flash('discount_message_' . $productId, 'Diskon diterapkan!');
    }

    public function calculate($product)
    {
        $price      = $product['product_price'];
        $tax        = 0.00;
        $unit_price = $price;
        $sub_total  = $price;

        if (isset($product['product_tax_type']) && $product['product_tax_type'] == 1) { // Inclusive
            $tax       = $price * ($product['product_order_tax'] / 100);
            $sub_total = $price + $tax;
        } elseif (isset($product['product_tax_type']) && $product['product_tax_type'] == 2) { // Exclusive
            $tax        = $price * ($product['product_order_tax'] / 100);
            $unit_price = $price - $tax;
        }

        return [
            'price'       => $price,
            'unit_price'  => $unit_price,
            'product_tax' => $tax,
            'sub_total'   => $sub_total
        ];
    }

    // ========= CREATE INVOICE =========
    public function createInvoice()
    {
        if ($this->sale) {
            $this->dispatch('swal-warning', 'Invoice sudah dibuat. Silakan lanjut ke pembayaran.');
            return;
        }

        if (Cart::instance('sale')->count() === 0) {
            $this->dispatch('swal-error', 'Keranjang masih kosong.');
            return;
        }

        try {
            $sale = DB::transaction(function () {
                $cartItems   = Cart::instance('sale')->content();
                $totalAmount = $this->total_amount ?? $this->cartTotalInt(); // PATCH

                $paymentMethod = $this->payment_method ?: 'Tunai';
                $bankName      = $this->bank_name ?: null;

                $sale = Sale::create([
                    'date'                => now()->toDateString(),
                    'reference'           => $this->reference ?? ('SL-' . now()->format('Ymd-His')),
                    'tax_percentage'      => (int) $this->global_tax,
                    'tax_amount'          => (int) (Cart::instance('sale')->tax() ?? 0),
                    'discount_percentage' => (int) $this->global_discount,
                    'discount_amount'     => (int) (Cart::instance('sale')->discount() ?? 0),
                    'shipping_amount'     => (int) $this->shipping,
                    'total_amount'        => (int) $totalAmount,
                    'total_hpp'           => 0,
                    'total_profit'        => 0,
                    'paid_amount'         => 0,
                    'due_amount'          => (int) $totalAmount,
                    'status'              => 'Draft',
                    'payment_status'      => 'Unpaid',
                    'payment_method'      => $paymentMethod,
                    'bank_name'           => $bankName,
                    'note'                => $this->note,
                ]);

                foreach ($cartItems as $item) {
                    $source      = $item->options->source_type ?? 'new';
                    $hpp         = (float) ($item->options->hpp ?? 0);
                    $code        = $item->options->code ?? '-';
                    $discountAmt = (int) ($item->options->discount ?? 0);
                    $taxAmt      = (int) ($item->options->tax ?? 0);

                    SaleDetails::create([
                        'sale_id'                 => $sale->id,
                        'item_name'               => $item->name,
                        'product_id'              => ($source === 'new') ? $item->id : null,
                        'productable_id'          => $item->options->productable_id ?? null,
                        'productable_type'        => $item->options->productable_type ?? null,
                        'source_type'             => $source,
                        'product_name'            => $item->name,
                        'product_code'            => $code,
                        'quantity'                => (int) $item->qty,
                        'price'                   => (int) $item->price,
                        'hpp'                     => $hpp,
                        'unit_price'              => (int) $item->price,
                        'sub_total'               => (int) $item->subtotal,
                        'subtotal_profit'         => 0,
                        'product_discount_amount' => $discountAmt,
                        'product_discount_type'   => $item->options->discount_type ?? 'fixed',
                        'product_tax_amount'      => $taxAmt,
                    ]);
                }

                return $sale;
            });

            $this->sale         = $sale;
            $this->sale_details = SaleDetails::where('sale_id', $sale->id)->get();
            Cart::instance('sale')->destroy();

            $this->dispatch('swal-success', 'Invoice berhasil dibuat. Silakan proses pembayaran.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('swal-error', 'Gagal membuat invoice. ' . $e->getMessage());
        }
    }

    // ========= Tandai Lunas =========
    public function markAsPaid()
    {
        if (!$this->sale) {
            $this->dispatch('swal-error', 'Invoice belum ada. Buat invoice dulu.');
            return;
        }
        if ($this->sale->payment_status === 'Paid') {
            $this->dispatch('swal-warning', 'Transaksi ini sudah lunas.');
            return;
        }

        $this->validate(['payment_method' => 'required|in:Tunai,Transfer,Kredit']);
        if ($this->payment_method !== 'Tunai') {
            $this->validate(['bank_name' => 'required|string|max:255']);
        }

        $pay   = $this->sanitizeMoney($this->paid_amount);
        $total = (int) $this->sale->total_amount;

        if ($pay < $total) {
            $this->dispatch('swal-warning', 'Jumlah dibayar kurang dari total. Lunasi penuh untuk Tandai Lunas.');
            return;
        }

        try {
            DB::transaction(function () use ($pay, $total) {
                // LOCK baris sale
                $sale = Sale::lockForUpdate()->find($this->sale->id);

                // Catat payment (hanya sebesar total, kelebihan jadi kembalian)
                SalePayment::create([
                    'date'           => now()->toDateString(),
                    'reference'      => 'INV/' . $sale->reference,
                    'amount'         => $total,
                    'sale_id'        => $sale->id,
                    'payment_method' => $this->payment_method ?: 'Tunai',
                ]);

                // (Opsional) Kurangi stok di fase “Paid” jika belum dilakukan
                if ($sale->status !== 'Completed') {
                    foreach ($sale->details as $d) {
                        if ($d->source_type === 'new' && $d->product_id) {
                            if ($p = Product::find($d->product_id)) {
                                $p->decrement('product_quantity', $d->quantity);
                            }
                        } elseif ($d->source_type === 'second' && $d->product_id) {
                            if ($s = ProductSecond::find($d->product_id)) {
                                $s->update(['status' => 'sold']);
                            }
                        }
                    }
                }

                $sale->update([
                    'paid_amount'    => $total,
                    'due_amount'     => 0,
                    'status'         => 'Completed',
                    'payment_status' => 'Paid',
                    'payment_method' => $this->payment_method ?: 'Tunai',
                    'bank_name'      => $this->payment_method !== 'Tunai' ? $this->bank_name : null,
                ]);

                // sinkron objek $this->sale yang sudah dikunci barusan
                $this->sale = $sale;
            });

            $this->change = max(0, $pay - $total);
            $this->sale->refresh();

            $this->dispatch('swal-success', 'Pembayaran diterima. Transaksi telah dilunasi.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('swal-error', 'Gagal menandai lunas: ' . $e->getMessage());
        }
    }

    // ========= Transaksi Baru =========
    public function newTransaction()
    {
        $this->sale           = null;
        $this->sale_details   = null;
        $this->paid_amount    = null;
        $this->bank_name      = null;
        $this->note           = null;
        $this->change         = 0;
        $this->payment_method = 'Tunai';

        $this->refreshCart();
    }
}
