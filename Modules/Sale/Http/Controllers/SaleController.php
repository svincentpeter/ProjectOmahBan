<?php

namespace Modules\Sale\Http\Controllers;

use Modules\Sale\DataTables\SalesDataTable;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\Sale\Http\Requests\StoreSaleRequest;
use Illuminate\Support\Str;
use Modules\Sale\Http\Requests\UpdateSaleRequest;



class SaleController extends Controller
{
    public function index(SalesDataTable $dataTable)
    {
        abort_if(Gate::denies('access_sales'), 403);
        return $dataTable->render('sale::index');
    }

    /**
     * Partial daftar item untuk quick view/section AJAX.
     */
    public function items(Sale $sale)
    {
        $sale->load([
            'saleDetails' => function ($q) {
                $q->select(
                    'id','sale_id','item_name','product_id',
                    'productable_id','productable_type','source_type',
                    'product_name','product_code','quantity','price',
                    'hpp','unit_price','sub_total','subtotal_profit',
                    'product_discount_amount','product_discount_type','product_tax_amount'
                );
            }
        ]);

        return view('sale::partials.items-mini', compact('sale'));
    }

    public function create()
    {
        abort_if(Gate::denies('create_sales'), 403);
        // pastikan mulai bersih
        Cart::instance('sale')->destroy();
        session()->forget('editing_sale_id');
        return view('sale::create');
    }

    /**
     * STORE transaksi baru.
     */
    public function store(StoreSaleRequest $request)
    {
        $cart = Cart::instance('sale');
        if ($cart->count() <= 0) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        // Normalisasi input uang
        $shippingAmount  = $this->sanitizeMoney($request->input('shipping_amount'));
        $paidReq         = $this->sanitizeMoney($request->input('paid_amount'));
        $taxPercent      = (int) ($request->input('tax_percentage') ?? 0);
        $discPercent     = (int) ($request->input('discount_percentage') ?? 0);

        // Hitung subtotal dari keranjang
        [$subtotal, $totalHpp] = $this->calcSubtotalAndHpp($cart->content());

        $taxAmount      = (int) round($subtotal * ($taxPercent / 100));
        $discountAmount = (int) round($subtotal * ($discPercent / 100));
        $grandTotal     = max(0, $subtotal + $taxAmount - $discountAmount + $shippingAmount);

        $paidAmount     = (int) min($grandTotal, max(0, $paidReq));
        $dueAmount      = max(0, $grandTotal - $paidAmount);
        $paymentStatus  = $this->calcPaymentStatus($grandTotal, $paidAmount);

        DB::beginTransaction();
        try {
            // Precheck stok/status
            $this->assertStockOk($cart->content());

            $reference = 'SL-' . now()->format('Ymd-His');

            // Header
            $sale = Sale::create([
                'date'                => $request->input('date', now()->toDateString()),
                'reference'           => $reference,
                'user_id'             => auth()->id(),
                'tax_percentage'      => $taxPercent,
                'discount_percentage' => $discPercent,
                'shipping_amount'     => $shippingAmount,
                'paid_amount'         => $paidAmount,
                'total_amount'        => $grandTotal,
                'due_amount'          => $dueAmount,
                'status'              => $request->input('status', 'Completed'),
                'payment_status'      => $paymentStatus,
                'payment_method'      => $request->input('payment_method', 'Tunai'),
                'bank_name'           => $request->input('payment_method') === 'Transfer' ? $request->input('bank_name') : null,
                'note'                => $request->input('note'),
                'tax_amount'          => $taxAmount,
                'discount_amount'     => $discountAmount,
                'total_hpp'           => $totalHpp,
                'total_profit'        => max(0, $subtotal - $taxAmount + $discountAmount - $shippingAmount - $totalHpp),
            ]);

            // Detail + mutasi stok/status
            foreach ($cart->content() as $item) {
                $this->persistDetailAndMutateStock($sale, $reference, $item);
            }

            // Pembayaran awal
            if ($paidAmount > 0) {
                SalePayment::create([
                    'date'           => $sale->date,
                    'reference'      => 'INV/' . $sale->reference,
                    'amount'         => $paidAmount,
                    'sale_id'        => $sale->id,
                    'payment_method' => $sale->payment_method,
                    'note'           => $sale->payment_method !== 'Tunai' ? (string) $sale->bank_name : null,
                ]);
            }

            $cart->destroy();
            DB::commit();

            toast('Sale Created!', 'success');
            return redirect()->route('sales.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: '.$e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        abort_if(Gate::denies('show_sales'), 403);
        return view('sale::show', compact('sale'));
    }

    /**
     * Edit: reset cart saat berpindah sale & rebuild dari detail jika cart kosong.
     */
    public function edit(Sale $sale)
{
    $cart = Cart::instance('sale');
    $flag = 'editing_sale_id';

    // Jika pindah ke sale lain → kosongkan cart dan set flag baru
    if (session($flag) !== $sale->id) {
        $cart->destroy();
        session([$flag => $sale->id]);
    }

    // Rebuild hanya jika kosong (supaya item manual baru tidak hilang)
    if ($cart->count() === 0) {
        foreach ($sale->saleDetails as $d) {
            $cart->add([
                'id'      => 'detail-'.$d->id, // rowId unik & stabil
                'name'    => $d->product_name,
                'qty'     => $d->source_type === 'second' ? 1 : (int) $d->quantity,
                'price'   => (int) $d->price,
                'weight'  => 0, // <- penting untuk beberapa versi shoppingcart
                'options' => [
                    'source_type'       => $d->source_type,
                    'code'              => $d->product_code ?: '-',
                    'discount'          => (int) $d->product_discount_amount,
                    'tax'               => (int) $d->product_tax_amount,
                    'hpp'               => (int) $d->hpp,
                    'product_id'        => $d->product_id,
                    'productable_type'  => $d->productable_type,
                    'productable_id'    => $d->productable_id,
                ],
            ]);
        }
    }

    return view('sale::edit', compact('sale'));
}


    /**
     * UPDATE: tulis ulang header & detail dari cart, dan catat delta payment.
     */
    public function update(UpdateSaleRequest $request, Sale $sale)
{
    $data = $request->validate([
        'date'                 => ['required','date'],
        'status'               => ['required','in:Pending,Shipped,Completed'],
        'payment_method'       => ['required','in:Tunai,Transfer,QRIS'],
        'bank_name'            => ['nullable','string','max:120'],
        'shipping_amount'      => ['nullable','integer','min:0'],
        'tax_percentage'       => ['nullable','integer','min:0','max:100'],
        'discount_percentage'  => ['nullable','integer','min:0','max:100'],
        'paid_amount'          => ['nullable','integer','min:0'],
        'note'                 => ['nullable','string'],
    ]);

    $items = Cart::instance('sale')->content();

    // 1) Fail-fast: dilarang update kalau cart kosong → mencegah detail hilang.
    if ($items->isEmpty()) {
        return back()->with('swal-error', 'Keranjang kosong. Tambahkan item terlebih dahulu.')
                     ->withInput();
    }

    // 2) Hitung subtotal item (qty untuk 'second' dipaksa 1)
    $subtotalItems = $items->sum(function ($i) {
        $price = (int) $i->price;
        $qty   = data_get($i->options, 'source_type') === 'second' ? 1 : (int) $i->qty;
        $disc  = (int) data_get($i->options, 'discount', 0);
        $tax   = (int) data_get($i->options, 'tax', 0);
        return max(0, $price * $qty - $disc + $tax);
    });

    $ship   = (int) ($data['shipping_amount'] ?? 0);
    $taxPct = (int) ($data['tax_percentage'] ?? 0);
    $disPct = (int) ($data['discount_percentage'] ?? 0);

    $taxAmt  = (int) round($subtotalItems * ($taxPct / 100));
    $discAmt = (int) round($subtotalItems * ($disPct / 100));
    $grand   = max(0, $subtotalItems + $taxAmt - $discAmt + $ship);

    // Paid saat ini/target
    $existingPaid = (int) ($sale->paid_amount ?? 0);
    $targetPaid   = (int) ($data['paid_amount'] ?? $existingPaid);

    $paymentStatus = 'Unpaid';
    if ($targetPaid >= $grand)      $paymentStatus = 'Paid';
    elseif ($targetPaid > 0)        $paymentStatus = 'Partial';

    try {
        DB::transaction(function () use (
            $sale, $data, $items, $grand, $subtotalItems, $taxPct, $disPct, $ship,
            $paymentStatus, $existingPaid, $targetPaid
        ) {
            // 3) Update header
            $sale->date                 = $data['date'];
            $sale->status               = $data['status'];
            $sale->payment_method       = $data['payment_method'];
            $sale->bank_name            = $data['payment_method'] === 'Transfer' ? ($data['bank_name'] ?? null) : null;
            $sale->shipping_amount      = $ship;
            $sale->tax_percentage       = $taxPct;
            $sale->discount_percentage  = $disPct;
            $sale->total_amount         = $grand;
            $sale->payment_status       = $paymentStatus;
            $sale->note                 = $data['note'] ?? $sale->note;
            $sale->save();

            // 4) Tulis ulang detail (tanpa mutasi stok saat UPDATE)
            SaleDetails::where('sale_id', $sale->id)->delete();

            $totalHpp   = 0;
            $inserted   = 0;

            foreach ($items as $i) {
                $src   = (string) data_get($i->options, 'source_type', 'new');
                $qty   = $src === 'second' ? 1 : (int) $i->qty;
                $price = (int) $i->price;
                $disc  = (int) data_get($i->options, 'discount', 0);
                $tax   = (int) data_get($i->options, 'tax', 0);
                $hpp   = (int) data_get($i->options, 'hpp', 0);
                $code  = (string) data_get($i->options, 'code', '-');

                $sub   = max(0, $price * $qty - $disc + $tax);

                SaleDetails::create([
                    'sale_id'                 => $sale->id,
                    'item_name'               => $i->name, // <- kolom wajib
                    'product_id'              => data_get($i->options, 'product_id'),
                    'productable_type'        => data_get($i->options, 'productable_type'),
                    'productable_id'          => data_get($i->options, 'productable_id'),
                    'source_type'             => $src, // new | second | manual
                    'product_name'            => $i->name,
                    'product_code'            => $code,
                    'quantity'                => $qty,
                    'price'                   => $price,
                    'hpp'                     => $hpp,
                    'unit_price'              => $price,
                    'sub_total'               => $sub,
                    'subtotal_profit'         => ($price - $hpp) * $qty, // bisa negatif
                    'product_discount_amount' => $disc,
                    'product_discount_type'   => 'fixed',
                    'product_tax_amount'      => $tax,
                ]);

                $totalHpp += ($hpp * $qty);
                $inserted++;
            }

            // Safety net: kalau tidak ada baris tersimpan, batalkan
            if ($inserted === 0) {
                throw new \RuntimeException('Tidak ada item yang disimpan.');
            }

            // 5) Hitung ulang agregat profit/hpp
            $sale->total_hpp    = $totalHpp;
            $sale->total_profit = max(0, $subtotalItems - $totalHpp); // pakai subtotal bersih item
            $sale->save();

            // 6) Catat delta pembayaran (positif=bayar, negatif=refund)
            // 6) Catat delta pembayaran (positif=bayar, negatif=refund)
$delta = $targetPaid - $existingPaid;
if ($delta !== 0) {
    \Modules\Sale\Entities\SalePayment::create([
        'sale_id'        => $sale->id,
        'reference'      => $this->makePaymentReference(),   // <-- WAJIB: isi reference
        'amount'         => abs($delta),                      // simpan nilai absolut
        'payment_method' => $data['payment_method'],
        'note'           => $delta > 0 ? 'Penyesuaian saat edit (+)' : 'Penyesuaian saat edit (refund)',
        'date'           => now(),
    ]);

    // sinkronkan header paid_amount ke target
    $sale->paid_amount    = max(0, $existingPaid + $delta);
    $sale->payment_status = ($sale->paid_amount >= $sale->total_amount)
                            ? 'Paid' : (($sale->paid_amount > 0) ? 'Partial' : 'Unpaid');
    $sale->save();
}

        });
    } catch (\Throwable $e) {
        // Rollback sudah otomatis; tampilkan pesan ramah
        return back()->with('swal-error', 'Gagal menyimpan perubahan: '.$e->getMessage())
                     ->withInput();
    }

    // selesai edit: kosongkan cart & hapus penanda sesi supaya tidak "nyangkut"
    Cart::instance('sale')->destroy();
    session()->forget('editing_sale_id');

    return redirect()->route('sales.show', $sale)->with('swal-success', 'Sale berhasil diperbarui.');
}

/**
 * Buat reference unik untuk sale_payments.
 * Format: SP-YYYYMMDD-HHMMSS-XXXXX
 */
private function makePaymentReference(): string
{
    $ref = 'SP-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(5));
    // Jika ada constraint unique, pastikan unik dengan loop ringan
    while (\Modules\Sale\Entities\SalePayment::where('reference', $ref)->exists()) {
        $ref = 'SP-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(6));
    }
    return $ref;
}



/**
 * Konversi string uang masked → integer rupiah.
 */
private function toInt($value): int
{
    if (is_null($value)) return 0;
    if (is_int($value))  return $value;
    // buang karakter non-digit (termasuk titik/koma/space)
    $num = preg_replace('/[^\d\-]/', '', (string)$value);
    if ($num === '' || $num === '-') return 0;
    return (int) $num;
}

/**
 * Tentukan status pembayaran dari grand total & paid.
 */
private function paymentStatus(int $grand, int $paid): string
{
    if ($paid <= 0)          return 'Unpaid';
    if ($paid >= $grand)     return 'Paid';
    return 'Partial';
}


    public function destroy(Sale $sale)
    {
        abort_if(Gate::denies('delete_sales'), 403);
        $sale->delete();
        toast('Sale Deleted!', 'warning');
        return redirect()->route('sales.index');
    }

    // ===================== Helper Methods =====================

    protected function sanitizeMoney($value): int
    {
        if (is_null($value)) return 0;
        if (is_int($value))  return $value;
        $digits = preg_replace('/[^\d]/', '', (string) $value);
        return $digits === '' ? 0 : (int) $digits;
    }

    /**
     * Subtotal & total HPP dari cart. Qty untuk 'second' dipaksa 1.
     */
    protected function calcSubtotalAndHpp($items): array
    {
        $subtotal = 0;
        $totalHpp = 0;
        foreach ($items as $it) {
            $qty       = data_get($it->options, 'source_type') === 'second' ? 1 : (int) $it->qty;
            $unitPrice = (int) $it->price;
            $line      = $unitPrice * $qty;

            $disc      = (int) data_get($it->options, 'discount', 0);
            $tax       = (int) data_get($it->options, 'tax', 0);
            $lineNet   = $line - $disc + $tax;

            $subtotal += $lineNet;
            $totalHpp += ((int) data_get($it->options, 'hpp', 0)) * $qty;
        }
        return [$subtotal, $totalHpp];
    }

    protected function calcPaymentStatus(int $grandTotal, int $paidAmount): string
    {
        if ($paidAmount <= 0) return 'Unpaid';
        if ($paidAmount >= $grandTotal) return 'Paid';
        return 'Partial';
    }

    /**
     * Precheck stok produk baru & status second sebelum commit.
     */
    protected function assertStockOk($items): void
    {
        foreach ($items as $it) {
            $src = data_get($it->options, 'source_type', 'new');
            $qty = (int) $it->qty;

            if ($src === 'new') {
                $p = Product::lockForUpdate()->findOrFail((int) $it->id);
                if ($p->product_quantity < $qty) {
                    throw new \RuntimeException("Stok {$p->product_name} tidak mencukupi.");
                }
            } elseif ($src === 'second') {
                $s = ProductSecond::lockForUpdate()->findOrFail((int) $it->id);
                if (strtolower((string)$s->status) !== 'available') {
                    throw new \RuntimeException("Produk bekas {$s->name} sudah terjual/tidak tersedia.");
                }
            } else {
                // manual/jasa: tidak ada precheck stok
            }
        }
    }

    // ========== AJAX: Hapus satu baris dari keranjang edit ==========
    public function removeLine(Request $r)
    {
        $rowId = $r->input('rowId');

        try {
            Cart::instance('sale')->remove($rowId);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Row tidak ditemukan']);
        }

        // Subtotal server-authoritative (qty second = 1)
        $sub = Cart::instance('sale')->content()->sum(function ($i) {
            $price = (int) $i->price;
            $qty   = data_get($i->options, 'source_type') === 'second' ? 1 : (int) $i->qty;
            $disc  = (int) data_get($i->options, 'discount', 0);
            $tax   = (int) data_get($i->options, 'tax', 0);
            return max(0, $price * $qty - $disc + $tax);
        });

        return response()->json([
            'ok' => true,
            'summary' => ['display_sub' => number_format($sub, 0, ',', '.')]
        ]);
    }

    // ========== AJAX: Tambah item manual (jasa/biaya) ke keranjang edit ==========
    public function addManualLine(Request $request)
{
    $data = $request->validate([
        'name'  => ['required','string','max:255'],
        'price' => ['required','integer','min:1'],
        'qty'   => ['required','integer','min:1'],
    ]);

    $cart = Cart::instance('sale');
    $item = $cart->add([
        'id'      => 'manual-'.Str::uuid(),
        'name'    => $data['name'],
        'qty'     => $data['qty'],
        'price'   => $data['price'],
        'weight'  => 0, // <- WAJIB agar tidak error "weight"
        'options' => [
            'source_type' => 'manual',
            'code'        => '-',
            'discount'    => 0,
            'tax'         => 0,
            'hpp'         => 0,
        ],
    ]);

    // Hitung subtotal server
    $subtotal = 0;
    foreach ($cart->content() as $ci) {
        $p = (int)$ci->price;
        $q = (int)$ci->qty;
        $d = (int) data_get($ci->options,'discount',0);
        $t = (int) data_get($ci->options,'tax',0);
        $subtotal += max(0, ($p - $d + $t) * $q);
    }

    $rowHtml = view('sale::partials.edit-row', ['it' => $item])->render();

    return response()->json([
        'ok'             => true,
        'rowHtml'        => $rowHtml,
        'subtotalItems'  => $subtotal,
        'formatted'      => ['subtotalItems' => number_format($subtotal,0,',','.')],
    ]);
}


public function printA4(Sale $sale)
{
    // Eager load seperlunya
    $sale->load('user', 'saleDetails.product.brand');

    $pdf = \PDF::loadView('sale::print', ['sale' => $sale])->setPaper('a4');
    return $pdf->stream('sale-' . $sale->reference . '.pdf');
}

    /**
     * Simpan baris detail + lakukan mutasi stok/status sesuai sumber item (untuk STORE).
     */
    protected function persistDetailAndMutateStock(Sale $sale, string $reference, $item): void
    {
        $qty       = data_get($item->options, 'source_type') === 'second' ? 1 : (int) $item->qty;
        $unitPrice = (int) $item->price;
        $line      = $unitPrice * $qty;

        $disc      = (int) data_get($item->options, 'discount', 0);
        $tax       = (int) data_get($item->options, 'tax', 0);
        $lineNet   = $line - $disc + $tax;

        $src       = data_get($item->options, 'source_type', 'new');
        $code      = (string) data_get($item->options, 'code', 'MANUAL');
        $hpp       = (int) data_get($item->options, 'hpp', 0);
        $name      = $item->name;

        $productId       = null;
        $productableType = null;
        $productableId   = null;

        if ($src === 'new') {
            $p = Product::lockForUpdate()->findOrFail((int) $item->id);
            if (in_array($sale->status, ['Shipped','Completed'])) {
                if ($p->product_quantity < $qty) {
                    throw new \RuntimeException("Stok {$p->product_name} tidak mencukupi.");
                }
                $p->decrement('product_quantity', $qty);

                DB::table('stock_movements')->insert([
                    'productable_type' => Product::class,
                    'productable_id'   => $p->id,
                    'type'             => 'out',
                    'quantity'         => $qty,
                    'description'      => 'Sale #'.$reference,
                    'user_id'          => auth()->id(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
            $productId = $p->id;
        } elseif ($src === 'second') {
            $s = ProductSecond::lockForUpdate()->findOrFail((int) $item->id);
            if (in_array($sale->status, ['Shipped','Completed'])) {
                if (strtolower((string) $s->status) !== 'available') {
                    throw new \RuntimeException("Produk bekas {$s->name} sudah terjual/tidak tersedia.");
                }
                $s->update(['status' => 'sold']);

                DB::table('stock_movements')->insert([
                    'productable_type' => ProductSecond::class,
                    'productable_id'   => $s->id,
                    'type'             => 'out',
                    'quantity'         => 1,
                    'description'      => 'Sale (second) #'.$reference,
                    'user_id'          => auth()->id(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
            $productableType = ProductSecond::class;
            $productableId   = $s->id;
        } // manual: tidak ada mutasi stok

        SaleDetails::create([
            'sale_id'                 => $sale->id,
            'item_name'               => $name,
            'product_id'              => $productId,
            'productable_type'        => $productableType,
            'productable_id'          => $productableId,
            'source_type'             => $src,
            'product_name'            => $name,
            'product_code'            => $code,
            'quantity'                => $qty,
            'price'                   => $unitPrice,
            'hpp'                     => $hpp,
            'unit_price'              => $unitPrice,
            'sub_total'               => $lineNet,
            'subtotal_profit'         => ($unitPrice - $hpp) * $qty,
            'product_discount_amount' => $disc,
            'product_discount_type'   => data_get($item->options, 'discount_type', 'fixed'),
            'product_tax_amount'      => $tax,
        ]);
    }
}
