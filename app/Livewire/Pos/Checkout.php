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
use App\Services\Midtrans\CreateSnapTokenService;

class Checkout extends Component
{
    // ========= Properti keranjang & data pendukung =========
    public $cart_instance;
    public $customer_name = ''; // [NEW] nama customer (opsional)
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
    public $payment_method = 'Tunai'; // Tunai | Transfer | QRIS | (Midtrans diproses terpisah)
    public $bank_name;
    public $note;
    public $change = 0;

    // Invoice (setelah dibuat)
    public $sale = null;
    public $sale_details = null;

    // ========= PROPERTIES BARU: Edit Harga =========
    public $showEditPriceModal = false;
    public $editingRowId = null;
    public $editingProductId = null;
    public $editingProductName = '';
    public $editingSourceType = '';
    public $editingOriginalPrice = 0;
    public $newPrice = 0;
    public $priceNote = '';

    // UI flag: form pembayaran muncul setelah "Lanjut ke Pembayaran"
    public bool $show_payment = false;

    public $show_midtrans_payment = false;
    public $midtrans_snap_token = null;

    protected $rules = [
        'customer_name' => 'nullable|string|max:255',
        'payment_method' => 'required|string|in:Tunai,Transfer,QRIS',
        'paid_amount' => 'nullable|numeric|min:0',
    ];

    // ========= Helpers =========
    private function cart()
    {
        return Cart::instance($this->cart_instance);
    }

    private function sanitizeMoney($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        return (int) ($clean ?: 0);
    }

    // Normalisasi metode bayar dari UI -> bentuk kanonik
    private function normalizePaymentMethod(?string $raw): array
    {
        $r = trim((string) $raw);
        switch (mb_strtolower($r)) {
            case 'tunai':
                return ['method' => 'cash', 'label' => 'Tunai'];
            case 'transfer':
                return ['method' => 'transfer', 'label' => 'Transfer'];
            case 'qris':
                return ['method' => 'qris', 'label' => 'QRIS'];
            default:
                return ['method' => 'cash', 'label' => 'Tunai'];
        }
    }

    // total angka murni (tanpa pemisah)
    private function cartTotalInt(): int
    {
        $total = $this->cart()->total(0, '', ''); // string angka
        return (int) $total + (int) $this->shipping;
    }

    // ========= Lifecycle =========
    public function mount($cartInstance = 'sale')
    {
        $this->cart_instance = $cartInstance;
        $this->total_amount = $this->cartTotalInt();
        $this->paid_amount = $this->total_amount;
        $this->calculateChange();
    }

    public function hydrate()
    {
        $effectiveTotal = $this->sale ? (int) $this->sale->total_amount : (int) $this->cartTotalInt();
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
        $this->dispatch('paid-input-ready'); // re-init input rupiah
    }

    #[On('productSelected')]
    public function productSelected($payload): void
    {
        // Menerima: int ID, {product: id}, atau array payload
        if (is_int($payload)) {
            $product = $this->buildProductPayloadFromId($payload);
        } elseif (is_array($payload)) {
            $product = isset($payload['product']) && is_int($payload['product']) ? $this->buildProductPayloadFromId($payload['product']) : $payload;
        } else {
            $this->dispatch('swal-error', 'Payload produk tidak dikenal.');
            return;
        }

        if (!isset($product['id'])) {
            $this->dispatch('swal-error', 'Produk tidak valid.');
            return;
        }

        // Cegah duplikasi
        $exists = $this->cart()->search(fn($ci) => $ci->id == $product['id']);
        if ($exists->isNotEmpty()) {
            $this->dispatch('swal-warning', 'Produk sudah ada di keranjang!');
            return;
        }

        $calculated = $this->calculate($product);

        $this->cart()->add([
            'id' => $product['id'],
            'name' => $product['product_name'] ?? 'Produk',
            'qty' => 1,
            'price' => (int) ($calculated['price'] ?? ($product['product_price'] ?? 0)),
            'weight' => 1,
            'options' => [
                'source_type' => 'new',
                'code' => $product['product_code'] ?? '-',
                'stock' => (int) ($product['product_quantity'] ?? ($product['stock'] ?? 999)),
                'unit_price' => (int) ($calculated['unit_price'] ?? ($product['product_price'] ?? 0)),
                'cost_price' => (int) ($product['product_cost'] ?? 0),
                'tax' => (int) ($calculated['product_tax'] ?? ($product['product_order_tax'] ?? 0)),
                'tax_type' => $product['product_tax_type'] ?? null,
                'discount' => (int) ($calculated['discount'] ?? 0),
                'discount_type' => 'fixed',

                // ✅ TAMBAHAN BARU: Tracking original price
                'original_price' => (int) ($product['product_price'] ?? 0), // ← PENTING!
                'is_price_adjusted' => false,
                'price_adjustment_amount' => 0,
                'price_adjustment_note' => null,
            ],
        ]);

        // Sinkron state default untuk baris baru
        $pid = $product['id'];
        $this->quantity[$pid] = 1;
        $this->check_quantity[$pid] = (int) ($product['product_quantity'] ?? ($product['stock'] ?? 999));
        $this->discount_type[$pid] = 'fixed';
        $this->item_discount[$pid] = 0;

        $this->total_amount = $this->calculateTotal();
        $this->dispatch('swal-success', 'Produk berhasil ditambahkan!');
        $this->dispatch('paid-input-ready'); // re-init AutoNumeric
    }

    private function buildProductPayloadFromId(int $id): array
    {
        $p = Product::find($id);
        if (!$p) {
            return [];
        }

        return [
            'id' => $p->id,
            'product_name' => (string) $p->product_name,
            'product_code' => (string) $p->product_code,
            'product_price' => (int) $p->product_price,
            'product_cost' => (int) ($p->product_cost ?? 0),
            'product_order_tax' => (int) ($p->product_order_tax ?? 0),
            'product_tax_type' => $p->product_tax_type ?? null,
            'product_quantity' => (int) ($p->product_quantity ?? 0),
            'source_type' => 'new',
        ];
    }

    // ========= Perhitungan =========
    public function updatedPaidAmount($value)
    {
        $this->paid_amount = $this->sanitizeMoney($value);
        $effectiveTotal = $this->sale ? (int) $this->sale->total_amount : (int) $this->cartTotalInt();
        $this->change = max(0, (int) $this->paid_amount - $effectiveTotal);
    }

    public function calculateChange()
    {
        $paid = (int) $this->sanitizeMoney($this->paid_amount);
        $effectiveTotal = $this->sale ? (int) $this->sale->total_amount : (int) $this->cartTotalInt();
        $this->change = max(0, $paid - $effectiveTotal);
    }

    public function calculate($product)
    {
        $price = (int) ($product['product_price'] ?? 0);
        $tax = 0;
        $unit_price = $price;
        $sub_total = $price;

        if (isset($product['product_tax_type']) && (int) $product['product_tax_type'] === 1) {
            // Inclusive
            $tax = (int) round(($price * ($product['product_order_tax'] ?? 0)) / 100);
            $sub_total = $price + $tax;
        } elseif (isset($product['product_tax_type']) && (int) $product['product_tax_type'] === 2) {
            // Exclusive
            $tax = (int) round(($price * ($product['product_order_tax'] ?? 0)) / 100);
            $unit_price = $price - $tax;
        }

        return [
            'price' => $price,
            'unit_price' => $unit_price,
            'product_tax' => $tax,
            'sub_total' => $sub_total,
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
        // ✅ Cek existence sebelum remove
        if (!$this->cart()->content()->has($rowId)) {
            // Item sudah tidak ada (double click atau refresh)
            $this->dispatch('cartUpdated');
            return;
        }

        try {
            $this->cart()->remove($rowId);
            $this->dispatch('cartUpdated');
            $this->dispatch('swal-success', 'Item berhasil dihapus dari keranjang.');
        } catch (\Exception $e) {
            report($e);
            $this->dispatch('swal-error', 'Gagal menghapus item.');
        }
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

        $discount_amount = ($this->discount_type[$productId] ?? 'fixed') === 'fixed' ? (int) ($this->item_discount[$productId] ?? 0) : (int) round($item->price * (($this->item_discount[$productId] ?? 0) / 100));

        $newPrice = max(0, (int) $item->price - $discount_amount);

        $original = (int) data_get($item->options, 'original_price', (int) $item->price);
        $isAdjusted = $newPrice != $original;
        $adjustAmt = $original - $newPrice;

        $cart->update($rowId, [
            'price' => $newPrice,
            'options' => array_merge($item->options->toArray(), [
                // tandai sebagai penyesuaian harga agar tervalidasi di createInvoice
                'original_price' => $original,
                'is_price_adjusted' => $isAdjusted,
                'price_adjustment_amount' => $adjustAmt,
                // note dibiarkan null bila belum diisi; akan ditolak di validasi final
            ]),
        ]);

        session()->flash('discount_message_' . $productId, 'Diskon diterapkan!');
        $this->dispatch('cartUpdated');
    }

    // ========= EDIT HARGA PER ITEM =========

    /**
     * Buka modal untuk edit harga item di cart
     */
    public function openEditPriceModal($rowId)
    {
        // Validasi item ada di cart
        if (!$this->cart()->content()->has($rowId)) {
            $this->dispatch('swal-warning', 'Item tidak ditemukan di keranjang.');
            return;
        }

        $item = $this->cart()->get($rowId);
        $sourceType = $item->options->source_type ?? 'new';

        // ⚠️ Hanya boleh edit produk "new" dan "second", tidak boleh "manual"
        if ($sourceType === 'manual') {
            $this->dispatch('swal-warning', 'Harga item manual tidak bisa diedit di keranjang.');
            return;
        }

        // Set data untuk modal
        $this->editingRowId = $rowId;
        $this->editingProductId = $item->id;
        $this->editingProductName = $item->name;
        $this->editingSourceType = $sourceType;

        // Original price: ambil dari options (sudah disimpan saat add to cart)
        // Jika belum ada (backward compat), gunakan harga current
        $this->editingOriginalPrice = $item->options->original_price ?? $item->price;

        // Harga baru default = harga saat ini
        $this->newPrice = $item->price;

        // Note: ambil existing note (jika sudah pernah diedit)
        $this->priceNote = $item->options->price_adjustment_note ?? '';

        $this->showEditPriceModal = true;
    }

    /**
     * Simpan perubahan harga
     */
    public function saveEditedPrice()
    {
        // Validasi basic
        $this->validate(
            [
                'newPrice' => 'required|numeric|min:1',
            ],
            [
                'newPrice.required' => 'Harga baru wajib diisi',
                'newPrice.numeric' => 'Harga harus berupa angka',
                'newPrice.min' => 'Harga minimal Rp 1',
            ],
        );

        $item = $this->cart()->get($this->editingRowId);
        $originalPrice = (int) $this->editingOriginalPrice;
        $newPrice = (int) $this->sanitizeMoney($this->newPrice);

        // ✅ VALIDASI WAJIB: Jika harga TURUN, catatan WAJIB
        if ($newPrice < $originalPrice) {
            $noteClean = trim($this->priceNote);
            if (empty($noteClean)) {
                $this->addError('priceNote', 'Catatan wajib diisi jika harga dikurangi dari harga asli.');
                return;
            }

            // Validasi minimal panjang catatan (opsional, sesuaikan kebutuhan)
            if (mb_strlen($noteClean) < 10) {
                $this->addError('priceNote', 'Catatan terlalu singkat. Minimal 10 karakter untuk alasan diskon.');
                return;
            }
        }

        // Hitung selisih
        $adjustmentAmount = $originalPrice - $newPrice;
        $isPriceAdjusted = $newPrice != $originalPrice;

        // Update cart item dengan harga baru + tracking data
        $this->cart()->update($this->editingRowId, [
            'price' => $newPrice, // ← Harga final yang akan ditagih
            'options' => [
                // Pertahankan options existing
                'source_type' => $item->options->source_type,
                'code' => $item->options->code ?? '-',
                'stock' => $item->options->stock ?? 999,
                'unit_price' => $item->options->unit_price ?? $newPrice,
                'cost_price' => $item->options->cost_price ?? 0,
                'tax' => $item->options->tax ?? 0,
                'tax_type' => $item->options->tax_type ?? null,
                'discount' => $item->options->discount ?? 0,
                'discount_type' => $item->options->discount_type ?? 'fixed',
                'productable_id' => $item->options->productable_id ?? null,
                'productable_type' => $item->options->productable_type ?? null,
                'manual_kind' => $item->options->manual_kind ?? null,

                // ✅ TAMBAHAN BARU: Tracking perubahan harga
                'original_price' => $originalPrice, // Tetap simpan harga asli
                'is_price_adjusted' => $isPriceAdjusted,
                'price_adjustment_amount' => $adjustmentAmount,
                'price_adjustment_note' => $isPriceAdjusted ? trim($this->priceNote) : null,
            ],
        ]);

        // Close modal & reset state
        $this->showEditPriceModal = false;
        $this->reset(['editingRowId', 'editingProductId', 'editingProductName', 'editingSourceType', 'editingOriginalPrice', 'newPrice', 'priceNote']);

        // Refresh cart total
        $this->dispatch('cartUpdated');
        $this->dispatch('swal-success', 'Harga berhasil diubah!');
    }

    /**
     * Tutup modal tanpa simpan
     */
    public function closeEditPriceModal()
    {
        $this->showEditPriceModal = false;
        $this->reset(['editingRowId', 'editingProductId', 'editingProductName', 'editingSourceType', 'editingOriginalPrice', 'newPrice', 'priceNote']);
        $this->resetErrorBag();
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

        // VALIDASI FINAL: sebelum DB::transaction
        $cartItems = $this->cart()->content();

        // Backfill original_price jika kosong (fallback aman)
        foreach ($cartItems as $ci) {
            $orig = (int) data_get($ci->options, 'original_price', 0);
            if ($orig <= 0) {
                // Fallback: set original dari price saat ini agar tidak NULL
                // Catatan: untuk second sebaiknya ambil dari master, lihat blok tambahan di bawah.
                $this->cart()->update($ci->rowId, [
                    'options' => array_merge($ci->options->toArray(), [
                        'original_price' => (int) $ci->price,
                    ]),
                ]);
            }
        }

        // Re-load setelah backfill
        $cartItems = $this->cart()->content();

        // Larang checkout jika ada item turun harga tanpa note
        $violations = [];
        foreach ($cartItems as $ci) {
            $orig = (int) data_get($ci->options, 'original_price', (int) $ci->price);
            $cur = (int) $ci->price;
            $note = trim((string) data_get($ci->options, 'price_adjustment_note', ''));
            if ($cur < $orig && $note === '') {
                $violations[] = $ci->name;
            }
        }
        if (!empty($violations)) {
            $this->dispatch('swal-error', 'Tidak bisa checkout. Catatan wajib untuk harga yang diturunkan: ' . implode(', ', $violations));
            return;
        }

        try {
            $sale = DB::transaction(function () {
                $cartItems = $this->cart()->content();
                $totalAmount = $this->total_amount ?? $this->cartTotalInt();

                // normalisasi metode bayar
                $norm = $this->normalizePaymentMethod($this->payment_method);
                $paymentLabel = $norm['label']; // "Tunai"/"Transfer"/"QRIS"
                $paymentIsBank = $norm['method'] === 'transfer'; // untuk set bank_name

                $sale = Sale::create([
                    'date' => now()->toDateString(),
                    'user_id' => auth()->id(),
                    'customer_name' => $this->customer_name ?: null,
                    'tax_percentage' => (int) $this->global_tax,
                    'tax_amount' => (int) ($this->cart()->tax() ?? 0),
                    'discount_percentage' => (int) $this->global_discount,
                    'discount_amount' => (int) ($this->cart()->discount() ?? 0),
                    'shipping_amount' => (int) $this->shipping,
                    'total_amount' => (int) $totalAmount,
                    'total_hpp' => 0,
                    'total_profit' => 0,
                    'paid_amount' => 0,
                    'due_amount' => (int) $totalAmount,
                    'status' => 'Draft',
                    'payment_status' => 'Unpaid',
                    'payment_method' => $paymentLabel, // simpan label agar kompatibel data lama
                    'bank_name' => $paymentIsBank ? ($this->bank_name ?: null) : null,
                    'note' => $this->note,
                ]);

                $totalHpp = 0;
                $totalProfit = 0;

                foreach ($cartItems as $item) {
                    $src = data_get($item->options, 'source_type', 'new'); // new|second|manual
                    $code = data_get($item->options, 'code', '-');

                    // ✅ FIXED: Baca productable dari cart options
                    $productableId = data_get($item->options, 'productable_id');
                    $productableType = data_get($item->options, 'productable_type');

                    // ✅ FIXED: Baca manual_kind dari cart options
                    $manualKind = data_get($item->options, 'manual_kind');

                    // === HPP per item (PERBAIKAN) ===
                    $hpp = 0;
                    $manualHpp = null;

                    if ($src === 'new') {
                        // Produk baru: ambil HPP dari products.product_cost
                        if ($p = Product::find($item->id)) {
                            $hpp = (int) $p->product_cost;
                        }
                    } elseif ($src === 'second') {
                        // Produk bekas dari database
                        if ($productableId) {
                            // Cari dari ProductSecond via productable_id
                            $s = ProductSecond::find($productableId);
                            $hpp = $s ? (int) ($s->purchase_price ?? 0) : (int) data_get($item->options, 'cost_price', 0);
                        } else {
                            // Fallback: baca dari cart options (tidak akan terjadi setelah fix ProductListSecond)
                            $hpp = (int) data_get($item->options, 'cost_price', 0);
                        }
                    } elseif ($src === 'manual') {
                        // ✅ FIXED: Manual items - pisahkan service vs goods
                        if ($manualKind === 'service') {
                            // Jasa: tidak ada HPP
                            $hpp = 0;
                            $manualHpp = null;
                        } elseif ($manualKind === 'goods') {
                            // Barang fisik manual: pakai manual_hpp
                            $hpp = 0; // hpp utama tetap 0
                            $manualHpp = (int) data_get($item->options, 'cost_price', 0);
                        } else {
                            // Fallback jika manual_kind tidak valid
                            $hpp = 0;
                            $manualHpp = null;
                        }
                    }

                    $qty = (int) $item->qty;
                    $unitPrice = (int) $item->price;
                    $subTotal = $qty * $unitPrice;

                    // Hitung profit
                    $totalHppItem = $hpp + ($manualHpp ?? 0);
                    $subtotalProfit = $subTotal - $totalHppItem * $qty;

                    // ✅ TAMBAHAN: Ambil data adjustment dari cart options
                    $originalPrice = (int) data_get($item->options, 'original_price', $item->price);

                    // Untuk second, upayakan original dari master jika tersedia
                    if ($src === 'second' && $productableId) {
                        if ($s = \Modules\Product\Entities\ProductSecond::find($productableId)) {
                            $originalPrice = (int) ($s->sale_price ?? $originalPrice);
                        }
                    }
                    $isPriceAdjusted = (bool) data_get($item->options, 'is_price_adjusted', false);
                    $priceAdjustmentAmount = (int) data_get($item->options, 'price_adjustment_amount', 0);
                    $priceAdjustmentNote = data_get($item->options, 'price_adjustment_note');

                    SaleDetails::create([
                        'sale_id' => $sale->id,
                        'product_id' => $src === 'new' ? (int) $item->id : null,
                        'productable_type' => $productableType,
                        'productable_id' => $productableId,
                        'source_type' => $src,
                        'manual_kind' => $manualKind,
                        'item_name' => $item->name,
                        'product_name' => $item->name,
                        'product_code' => $code,
                        'quantity' => $qty,
                        'price' => $unitPrice, // ← Harga final (setelah edit jika ada)
                        'unit_price' => $unitPrice,
                        'sub_total' => $subTotal,
                        'hpp' => (int) $hpp,
                        'manual_hpp' => $manualHpp,
                        'subtotal_profit' => (int) $subtotalProfit,
                        'product_discount_amount' => (int) data_get($item->options, 'discount', 0),
                        'product_discount_type' => data_get($item->options, 'discount_type', 'fixed'),
                        'product_tax_amount' => (int) data_get($item->options, 'tax', 0),

                        // ✅ KOLOM BARU: Tracking perubahan harga
                        'original_price' => $originalPrice,
                        'is_price_adjusted' => $isPriceAdjusted ? 1 : 0,
                        'price_adjustment_amount' => $priceAdjustmentAmount,
                        'price_adjustment_note' => $priceAdjustmentNote,
                        'adjusted_by' => $isPriceAdjusted ? auth()->id() : null,
                        'adjusted_at' => $isPriceAdjusted ? now() : null,
                    ]);

                    $totalHpp += $totalHppItem * $qty;
                    $totalProfit += (int) $subtotalProfit;
                }

                $hasPriceAdjustment = $cartItems->contains(function ($item) {
                    return (bool) data_get($item->options, 'is_price_adjusted', false);
                });

                // Update flag di tabel sales
                $sale->update([
                    'total_hpp' => $totalHpp,
                    'total_profit' => $totalProfit,
                    'has_price_adjustment' => $hasPriceAdjustment ? 1 : 0, // ← SET FLAG
                ]);

                return $sale;
            });

            $this->sale = $sale;
            $this->sale_details = SaleDetails::where('sale_id', $sale->id)->get();

            $this->cart()->destroy(); // kosongkan keranjang setelah jadi invoice

            // Default paid_amount = total invoice (mempermudah "Tandai Lunas")
            $this->paid_amount = (int) $sale->total_amount;
            $this->show_payment = false; // invoice dulu, bayar belakangan
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

    // ========= Anti "double-double" & bersih saat ganti metode =========
    public function onPaymentMethodChange(): void
    {
        // Transfer butuh bank_name, lainnya bersihkan
        if ($this->payment_method !== 'Transfer') {
            $this->bank_name = null;
        }
        // jika UI kamu punya opsi Midtrans di radio, sembunyikan panel manual
        $this->show_midtrans_payment = false;
    }

    // ========= Tandai Lunas (manual: Tunai/Transfer/QRIS) =========
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

        $this->validate(['payment_method' => 'required|in:Tunai,Transfer,QRIS']);
        if ($this->payment_method !== 'Tunai') {
            $this->validate(['bank_name' => 'required|string|max:255']);
        }
        if ($this->payment_method === 'Transfer' && empty($this->bank_name)) {
            $this->addError('bank_name', 'Nama bank wajib diisi untuk pembayaran transfer.');
            return;
        }

        $pay = $this->sanitizeMoney($this->paid_amount);
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
                $norm = $this->normalizePaymentMethod($this->payment_method);
                $pmLabel = $norm['label']; // "Tunai"/"Transfer"/"QRIS"
                $pmMethod = $norm['method']; // "cash"/"transfer"/"qris"

                // Hitung jumlah bayar yang dipakai
                $payAmount = (int) min($total, max(0, (int) $this->sanitizeMoney($this->paid_amount)));

                // === [1] Mutasi stok (sekali saja, jika belum Completed) ===
                if ($sale->status !== 'Completed') {
                    foreach ($sale->saleDetails as $d) {
                        if ($d->source_type === 'new' && $d->product_id) {
                            // kunci baris produk
                            $p = Product::whereKey($d->product_id)->lockForUpdate()->first();
                            if (!$p) {
                                throw new \RuntimeException("Produk ID {$d->product_id} tidak ditemukan.");
                            }
                            if ((int) $p->product_quantity < (int) $d->quantity) {
                                throw new \RuntimeException("Stok {$p->product_name} tidak mencukupi.");
                            }

                            // update aman
                            $affected = Product::whereKey($p->id)
                                ->where('product_quantity', '>=', (int) $d->quantity)
                                ->update(['product_quantity' => DB::raw('product_quantity - ' . (int) $d->quantity)]);
                            if ($affected < 1) {
                                throw new \RuntimeException("Gagal mengurangi stok {$p->product_name} (race).");
                            }

                            DB::table('stock_movements')->insert([
                                'productable_type' => Product::class,
                                'productable_id' => $p->id,
                                'type' => 'out',
                                'quantity' => (int) $d->quantity,
                                'description' => 'Sale #' . ($sale->reference ?? $sale->id),
                                'user_id' => auth()->id(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } elseif ($d->source_type === 'second' && $d->productable_id) {
                            // ✅ FIXED: Hanya update jika memang dari DB (ada productable_id)
                            // Item second manual (tanpa productable_id) tidak perlu update status

                            $ps = ProductSecond::whereKey($d->productable_id)->lockForUpdate()->first();
                            if (!$ps) {
                                throw new \RuntimeException("Item second ID {$d->productable_id} tidak ditemukan.");
                            }
                            if ($ps->status !== 'available') {
                                throw new \RuntimeException("Item second {$ps->unique_code} sudah terjual / tidak tersedia.");
                            }

                            $ps->status = 'sold';
                            $ps->save();

                            DB::table('stock_movements')->insert([
                                'productable_type' => ProductSecond::class,
                                'productable_id' => $ps->id,
                                'type' => 'out',
                                'quantity' => 1,
                                'description' => 'Sale (second) #' . ($sale->reference ?? $sale->id),
                                'user_id' => auth()->id(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                // === [2] Catat pembayaran ===
                SalePayment::create([
                    'date' => now()->toDateString(),
                    'reference' => 'INV/' . ($sale->reference ?? $sale->id),
                    'amount' => (int) $payAmount,
                    'sale_id' => $sale->id,
                    'payment_method' => $pmLabel,
                    'note' => $pmMethod === 'transfer' ? (string) ($this->bank_name ?? '') : null,
                ]);

                // === [3] Update status sale ===
                $sale->update([
                    'paid_amount' => (int) $payAmount,
                    'due_amount' => max(0, (int) $sale->total_amount - (int) $payAmount),
                    'status' => 'Completed',
                    'payment_status' => (int) $sale->total_amount - (int) $payAmount > 0 ? 'Partial' : 'Paid',
                    'payment_method' => $pmLabel,
                    'bank_name' => $pmMethod === 'transfer' ? $this->bank_name ?? null : null,
                ]);

                // sinkronkan instance di komponen
                $this->sale = $sale;
            });

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
        $this->sale = null;
        $this->sale_details = null;
        $this->customer_name = '';
        $this->paid_amount = null;
        $this->bank_name = null;
        $this->note = null;
        $this->change = 0;
        $this->payment_method = 'Tunai';
        $this->show_payment = false;

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

    // ========= AKSI MIDTRANS =========
    public function generateMidtransToken()
    {
        if (!$this->sale) {
            $this->dispatch('swal-error', 'Buat invoice terlebih dahulu.');
            return;
        }

        try {
            $midtrans = new CreateSnapTokenService($this->sale);
            $snapToken = $midtrans->getSnapToken();

            $this->sale->snap_token = $snapToken;
            $this->sale->save();

            $this->midtrans_snap_token = $snapToken;
            $this->show_midtrans_payment = true;

            $this->dispatch('open-midtrans-snap', ['token' => $snapToken]);
        } catch (\Exception $e) {
            $this->dispatch('swal-error', 'Gagal generate payment token: ' . $e->getMessage());
        }
    }

    public function checkMidtransStatus()
    {
        if (!$this->sale) {
            return;
        }

        $this->sale->refresh();

        if ($this->sale->payment_status === 'Paid') {
            $this->show_payment = false;
            $this->show_midtrans_payment = false;

            // konsisten dengan event swal lain
            $this->dispatch('swal-success', 'Pembayaran Midtrans berhasil.');
        }
    }
}
