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

    // [A] UI flag: form pembayaran hanya muncul setelah ditekan "Lanjut ke Pembayaran"
    public bool $show_payment = false;

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

    // --- [BARU] Normalisasi metode bayar UI -> bentuk kanonik ---
    private function normalizePaymentMethod(?string $raw): array
    {
        $r = trim((string) $raw);
        switch (mb_strtolower($r)) {
            case 'tunai':     return ['method' => 'cash',     'label' => 'Tunai'];
            case 'transfer':  return ['method' => 'transfer', 'label' => 'Transfer'];
            case 'kredit':    return ['method' => 'debit',    'label' => 'Kredit']; // istilah UI kamu
            // kalau nanti tambah QRIS:
            // case 'qris':      return ['method' => 'qris',     'label' => 'QRIS'];
            default:          return ['method' => 'cash',     'label' => 'Tunai'];
        }
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
        // Jangan overwrite nilai input user
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
    public function productSelected($payload): void
    {
        // Terima 3 pola: int ID, {product: id}, atau array lengkap
        if (is_int($payload)) {
            $product = $this->buildProductPayloadFromId($payload);
        } elseif (is_array($payload)) {
            if (isset($payload['product']) && is_int($payload['product'])) {
                $product = $this->buildProductPayloadFromId($payload['product']);
            } else {
                $product = $payload; // sudah lengkap
            }
        } else {
            $this->dispatch('swal-error', 'Payload produk tidak dikenal.');
            return;
        }

        if (!isset($product['id'])) {
            $this->dispatch('swal-error', 'Produk tidak valid.');
            return;
        }

        // Cegah duplikasi
        $cart   = $this->cart();
        $exists = $cart->search(fn($ci) => $ci->id == $product['id']);
        if ($exists->isNotEmpty()) {
            $this->dispatch('swal-warning', 'Produk sudah ada di keranjang!');
            return;
        }

        // Hitung harga/tax/diskon sesuai fungsi existing
        $calculated = $this->calculate($product);

        $cart->add([
            'id'      => $product['id'],
            'name'    => $product['product_name'] ?? 'Produk',
            'qty'     => 1,
            'price'   => (int) ($calculated['price'] ?? $product['product_price'] ?? 0),
            'weight'  => 1,
            'options' => [
                'source_type'  => 'new',
                'code'         => $product['product_code'] ?? '-',
                'stock'        => (int) ($product['product_quantity'] ?? $product['stock'] ?? 999),
                'unit_price'   => (int) ($calculated['unit_price'] ?? $product['product_price'] ?? 0),
                'hpp'          => (int) ($product['product_cost'] ?? 0),
                'tax'          => (int) ($calculated['product_tax'] ?? $product['product_order_tax'] ?? 0),
                'tax_type'     => $product['product_tax_type'] ?? null,
                'discount'     => (int) ($calculated['discount'] ?? 0),
            ],
        ]);

        // Sinkronkan state array agar baris baru kenal default-nya
        $pid = $product['id'];
        $this->quantity[$pid]        = 1;
        $this->check_quantity[$pid]  = (int) ($product['product_quantity'] ?? $product['stock'] ?? 999);
        $this->discount_type[$pid]   = 'fixed';
        $this->item_discount[$pid]   = 0;

        $this->total_amount = $this->calculateTotal();
        $this->dispatch('swal-success', 'Produk berhasil ditambahkan!');
        $this->dispatch('paid-input-ready'); // re-init AutoNumeric
    }

    /**
     * Ambil Product dari DB dan susun payload standar untuk perhitungan
     */
    private function buildProductPayloadFromId(int $id): array
    {
        $p = Product::find($id);
        if (!$p) return [];

        return [
            'id'                => $p->id,
            'product_name'      => (string) $p->product_name,
            'product_code'      => (string) $p->product_code,
            'product_price'     => (int) $p->product_price,
            'product_cost'      => (int) ($p->product_cost ?? 0),
            'product_order_tax' => (int) ($p->product_order_tax ?? 0),
            'product_tax_type'  => $p->product_tax_type ?? null,
            'product_quantity'  => (int) ($p->product_quantity ?? 0),
            'source_type'       => 'new',
        ];
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
            $this->dispatch('swal-warning', 'Keranjang masih kosong!');
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
                $this->dispatch('swal-warning', 'Kuantitas melebihi stok.');
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

                // normalisasi metode bayar (UI -> kanonik + label)
                $norm          = $this->normalizePaymentMethod($this->payment_method);
                $paymentMethod = $norm['label']; // simpan label UI agar kompatibel dengan data lama
                $bankName      = $norm['method'] === 'transfer' ? ($this->bank_name ?: null) : null;

                $sale = Sale::create([
                    'date'                => now()->toDateString(),
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

                $totalHpp    = 0;
                $totalProfit = 0;

                foreach ($cartItems as $item) {
                    $src  = data_get($item->options, 'source_type', 'new');
                    $code = data_get($item->options, 'code', $src === 'manual' ? '-' : '-');

                    $originalId = (int) data_get($item->options, 'original_id', $item->id);

                    // HPP
                    $hpp = 0;
                    if ($src === 'new') {
                        if ($p = Product::find($originalId)) {
                            $hpp = (int) $p->product_cost;
                        }
                    } elseif ($src === 'second') {
                        if ($s = ProductSecond::find($originalId)) {
                            $hpp = (int) ($s->purchase_price ?? 0);
                        }
                    }

                    $qty       = (int) $item->qty;
                    $unitPrice = (int) $item->price;
                    $subTotal  = $qty * $unitPrice;

                    SaleDetails::create([
                        'sale_id'                 => $sale->id,
                        'product_id'              => $src === 'new' ? $originalId : null,
                        'productable_type'        => $src === 'second' ? ProductSecond::class : null,
                        'productable_id'          => $src === 'second' ? $originalId : null,
                        'source_type'             => $src,
                        'item_name'               => $item->name,
                        'product_name'            => $item->name,
                        'product_code'            => $code,
                        'quantity'                => $qty,
                        'price'                   => $unitPrice,
                        'unit_price'              => $unitPrice,
                        'sub_total'               => $subTotal,
                        'hpp'                     => $hpp,
                        'subtotal_profit'         => max(0, ($unitPrice - $hpp) * $qty),
                        'product_discount_amount' => (int) data_get($item->options, 'discount', 0),
                        'product_discount_type'   => data_get($item->options, 'discount_type', 'fixed'),
                        'product_tax_amount'      => (int) data_get($item->options, 'tax', 0),
                    ]);

                    $totalHpp    += $hpp * $qty;
                    $totalProfit += max(0, ($unitPrice - $hpp) * $qty);
                }

                $sale->update([
                    'total_hpp'    => $totalHpp,
                    'total_profit' => $totalProfit,
                ]);

                return $sale;
            });

            $this->sale         = $sale;
            $this->sale_details = SaleDetails::where('sale_id', $sale->id)->get();

            $this->cart()->destroy(); // kosongkan keranjang setelah jadi invoice

            // Default paid_amount = total invoice (mempermudah "Tandai Lunas")
            $this->paid_amount   = (int) $sale->total_amount;
            $this->show_payment  = false; // invoice dulu, bayar belakangan
            $this->calculateChange();

            $this->dispatch('paid-input-ready');
            $this->dispatch('swal-success', 'Invoice berhasil dibuat. Silakan cetak & lanjut ke pembayaran.');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('swal-error', 'Gagal membuat invoice. ' . $e->getMessage());
        }
    }

    // ========= Lanjut ke Pembayaran (tampilkan form) =========
    public function showPayment(): void
    {
        if (!$this->sale) {
            $this->dispatch('swal-warning', 'Buat invoice dulu.');
            return;
        }
        if ($this->sale->payment_status === 'Paid') {
            $this->dispatch('swal-warning', 'Invoice ini sudah lunas.');
            return;
        }

        $this->show_payment = true;
        $this->dispatch('paid-input-ready'); // init AutoNumeric saat form muncul
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
        if ($this->payment_method === 'Transfer' && empty($this->bank_name)) {
            $this->addError('bank_name', 'Nama bank wajib diisi untuk pembayaran transfer.');
            return;
        }

        $pay   = $this->sanitizeMoney($this->paid_amount);
        $total = (int) $this->sale->total_amount;

        if ($pay < $total) {
            $this->dispatch('swal-warning', 'Jumlah dibayar kurang dari total. Lunasi penuh untuk Tandai Lunas.');
            return;
        }

        try {
            DB::transaction(function () use ($total) {
                // LOCK baris sale + ambil detail
                $sale = Sale::lockForUpdate()->with('saleDetails')->find($this->sale->id);
                if (!$sale) {
                    throw new \RuntimeException('Invoice tidak ditemukan/terkunci.');
                }

                // Normalisasi metode bayar
                $norm    = $this->normalizePaymentMethod($this->payment_method);
                $pmLabel = $norm['label'];   // "Tunai"/"Transfer"/"Kredit"
                $pmMethod= $norm['method'];  // "cash"/"transfer"/"debit"

                // Hitung jumlah bayar yang dipakai
                $payAmount = (int) min($total, max(0, (int)$this->sanitizeMoney($this->paid_amount)));

                // === [1] Mutasi stok dengan LOCK & GUARD ===
                // Hanya jika status belum Completed (hindari double-decrement)
                if ($sale->status !== 'Completed') {
                    foreach ($sale->saleDetails as $d) {
                        if ($d->source_type === 'new' && $d->product_id) {
                            // Kunci baris produk
                            $p = Product::whereKey($d->product_id)->lockForUpdate()->first();
                            if (!$p) {
                                throw new \RuntimeException("Produk ID {$d->product_id} tidak ditemukan.");
                            }
                            if ((int)$p->product_quantity < (int)$d->quantity) {
                                throw new \RuntimeException("Stok {$p->product_name} tidak mencukupi.");
                            }

                            // GUARD: hanya kurangi jika stok cukup (hindari race)
                            $affected = Product::whereKey($p->id)
                                ->where('product_quantity', '>=', (int)$d->quantity)
                                ->update([
                                    'product_quantity' => DB::raw('product_quantity - ' . (int)$d->quantity)
                                ]);

                            if ($affected < 1) {
                                throw new \RuntimeException("Gagal mengurangi stok {$p->product_name} (race condition).");
                            }

                            // Catat movement (keluar)
                            DB::table('stock_movements')->insert([
                                'productable_type' => Product::class,
                                'productable_id'   => $p->id,
                                'type'             => 'out',
                                'quantity'         => (int) $d->quantity,
                                'description'      => 'Sale #' . ($sale->reference ?? $sale->id),
                                'user_id'          => auth()->id(),
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ]);
                        }
                        elseif ($d->source_type === 'second' && $d->productable_id) {
                            // Kunci baris second item
                            $ps = ProductSecond::whereKey($d->productable_id)->lockForUpdate()->first();
                            if (!$ps) {
                                throw new \RuntimeException("Item second ID {$d->productable_id} tidak ditemukan.");
                            }
                            if ($ps->status !== 'available') {
                                throw new \RuntimeException("Item second {$ps->unique_code} sudah terjual / tidak tersedia.");
                            }

                            $ps->status = 'sold';
                            $ps->save();

                            // Catat movement second
                            DB::table('stock_movements')->insert([
                                'productable_type' => ProductSecond::class,
                                'productable_id'   => $ps->id,
                                'type'             => 'out',
                                'quantity'         => 1,
                                'description'      => 'Sale (second) #' . ($sale->reference ?? $sale->id),
                                'user_id'          => auth()->id(),
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ]);
                        }
                        // manual/jasa: tidak mengubah stok fisik
                    }
                }

                // === [2] Catat pembayaran ===
                SalePayment::create([
                    'date'           => now()->toDateString(),
                    'reference'      => 'INV/' . ($sale->reference ?? $sale->id),
                    'amount'         => (int) $payAmount,
                    'sale_id'        => $sale->id,
                    'payment_method' => $pmLabel, // simpan label (kompatibel dengan data lama)
                    // NOTE: saat ini kamu menaruh nama bank di kolom 'note'.
                    // Jika sudah ada kolom bank_name di sale_payments, tambahkan di sini.
                    'note'           => $pmMethod === 'transfer' ? (string) ($this->bank_name ?? '') : null,
                ]);

                // === [3] Update status sale ===
                $sale->update([
                    'paid_amount'    => (int) $payAmount,
                    'due_amount'     => max(0, (int)$sale->total_amount - (int)$payAmount),
                    'status'         => 'Completed',
                    'payment_status' => ((int)$sale->total_amount - (int)$payAmount) > 0 ? 'Partial' : 'Paid',
                    'payment_method' => $pmLabel,
                    'bank_name'      => $pmMethod === 'transfer' ? ($this->bank_name ?? null) : null,
                ]);

                // sinkronkan instance di komponen
                $this->sale = $sale;
            });

            // kembalian dihitung client-side (this->change sudah di-set saat updatedPaidAmount/hydrate)
            $this->sale->refresh();
            $this->show_payment = false; // sembunyikan form setelah lunas
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
        $this->show_payment   = false;

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
