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
    public $shipping = 0;

    public $quantity = [];
    public $check_quantity = [];
    public $discount_type = [];
    public $item_discount = [];
    public $total_amount;

    // ========= Properti checkout =========
    public $paid_amount;
    public $payment_method = 'Tunai'; // Tunai | Transfer | Kredit
    public $bank_name;
    public $note;
    public $change = 0;

    // Invoice (setelah dibuat)
    public $sale = null;
    public $sale_details = null;

    protected $rules = [
        'payment_method' => 'required|string|in:Tunai,Transfer,Kredit',
        'paid_amount'    => 'nullable|numeric|min:0',
    ];

    // ========= Helpers =========
    private function cart()
    {
        return Cart::instance($this->cart_instance);
    }

    private function sanitizeMoney($value): int
    {
        if (is_null($value) || $value === '') return 0;
        if (is_numeric($value)) return (int) $value;
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        return (int) ($clean ?: 0);
    }

    // total angka murni (tanpa pemisah)
    private function cartTotalInt(): int
    {
        // total($decimals, $decimalSeparator, $thousandSeparator)
        $total = $this->cart()->total(0, '', '');
        return (int) $total + (int) $this->shipping;
    }

    // ========= Lifecycle =========
    public function mount($cartInstance = 'sale')
    {
        $this->cart_instance = $cartInstance;
        $this->total_amount  = $this->cartTotalInt();
        $this->paid_amount   = $this->total_amount;
        $this->calculateChange();
    }

    public function hydrate()
{
    $effectiveTotal = $this->sale
        ? (int) $this->sale->total_amount
        : (int) $this->cartTotalInt();

    $this->total_amount = $effectiveTotal;

    // ❌ Jangan set default paid_amount di sini.
    // ✅ Cukup hitung ulang kembalian dari nilai yang ada sekarang.
    $this->change = max(0, $this->sanitizeMoney($this->paid_amount) - $effectiveTotal);
}



    // ========= Listener =========
    #[On('cartUpdated')]
    #[On('discountModalRefresh')]
    public function refreshCart()
    {
        $cart_items = $this->cart()->content();
        foreach ($cart_items as $item) {
            $itemId = $item->id;

            if (!isset($this->quantity[$itemId]))        $this->quantity[$itemId]        = $item->qty;
            if (!isset($this->check_quantity[$itemId]))  $this->check_quantity[$itemId]  = $item->options->stock ?? 999;
            if (!isset($this->discount_type[$itemId]))   $this->discount_type[$itemId]   = 'fixed';
            if (!isset($this->item_discount[$itemId]))   $this->item_discount[$itemId]   = 0;
        }

        $this->total_amount = $this->calculateTotal();
        $this->dispatch('paid-input-ready'); // biar input rupiah re-init
    }

    #[On('productSelected')]
    public function productSelected(array $product)
    {
        $cart   = $this->cart();
        $exists = $cart->search(fn($cartItem) => $cartItem->id == $product['id']);

        if ($exists->isNotEmpty()) {
            $this->dispatch('showWarning', ['message' => 'Produk sudah ada di keranjang!']);
            return;
        }

        $calculated = $this->calculate($product);

        $cart->add([
            'id'      => $product['id'],
            'name'    => $product['product_name'],
            'qty'     => 1,
            'price'   => $calculated['price'],
            'weight'  => 1,
            'options' => [
                'source_type'           => 'new', // new | second | manual
                'product_discount'      => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total'             => $calculated['sub_total'],
                'code'                  => $product['product_code'] ?? null,
                'stock'                 => $product['product_quantity'] ?? 0,
                'unit'                  => $product['product_unit'] ?? null,
                'product_tax'           => $calculated['product_tax'],
                'unit_price'            => $calculated['unit_price'],
                'hpp'                   => $product['product_cost'] ?? 0,
            ],
        ]);

        $this->dispatch('showSuccess', ['message' => 'Produk berhasil ditambahkan!']);
        $this->refreshCart();
    }

    // ========= Perhitungan =========
    public function updatedPaidAmount($value)
    {
        $this->paid_amount = $this->sanitizeMoney($value);

        $effectiveTotal = $this->sale
            ? (int) $this->sale->total_amount
            : (int) $this->cartTotalInt();

        $this->change = max(0, (int) $this->paid_amount - $effectiveTotal);
    }

    public function calculateChange()
    {
        $paid = (int) $this->sanitizeMoney($this->paid_amount);
        $effectiveTotal = $this->sale
            ? (int) $this->sale->total_amount
            : (int) $this->cartTotalInt();

        $this->change = max(0, $paid - $effectiveTotal);
    }

    public function calculate($product)
    {
        $price      = (int) ($product['product_price'] ?? 0);
        $tax        = 0;
        $unit_price = $price;
        $sub_total  = $price;

        if (isset($product['product_tax_type']) && (int)$product['product_tax_type'] === 1) { // Inclusive
            $tax       = (int) round($price * ($product['product_order_tax'] ?? 0) / 100);
            $sub_total = $price + $tax;
        } elseif (isset($product['product_tax_type']) && (int)$product['product_tax_type'] === 2) { // Exclusive
            $tax        = (int) round($price * ($product['product_order_tax'] ?? 0) / 100);
            $unit_price = $price - $tax;
        }

        return [
            'price'       => $price,
            'unit_price'  => $unit_price,
            'product_tax' => $tax,
            'sub_total'   => $sub_total
        ];
    }

    public function calculateTotal()
    {
        return $this->cartTotalInt();
    }

    // ========= Aksi UI sebelum invoice =========
    public function proceed()
    {
        if ($this->cart()->count() === 0) {
            $this->dispatch('showWarning', ['message' => 'Keranjang masih kosong!']);
            return;
        }
        $this->dispatch('showCheckoutModal');
    }

    public function removeItem($rowId)
    {
        $this->cart()->remove($rowId);
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($rowId, $productId, $sourceType = 'new')
    {
        if ($sourceType === 'new') {
            $product = Product::findOrFail($productId);
            if (($this->quantity[$productId] ?? 1) > $product->product_quantity) {
                $this->dispatch('showWarning', ['message' => 'Kuantitas melebihi stok.']);
                $this->quantity[$productId] = $this->cart()->get($rowId)->qty;
                return;
            }
        }

        $this->cart()->update($rowId, $this->quantity[$productId] ?? 1);
        $this->dispatch('cartUpdated');
    }

    public function setProductDiscount($rowId, $productId)
    {
        $cart = $this->cart();
        $item = $cart->get($rowId);

        $discount_amount = ($this->discount_type[$productId] ?? 'fixed') === 'fixed'
            ? (int) ($this->item_discount[$productId] ?? 0)
            : (int) round($item->price * (($this->item_discount[$productId] ?? 0) / 100));

        $newPrice = max(0, (int) $item->price - $discount_amount);
        $cart->update($rowId, ['price' => $newPrice]);
        session()->flash('discount_message_' . $productId, 'Diskon diterapkan!');
        $this->dispatch('cartUpdated');
    }

    // ========= CREATE INVOICE =========
    public function createInvoice()
    {
        if ($this->sale) {
            $this->dispatch('swal-warning', 'Invoice sudah dibuat. Silakan lanjut ke pembayaran.');
            return;
        }

        if ($this->cart()->count() === 0) {
            $this->dispatch('swal-error', 'Keranjang masih kosong.');
            return;
        }

        try {
            $sale = DB::transaction(function () {
                $cartItems   = $this->cart()->content();
                $totalAmount = $this->total_amount ?? $this->cartTotalInt();

                $paymentMethod = $this->payment_method ?: 'Tunai';
                $bankName      = $this->payment_method !== 'Tunai' ? ($this->bank_name ?: null) : null;

                $sale = Sale::create([
                    'date'                => now()->toDateString(),
                    'reference'           => $this->reference ?? ('SL-' . now()->format('Ymd-His')),
                    'user_id'             => auth()->id(),
                    'tax_percentage'      => (int) $this->global_tax,
                    'tax_amount'          => (int) ($this->cart()->tax() ?? 0),
                    'discount_percentage' => (int) $this->global_discount,
                    'discount_amount'     => (int) ($this->cart()->discount() ?? 0),
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
                    $code        = $item->options->code ?? null;
                    $discountAmt = (int) ($item->options->discount ?? 0);
                    $taxAmt      = (int) ($item->options->tax ?? 0);

                    SaleDetails::create([
                        'sale_id'                 => $sale->id,
                        'product_id'              => ($source !== 'manual') ? $item->id : null,
                        'source_type'             => $source,
                        'item_name'               => $item->name,          // <= PENTING
                        'product_name'            => $item->name,
                        'product_code'            => $code ?? '-',
                        'quantity'                => (int) $item->qty,
                        'price'                   => (int) $item->price,
                        'unit_price'              => (int) $item->price,
                        'sub_total'               => (int) $item->subtotal,
                        'hpp'                     => $hpp,
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

            $this->cart()->destroy(); // kosongkan keranjang setelah jadi invoice

            // Default paid_amount = total invoice (mempermudah "Tandai Lunas")
            $this->paid_amount = (int) $sale->total_amount;
            $this->calculateChange();

            $this->dispatch('paid-input-ready');
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
            DB::transaction(function () use ($total) {
                // LOCK baris sale
                $sale = Sale::lockForUpdate()->with('saleDetails')->find($this->sale->id);

                // Catat payment (sebesar total)
                SalePayment::create([
                    'date'           => now()->toDateString(),
                    'reference'      => 'INV/' . $sale->reference,
                    'amount'         => $total,
                    'sale_id'        => $sale->id,
                    'payment_method' => $this->payment_method ?: 'Tunai',
                ]);

                // Kurangi stok saat status naik ke Completed (jika belum)
                if ($sale->status !== 'Completed') {
                    foreach ($sale->saleDetails as $d) {
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

                // sinkronkan properti
                $this->sale = $sale;
            });

            // kembalian dihitung client-side (this->change sudah di-set saat updatedPaidAmount/hydrate)
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
        $this->dispatch('paid-input-ready');
    }

    // ========= Render =========
    public function render()
    {
        $cart_items = $this->cart()->content();
        // Aman dipanggil tiap render (idempoten di JS)
        $this->dispatch('paid-input-ready');

        return view('livewire.pos.checkout', ['cart_items' => $cart_items]);
    }
}
