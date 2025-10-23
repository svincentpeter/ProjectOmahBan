<?php

namespace Modules\Sale\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
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
    /**
     * Tampilkan halaman POS.
     */
    public function index()
    {
        // Siapkan data pendukung untuk tab produk
        $categories = Category::orderBy('category_name')->get();

        // Pastikan instance keranjang POS konsisten
        if (!session()->has('cart_instance')) {
            session(['cart_instance' => 'sale']);
        }

        return view('sale::pos.index', [
            'categories' => $categories,
            'product_categories' => $categories, // alias agar view lama tetap hidup
        ]);
    }

    /**
     * Simpan transaksi POS.
     *
     * Catatan penting:
     * - Semua nominal uang disimpan sebagai integer (rupiah utuh).
     * - Produk baru akan mengurangi stok kuantitas.
     * - Produk bekas (second) ditandai sold (1 unit unik).
     * - Detail second menggunakan kolom polymorphic productable_*
     */
    public function store(StorePosSaleRequest $request)
    {
        $cart = Cart::instance('sale');

        if ($cart->count() <= 0) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        // --- Normalisasi input uang ---
        $shippingAmount     = $this->sanitizeMoney($request->input('shipping_amount'));
        $paidAmountRequest  = $this->sanitizeMoney($request->input('paid_amount'));
        $taxPercent         = (int) ($request->input('tax_percentage') ?? 0);
        $discountPercent    = (int) ($request->input('discount_percentage') ?? 0);

        // --- Hitung subtotal berdasarkan item keranjang ---
        $items = $cart->content();
        $subtotal    = 0;
        $totalHpp    = 0;

        foreach ($items as $item) {
            $qty        = (int) $item->qty;
            $unitPrice  = (int) $item->price;
            $line       = $unitPrice * $qty;

            $itemDisc   = (int) data_get($item->options, 'discount', 0);
            $itemTax    = (int) data_get($item->options, 'tax', 0);
            $lineNet    = $line - $itemDisc + $itemTax;

            $subtotal  += $lineNet;
            $totalHpp  += ((int) data_get($item->options, 'hpp', 0)) * $qty;
        }

        $taxAmount       = (int) round($subtotal * ($taxPercent / 100));
        $discountAmount  = (int) round($subtotal * ($discountPercent / 100));

        $grandTotal      = max(0, $subtotal + $taxAmount - $discountAmount + $shippingAmount);

        // Lindungi dari input salah: paid tidak boleh > total
        $paidAmount      = (int) min($grandTotal, max(0, $paidAmountRequest));
        $dueAmount       = max(0, $grandTotal - $paidAmount);
        $paymentStatus   = $paidAmount >= $grandTotal ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Unpaid');

        DB::beginTransaction();

        try {
            // Kunci produk yang akan terlibat agar tidak race condition
            $this->assertStockSufficientOrAvailable($items);

            // Buat nomor referensi sederhana (bisa diganti dengan generator terpisah)
            $reference = 'SL-' . now()->format('Ymd-His');

            // Simpan header sale
            $sale = Sale::create([
                'date'                => now()->toDateString(),
                'reference'           => $reference,
                'user_id'             => auth()->id(),
                'customer_name'       => $request->input('customer_name') ?: null, // 👈 TAMBAHAN: Simpan customer name
                'tax_percentage'      => $taxPercent,
                'discount_percentage' => $discountPercent,
                'shipping_amount'     => $shippingAmount,
                'paid_amount'         => $paidAmount,
                'total_amount'        => $grandTotal,
                'due_amount'          => $dueAmount,
                'status'              => 'Completed',
                'payment_status'      => $paymentStatus,
                'payment_method'      => $request->input('payment_method', 'Tunai'),
                'bank_name'           => $request->input('payment_method') === 'Transfer' ? (string) $request->input('bank_name') : null,
                'note'                => $request->input('note'),
                'tax_amount'          => $taxAmount,
                'discount_amount'     => $discountAmount,
                'total_hpp'           => $totalHpp,
                'total_profit'        => max(0, $grandTotal - $taxAmount - $discountAmount - $shippingAmount - $totalHpp), // konservatif
            ]);

            // Simpan detail + lakukan mutasi stok/status
            foreach ($items as $item) {
                $qty        = (int) $item->qty;
                $unitPrice  = (int) $item->price;
                $line       = $unitPrice * $qty;

                $itemDisc   = (int) data_get($item->options, 'discount', 0);
                $itemTax    = (int) data_get($item->options, 'tax', 0);
                $lineNet    = $line - $itemDisc + $itemTax;

                $source     = data_get($item->options, 'source_type', 'new');
                $code       = (string) data_get($item->options, 'code', data_get($item->options, 'product_code', 'MANUAL'));
                $hpp        = (int) data_get($item->options, 'hpp', 0);
                $name       = $item->name;

                $productId        = null;
                $productableType  = null;
                $productableId    = null;

                if ($source === 'new') {
                    $productId = (int) $item->id;

                    // Kurangi stok produk baru
                    /** @var Product $p */
                    $p = Product::lockForUpdate()->findOrFail($productId);
                    if ($p->product_quantity < $qty) {
                        throw new \RuntimeException("Stok produk {$p->product_name} tidak mencukupi.");
                    }
                    $p->decrement('product_quantity', $qty);

                    // Catat mutasi
                    DB::table('stock_movements')->insert([
                        'productable_type' => Product::class,
                        'productable_id'   => $p->id,
                        'type'             => 'out',
                        'quantity'         => $qty,
                        'description'      => 'Sale #' . $reference,
                        'user_id'          => auth()->id(),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                } elseif ($source === 'second') {
                    // Tandai sold
                    $second = ProductSecond::lockForUpdate()->findOrFail((int)$item->id);
                    if (strtolower((string)$second->status) !== 'available') {
                        throw new \RuntimeException("Produk bekas {$second->name} sudah terjual/tidak tersedia.");
                    }
                    $second->update(['status' => 'sold']);

                    $productableType = ProductSecond::class;
                    $productableId   = $second->id;

                    // Catat mutasi keluar 1 unit
                    DB::table('stock_movements')->insert([
                        'productable_type' => ProductSecond::class,
                        'productable_id'   => $second->id,
                        'type'             => 'out',
                        'quantity'         => 1,
                        'description'      => 'Sale (second) #' . $reference,
                        'user_id'          => auth()->id(),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                } else {
                    // manual service/item
                    // tidak ada mutasi stok
                }

                SaleDetails::create([
                    'sale_id'                => $sale->id,
                    'item_name'              => $name,
                    'product_id'             => $productId,
                    'productable_type'       => $productableType,
                    'productable_id'         => $productableId,
                    'source_type'            => $source,
                    'product_name'           => $name,
                    'product_code'           => $code,
                    'quantity'               => $qty,
                    'price'                  => $unitPrice,
                    'hpp'                    => $hpp,
                    'unit_price'             => $unitPrice,
                    'sub_total'              => $lineNet,
                    'subtotal_profit'        => max(0, ($unitPrice - $hpp) * $qty),
                    'product_discount_amount'=> $itemDisc,
                    'product_discount_type'  => data_get($item->options, 'discount_type', 'fixed'),
                    'product_tax_amount'     => $itemTax,
                ]);
            }

            // Catat pembayaran jika ada (boleh 0)
            SalePayment::create([
                'date'           => now()->toDateString(),
                'reference'      => 'INV/' . $sale->reference,
                'amount'         => $paidAmount,
                'sale_id'        => $sale->id,
                'payment_method' => $request->input('payment_method', 'Tunai'),
                'note' => $request->input('payment_method') === 'Transfer' ? (string) $request->input('bank_name') : null,
            ]);

            // Bersihkan keranjang setelah sukses
            $cart->destroy();

            DB::commit();

            session()->flash('swal-success', 'Transaksi Berhasil Disimpan!');
            return redirect()->route('app.pos.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Pastikan stok produk baru cukup dan second masih available.
     * Dilakukan sebelum membuat transaksi untuk menghindari setengah jalan.
     */
    protected function assertStockSufficientOrAvailable($items): void
    {
        foreach ($items as $item) {
            $source = data_get($item->options, 'source_type', 'new');
            $qty    = (int) $item->qty;

            if ($source === 'new') {
                /** @var Product $p */
                $p = Product::lockForUpdate()->findOrFail((int) $item->id);
                if ($p->product_quantity < $qty) {
                    throw new \RuntimeException("Stok {$p->product_name} tidak mencukupi.");
                }
            } elseif ($source === 'second') {
                $second = ProductSecond::lockForUpdate()->findOrFail((int) $item->id);
                if (strtolower((string)$second->status) !== 'available') {
                    throw new \RuntimeException("Produk bekas {$second->name} sudah terjual/tidak tersedia.");
                }
            } else {
                // manual item: ok
            }
        }
    }

    /**
     * Ubah angka berformat (mis. "1.250.000") menjadi integer rupiah 1250000.
     */
    protected function sanitizeMoney($value): int
    {
        if (is_null($value)) return 0;
        if (is_int($value)) return $value;
        $digits = preg_replace('/[^\d]/', '', (string) $value);
        return $digits === '' ? 0 : (int) $digits;
    }

    /**
     * 👇 PERBAIKAN: Cetak PDF Invoice POS dengan customer_name
     */
    public function printPos(Sale $sale)
    {
        // Memuat semua relasi yang dibutuhkan untuk nota
        $sale->load([
            'user',
            'saleDetails.product.brand',     // Untuk produk baru
            'saleDetails.productable.brand'  // Untuk produk bekas
        ]);

        // 👇 DEBUG: Uncomment untuk cek apakah customer_name ada
        // dd($sale->customer_name);

        $pdf = \PDF::loadView('sale::print-pos', ['sale' => $sale])
            ->setPaper('a6', 'landscape')
            ->setOption('margin-top', 5)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5);

        return $pdf->stream('nota-' . $sale->reference . '.pdf');
    }
}
