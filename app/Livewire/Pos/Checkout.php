<?php

namespace App\Livewire\Pos;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;
use Modules\Product\Entities\ServiceMaster;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\Sale\Entities\ManualInputLog;
use App\Services\Midtrans\CreateSnapTokenService;
use App\Events\ManualInputCreated;
use App\Models\ManualInputDetail;

class Checkout extends Component
{
    // ========= Properti keranjang & data pendukung =========
    public $cart_instance;
    public $customer_name = '';
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
    public $payment_method = 'Tunai'; // Tunai | Transfer | QRIS | Midtrans (UI)
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

    // public $newPrice = 0; // ❌ tidak dipakai lagi
    public $discountAmount = 0; // ✅ nominal POTONGAN (Rp)
    public $priceNote = '';
    // UI flag: form pembayaran muncul setelah "Lanjut ke Pembayaran"
    public bool $show_payment = false;

    public $show_midtrans_payment = false;
    public $midtrans_snap_token = null;

    protected $rules = [
        'customer_name' => 'nullable|string|max:255',
        'payment_method' => 'required|string|in:Tunai,Transfer,QRIS,Midtrans',
        'paid_amount' => 'nullable|numeric|min:0',
    ];

    // ========= Helpers =========

    /**
     * Deteksi apakah item di cart merupakan manual input (termasuk jasa ServiceMaster).
     * Meng-cover:
     * - source_type: manual | service_master
     * - manual_kind: service
     * - code berawalan SRV-
     * - productable_type = ServiceMaster
     * - flag options.is_manual_input = true
     */
    private function isManualInput($cartItem): bool
    {
        $src = (string) data_get($cartItem->options, 'source_type');
        $mkind = (string) data_get($cartItem->options, 'manual_kind');
        $code = (string) data_get($cartItem->options, 'code', '');
        $ptype = (string) data_get($cartItem->options, 'productable_type');

        $serviceTypes = [ServiceMaster::class, 'Modules\Product\Entities\ServiceMaster'];

        return in_array($src, ['manual', 'service_master'], true) || $mkind === 'service' || Str::startsWith($code, 'SRV-') || in_array($ptype, $serviceTypes, true) || (bool) data_get($cartItem->options, 'is_manual_input', false);
    }

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
        $clean = preg_replace('/[^\d-]/', '', (string) $value);
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
            case 'midtrans':
                return ['method' => 'midtrans', 'label' => 'Midtrans'];
            default:
                return ['method' => 'cash', 'label' => 'Tunai'];
        }
    }

    // di class Checkout
    protected array $rolesCanAdjust = ['Admin', 'Super Admin', 'Owner', 'Supervisor', 'Kasir'];

    protected function userCanAdjust(): bool
    {
        $u = auth()->user();
        return $u?->hasAnyRole($this->rolesCanAdjust) ?? false;
    }

    // total angka murni (tanpa pemisah)
    private function cartTotalInt(): int
    {
        $totalStr = $this->cart()->total(0, '', ''); // "12345"
        $total = (int) $totalStr;

        $shipping = (int) $this->shipping;
        if ($shipping < 0) {
            $shipping = 0; // hardening kecil
        }

        return $total + $shipping;
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
                // hormati source_type dari payload bila ada
                'source_type' => $product['source_type'] ?? 'new',
                'code' => $product['product_code'] ?? '-',
                'stock' => (int) ($product['product_quantity'] ?? ($product['stock'] ?? 999)),
                'unit_price' => (int) ($calculated['unit_price'] ?? ($product['product_price'] ?? 0)),
                'cost_price' => (int) ($product['product_cost'] ?? 0),
                'tax' => (int) ($calculated['product_tax'] ?? ($product['product_order_tax'] ?? 0)),
                'tax_type' => $product['product_tax_type'] ?? null,
                'discount' => (int) ($calculated['discount'] ?? 0),
                'discount_type' => 'fixed',

                // ✅ Tracking original price
                'original_price' => (int) ($product['product_price'] ?? 0),
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
        $rate = (int) ($product['product_order_tax'] ?? 0);
        $type = (int) ($product['product_tax_type'] ?? 0); // 0: none, 1: inclusive, 2: exclusive

        $tax = 0;
        $unit_price = $price;
        $sub_total = $price;

        if ($rate > 0) {
            if ($type === 1) {
                // Inclusive
                $tax = (int) round($price * ($rate / (100 + $rate)));
                $unit_price = $price - $tax;
                $sub_total = $price;
            } elseif ($type === 2) {
                // Exclusive
                $tax = (int) round($price * ($rate / 100));
                $unit_price = $price;
                $sub_total = $price + $tax;
            }
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
    }

    public function removeItem($rowId)
    {
        if (!$this->cart()->content()->has($rowId)) {
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
        $newQty = max(1, (int) ($this->quantity[$productId] ?? 1));

        if ($sourceType === 'new') {
            $product = Product::find($productId);
            if ($product && $newQty > $product->product_quantity) {
                $this->dispatch('swal-warning', 'Kuantitas melebihi stok.');
                $item = Cart::instance($this->cart_instance)->get($rowId);
                $this->quantity[$productId] = $item ? $item->qty : 1;
                return;
            }
        } elseif ($sourceType === 'second') {
            $this->dispatch('swal-warning', 'Qty item bekas tidak dapat diubah.');
            $item = Cart::instance($this->cart_instance)->get($rowId);
            $this->quantity[$productId] = $item ? $item->qty : 1;
            return;
        }

        Cart::instance($this->cart_instance)->update($rowId, ['qty' => $newQty]);

        $this->total_amount = $this->cartTotalInt();
        $this->calculateChange();

        $this->dispatch('cartUpdated');
        $this->dispatch('swal-success', 'Qty berhasil diupdate.');
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
                'original_price' => $original,
                'is_price_adjusted' => $isAdjusted,
                'price_adjustment_amount' => $adjustAmt,
            ]),
        ]);

        session()->flash('discount_message_' . $productId, 'Diskon diterapkan!');
        $this->dispatch('cartUpdated');
    }

    // ========= EDIT HARGA PER ITEM =========

    public function openEditPriceModal($rowId)
    {
        if (!$this->userCanAdjust()) {
            $this->dispatch('swal-error', 'Anda tidak berhak mengubah harga.');
            return;
        }

        if (!$this->cart()->content()->has($rowId)) {
            $this->dispatch('swal-warning', 'Item tidak ditemukan di keranjang.');
            return;
        }

        $item = $this->cart()->get($rowId);
        $sourceType = $item->options->source_type ?? 'new';

        if ($sourceType === 'manual') {
            $this->dispatch('swal-warning', 'Harga item manual tidak bisa diedit di keranjang.');
            return;
        }

        $this->editingRowId = $rowId;
        $this->editingProductId = $item->id;
        $this->editingProductName = $item->name;
        $this->editingSourceType = $sourceType;

        // Harga asli → dari options.original_price kalau ada, fallback harga saat ini
        $this->editingOriginalPrice = (int) ($item->options->original_price ?? $item->price);

        // Nilai default form (persist jika sudah pernah disesuaikan)
        $this->discountAmount = (int) ($item->options->price_adjustment_amount ?? 0);
        $this->priceNote = (string) ($item->options->price_adjustment_note ?? '');

        $this->showEditPriceModal = true;
    }

    public function saveEditedPrice()
    {
        // 1) Permission
        if (!$this->userCanAdjust()) {
            $this->addError('discountAmount', 'Anda tidak berhak mengubah harga.');
            return;
        }
        // 2) Ambil angka & hitung harga baru
        $orig = (int) $this->editingOriginalPrice;
        if ($orig < 1) {
            $this->addError('discountAmount', 'Harga asli tidak valid.');
            return;
        }

        $disc = (int) $this->sanitizeMoney($this->discountAmount);
        $new = max(0, $orig - $disc);

        // 3) Validasi
        $this->validate(
            [
                'discountAmount' => ['required', 'integer', 'min:0', 'lte:' . $orig],
                'priceNote' => [$disc > 0 ? 'required' : 'nullable', 'string', $disc > 0 ? 'min:10' : 'min:0'],
            ],
            [
                'discountAmount.lte' => 'Pengurangan tidak boleh melebihi harga asli.',
                'priceNote.required' => 'Alasan wajib diisi saat ada potongan.',
                'priceNote.min' => 'Alasan minimal 10 karakter.',
            ],
        );

        // (Opsional) kalau ingin melarang harga 0, pertahankan guard ini.
        if ($new < 1) {
            $this->addError('discountAmount', 'Potongan terlalu besar. Harga setelah potong minimal Rp 1.');
            return;
        }

        // 4) Update cart
        $item = $this->cart()->get($this->editingRowId);
        if (!$item) {
            $this->dispatch('swal-error', 'Item tidak ditemukan.');
            return;
        }

        $options = $item->options->toArray();
        $options['unit_price'] = $options['unit_price'] ?? $new;
        $options['original_price'] = $orig;
        $options['is_price_adjusted'] = $disc > 0;
        $options['price_adjustment_amount'] = $disc;
        $options['price_adjustment_note'] = $disc > 0 ? trim((string) $this->priceNote) : null;
        $options['price_adjustment_source'] = 'manual_discount';
        $options['price_adjusted_at'] = now()->toDateTimeString();

        $this->cart()->update($this->editingRowId, [
            'price' => $new,
            'options' => $options,
        ]);

        // 5) (Opsional) Notifikasi owner jika melewati threshold
        $pct = $orig > 0 ? ($disc / $orig) * 100 : 0;
        if ($pct >= 10) {
            // panggil service/event notifikasi owner di sini
            // contoh:
            // event(new SignificantPriceAdjustment(auth()->user(), $item->id, $orig, $disc, $new, $this->priceNote));
        }

        // 6) Bereskan state & refresh UI
        $this->showEditPriceModal = false;
        $this->reset(['editingRowId', 'editingProductId', 'editingProductName', 'editingSourceType', 'editingOriginalPrice', 'discountAmount', 'priceNote']);

        $this->dispatch('cartUpdated');
        $this->dispatch('swal-success', 'Harga berhasil disesuaikan!');
    }

    public function closeEditPriceModal()
    {
        $this->showEditPriceModal = false;
        $this->reset([
            'editingRowId',
            'editingProductId',
            'editingProductName',
            'editingSourceType',
            'editingOriginalPrice',
            // 'newPrice', // ❌
            'discountAmount', // ✅
            'priceNote',
        ]);
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

        Log::info('Checkout:createInvoice invoked', [
            'cart_count' => $this->cart()->count(),
            'payment_method' => $this->payment_method,
            'user_id' => auth()->id(),
        ]);

        // VALIDASI FINAL: sebelum DB::transaction
        $cartItems = $this->cart()->content();

        // Backfill original_price jika kosong
        foreach ($cartItems as $ci) {
            $orig = (int) data_get($ci->options, 'original_price', 0);
            if ($orig <= 0) {
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
            // Siapkan kolektor manual items untuk event
            $manualItems = [];
            $hasManualInput = false;

            $sale = DB::transaction(function () use (&$manualItems, &$hasManualInput) {
                $cartItems = $this->cart()->content();
                $totalAmount = $this->total_amount ?? $this->cartTotalInt();

                $norm = $this->normalizePaymentMethod($this->payment_method);
                $paymentLabel = $norm['label'];
                $paymentIsBank = $norm['method'] === 'transfer';

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
                    'payment_method' => $paymentLabel,
                    'bank_name' => $paymentIsBank ? ($this->bank_name ?: null) : null,
                    'note' => $this->note,
                ]);

                $totalHpp = 0;
                $totalProfit = 0;

                foreach ($cartItems as $item) {
                    $src = data_get($item->options, 'source_type', 'new'); // new|second|manual|service_master
                    $code = data_get($item->options, 'code', '-');

                    $productableId = data_get($item->options, 'productable_id');
                    $productableType = data_get($item->options, 'productable_type');
                    $manualKind = data_get($item->options, 'manual_kind');

                    // HPP
                    $hpp = 0;
                    $manualHpp = null;

                    if ($src === 'new') {
                        if ($p = Product::find($item->id)) {
                            $hpp = (int) $p->product_cost;
                        }
                    } elseif ($src === 'second') {
                        if ($productableId) {
                            $s = ProductSecond::find($productableId);
                            $hpp = $s ? (int) ($s->purchase_price ?? 0) : (int) data_get($item->options, 'cost_price', 0);
                        } else {
                            $hpp = (int) data_get($item->options, 'cost_price', 0);
                        }
                    } elseif ($src === 'manual') {
                        if ($manualKind === 'service') {
                            $hpp = 0;
                            $manualHpp = null;
                        } elseif ($manualKind === 'goods') {
                            $hpp = 0;
                            $manualHpp = (int) data_get($item->options, 'cost_price', 0);
                        } else {
                            $hpp = 0;
                            $manualHpp = null;
                        }
                    }

                    $qty = (int) $item->qty;
                    $unitPrice = (int) $item->price;
                    $subTotal = $qty * $unitPrice;

                    // ===== Normalisasi item JASA jadi manual/service =====
                    $isService = Str::startsWith((string) $code, 'SRV-') || in_array($productableType, [ServiceMaster::class, 'Modules\Product\Entities\ServiceMaster'], true);

                    if ($isService) {
                        $src = 'manual';
                        $manualKind = 'service';

                        if (empty($productableId) && preg_match('/^SRV-(\d+)/', (string) $code, $m)) {
                            $productableId = (int) $m[1];
                            $productableType = ServiceMaster::class;
                        }

                        $qty = 1;
                        $subTotal = $unitPrice;
                        $hpp = 0;
                        $manualHpp = null;
                    }
                    // ===== END Normalisasi =====

                    $totalHppItem = $hpp + ($manualHpp ?? 0);
                    $subtotalProfit = $subTotal - $totalHppItem * $qty;

                    $originalPrice = (int) data_get($item->options, 'original_price', $item->price);
                    if ($src === 'second' && $productableId) {
                        if ($s = ProductSecond::find($productableId)) {
                            $originalPrice = (int) ($s->sale_price ?? $originalPrice);
                        }
                    }
                    $isPriceAdjusted = (bool) data_get($item->options, 'is_price_adjusted', false);
                    $priceAdjustmentAmount = (int) data_get($item->options, 'price_adjustment_amount', 0);
                    $priceAdjustmentNote = data_get($item->options, 'price_adjustment_note');

                    // Simpan detail penjualan (TERMASUK field manual_kind, original_price, adjustments)
                    $detail = SaleDetails::create([
                        'sale_id' => $sale->id,
                        'product_id' => $src === 'new' ? (int) $item->id : null,
                        'productable_type' => $productableType,
                        'productable_id' => $productableId,
                        'source_type' => $src,
                        'manual_kind' => $manualKind,
                        'item_name' => $item->name,
                        'product_name' => $item->name,
                        'product_code' => $code,
                        'quantity' => (int) $qty,
                        'price' => (int) $unitPrice,
                        'unit_price' => (int) $unitPrice,
                        'sub_total' => (int) $subTotal,
                        'hpp' => (int) $hpp,
                        'manual_hpp' => $manualHpp,
                        'subtotal_profit' => (int) $subtotalProfit,
                        'product_discount_amount' => (int) data_get($item->options, 'discount', 0),
                        'product_discount_type' => data_get($item->options, 'discount_type', 'fixed'),
                        'product_tax_amount' => (int) data_get($item->options, 'tax', 0),

                        'original_price' => (int) $originalPrice,
                        'is_price_adjusted' => $isPriceAdjusted ? 1 : 0,
                        'price_adjustment_amount' => (int) $priceAdjustmentAmount,
                        'price_adjustment_note' => $priceAdjustmentNote,
                        'adjusted_by' => $isPriceAdjusted ? auth()->id() : null,
                        'adjusted_at' => $isPriceAdjusted ? now() : null,
                    ]);

                    // ===== DETEKSI & CATAT MANUAL INPUT =====
                    $isManual = $src === 'manual' || $manualKind === 'service' || $src === 'service_master';
                    $isManual = $isManual || $this->isManualInput($item);

                    if ($isManual) {
                        $hasManualInput = true;

                        // Detail manual untuk owner review
                        ManualInputDetail::create([
                            'sale_id' => $sale->id,
                            'sale_detail_id' => $detail->id,
                            'cashier_id' => auth()->id(),
                            'item_type' => (string) ($manualKind ?? data_get($item->options, 'manual_kind', 'goods')),
                            'item_name' => $item->name,
                            'quantity' => (int) $qty,
                            'price' => (int) $unitPrice,
                            'manual_reason' => data_get($item->options, 'manual_reason'),
                            'cost_price' => (int) data_get($item->options, 'cost_price', 0),
                        ]);

                        // Log audit untuk approval flow
                        ManualInputLog::create([
                            'sale_id' => $sale->id,
                            'sale_detail_id' => $detail->id,
                            'cashier_id' => auth()->id(),
                            'input_type' => 'manual_item',
                            'item_name' => $item->name,
                            'quantity' => (int) $qty,
                            'input_price' => (int) $unitPrice,
                            'reason_provided' => data_get($item->options, 'manual_reason', 'No reason provided'),
                            'approval_status' => 'pending',
                        ]);

                        // Kumpulkan untuk event dispatch
                        $manualItems[] = [
                            'name' => $item->name,
                            'quantity' => (int) $qty,
                            'price' => (int) $unitPrice,
                            'reason' => data_get($item->options, 'manual_reason'),
                            'type' => (string) ($manualKind ?? data_get($item->options, 'manual_kind', 'goods')),
                        ];
                    }
                    // ===== END MANUAL INPUT =====

                    $totalHpp += $totalHppItem * $qty;
                    $totalProfit += (int) $subtotalProfit;
                }

                $hasPriceAdjustment = $cartItems->contains(fn($i) => (bool) data_get($i->options, 'is_price_adjusted', false));

                // Update flag di sale
                $sale->update([
                    'total_hpp' => $totalHpp,
                    'total_profit' => $totalProfit,
                    'has_price_adjustment' => $hasPriceAdjustment ? 1 : 0,
                    'has_manual_input' => $hasManualInput ? 1 : 0,
                    'manual_input_count' => $hasManualInput ? count($manualItems) : 0,
                    'manual_input_summary' => $hasManualInput ? json_encode($manualItems) : null,
                    'is_manual_input_notified' => 0, // akan diset 1 oleh listener event
                ]);

                return $sale;
            });

            // ====== SET STATE KOMPONEN ======
            $this->sale = $sale;
            $this->sale_details = SaleDetails::where('sale_id', $sale->id)->get();

            // Dispatch event pakai $manualItems yang baru diproses (lebih andal)
            if (!empty($manualItems)) {
                Log::info('Dispatch ManualInputCreated from Checkout', [
                    'sale_id' => $this->sale->id,
                    'items_count' => count($manualItems),
                ]);

                event(new ManualInputCreated($this->sale->fresh(), $manualItems, auth()->user()));
            }

            // Kosongkan keranjang setelah jadi invoice
            $this->cart()->destroy();

            $this->quantity = [];
            $this->check_quantity = [];
            $this->discount_type = [];
            $this->item_discount = [];

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
        $this->dispatch('paid-input-ready');
    }

    /**
     * Finalize: buat invoice + (opsional) proses pembayaran full jika input sudah memenuhi.
     */
    public function finalize(): void
    {
        if ($this->cart()->count() === 0 && !$this->sale) {
            $this->dispatch('swal-error', 'Keranjang masih kosong.');
            return;
        }

        if (!$this->sale) {
            $this->createInvoice();
            if (!$this->sale) {
                return;
            }
        }

        $payInt = (int) $this->sanitizeMoney($this->paid_amount);
        if ($payInt >= (int) $this->sale->total_amount) {
            $this->markAsPaid();
        }

        $this->dispatch('checkout-success', [
            'invoice' => $this->sale->reference,
            'has_manual_input' => (int) $this->sale->has_manual_input === 1,
        ]);
    }

    public function onPaymentMethodChange(): void
    {
        if ($this->payment_method !== 'Transfer') {
            $this->bank_name = null;
        }
        $this->show_midtrans_payment = false;
    }

    // ========= Tandai Lunas (manual: Tunai/Transfer/QRIS/Midtrans fallback) =========
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

        $this->validate(['payment_method' => 'required|in:Tunai,Transfer,QRIS,Midtrans']);

        if ($this->payment_method === 'Transfer') {
            $this->validate(['bank_name' => 'required|string|max:255']);
            if (empty($this->bank_name)) {
                $this->addError('bank_name', 'Nama bank wajib diisi untuk pembayaran transfer.');
                return;
            }
        } else {
            $this->bank_name = null;
        }

        $pay = $this->sanitizeMoney($this->paid_amount);
        $total = (int) $this->sale->total_amount;

        if ($pay < $total) {
            $this->dispatch('swal-warning', 'Jumlah dibayar kurang dari total. Lunasi penuh untuk Tandai Lunas.');
            return;
        }

        try {
            DB::transaction(function () use ($total) {
                $sale = Sale::lockForUpdate()->with('saleDetails')->find($this->sale->id);
                if (!$sale) {
                    throw new \RuntimeException('Invoice tidak ditemukan/terkunci.');
                }

                $norm = $this->normalizePaymentMethod($this->payment_method);
                $pmLabel = $norm['label'];
                $pmMethod = $norm['method'];

                $payAmount = (int) min($total, max(0, (int) $this->sanitizeMoney($this->paid_amount)));

                if ($sale->status !== 'Completed') {
                    foreach ($sale->saleDetails as $d) {
                        if ($d->source_type === 'new' && $d->product_id) {
                            $p = Product::whereKey($d->product_id)->lockForUpdate()->first();
                            if (!$p) {
                                throw new \RuntimeException("Produk ID {$d->product_id} tidak ditemukan.");
                            }
                            if ((int) $p->product_quantity < (int) $d->quantity) {
                                throw new \RuntimeException("Stok {$p->product_name} tidak mencukupi.");
                            }
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

                SalePayment::create([
                    'date' => now()->toDateString(),
                    'reference' => 'INV/' . ($sale->reference ?? $sale->id),
                    'amount' => (int) $payAmount,
                    'sale_id' => $sale->id,
                    'payment_method' => $pmLabel,
                    'note' => $pmMethod === 'transfer' ? (string) ($this->bank_name ?? '') : null,
                ]);

                $sale->update([
                    'paid_amount' => (int) $payAmount,
                    'due_amount' => max(0, (int) $sale->total_amount - (int) $payAmount),
                    'status' => 'Completed',
                    'payment_status' => (int) $sale->total_amount - (int) $payAmount > 0 ? 'Partial' : 'Paid',
                    'payment_method' => $pmLabel,
                    'bank_name' => $pmMethod === 'transfer' ? $this->bank_name ?? null : null,
                ]);

                $this->sale = $sale;
            });

            $this->sale->refresh();
            $this->show_payment = false;
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
            $this->dispatch('swal-success', 'Pembayaran Midtrans berhasil.');
        }
    }

    /**
     * Increment quantity (tambah 1)
     */
    public function incrementQuantity(string $rowId): void
    {
        $item = Cart::instance($this->cart_instance)->get($rowId);

        if (!$item) {
            $this->dispatch('swal-error', 'Item tidak ditemukan di keranjang.');
            return;
        }

        $newQty = $item->qty + 1;

        $sourceType = data_get($item->options, 'source_type', 'manual');

        if ($sourceType === 'new' || $sourceType === 'second') {
            $productId = $item->id;
            $product = Product::find($productId);

            if ($product && $newQty > $product->product_quantity) {
                $this->dispatch('swal-error', "Stock tidak cukup! Tersedia: {$product->product_quantity}");
                return;
            }
        }

        Cart::instance($this->cart_instance)->update($rowId, $newQty);

        $this->quantity[$item->id] = $newQty;
        $this->total_amount = $this->cartTotalInt();

        if (!$this->sale) {
            $this->paid_amount = $this->total_amount;
        }

        $this->calculateChange();
        $this->dispatch('cartUpdated');
    }

    /**
     * Decrement quantity (kurang 1)
     */
    public function decrementQuantity(string $rowId): void
    {
        $item = Cart::instance($this->cart_instance)->get($rowId);

        if (!$item) {
            $this->dispatch('swal-error', 'Item tidak ditemukan di keranjang.');
            return;
        }

        $newQty = $item->qty - 1;

        if ($newQty < 1) {
            $this->dispatch('swal-error', 'Qty minimal 1. Jika ingin hapus, klik tombol Hapus.');
            return;
        }

        Cart::instance($this->cart_instance)->update($rowId, $newQty);

        $this->quantity[$item->id] = $newQty;

        $this->total_amount = $this->cartTotalInt();

        if (!$this->sale) {
            $this->paid_amount = $this->total_amount;
        }

        $this->calculateChange();

        $this->dispatch('cartUpdated');
    }

    /**
     * Dipanggil otomatis saat $quantity diubah via wire:model
     */
    public function updatedQuantity($value, $key)
    {
        $item = Cart::instance($this->cart_instance)->search(fn($cartItem) => $cartItem->id == $key)->first();

        if (!$item) {
            return;
        }

        $newQty = max(1, (int) $value);
        $sourceType = data_get($item->options, 'source_type', 'manual');

        if ($sourceType === 'new') {
            $product = Product::find($key);
            if ($product && $newQty > $product->product_quantity) {
                $this->quantity[$key] = $item->qty;
                $this->dispatch('swal-error', "Stock tidak cukup! Tersedia: {$product->product_quantity}");
                return;
            }
        } elseif ($sourceType === 'second') {
            $this->quantity[$key] = $item->qty;
            $this->dispatch('swal-warning', 'Qty item bekas tidak dapat diubah.');
            return;
        }

        Cart::instance($this->cart_instance)->update($item->rowId, ['qty' => $newQty]);

        $this->total_amount = $this->cartTotalInt();
        $this->calculateChange();

        $this->dispatch('cartUpdated');
    }
}
