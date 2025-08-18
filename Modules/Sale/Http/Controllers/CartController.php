<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Str;

class CartController extends Controller
{

    public function addManualLine(Request $request)
{
    $request->validate([
        'name'  => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'qty'   => 'required|integer|min:1',
    ]);

    $name  = $request->string('name');
    $price = (int) $request->input('price'); // angka mentah (Rupiah)
    $qty   = (int) $request->input('qty', 1);

    // Simpan ke instance 'sale' supaya konsisten dengan halaman edit
    $item = Cart::instance('sale')->add([
        'id'      => 'manual-'.uniqid(), // ID unik
        'name'    => $name,
        'qty'     => $qty,
        'price'   => $price,
        'weight'  => 0,
        'options' => [
            'code'        => '-',
            'source_type' => 'manual',
            'discount'    => 0,
            'tax'         => 0,
            // simpan juga sub_total agar footer awal benar walau belum sync
            'sub_total'   => max(0, $price * $qty),
        ],
    ]);

    // hitung subtotal terkini dari server
    $cartItems = Cart::instance('sale')->content();
    $subtotal  = $cartItems->sum(function ($i) {
        $price = (int) $i->price;
        $qty   = (int) $i->qty;
        $disc  = (int) data_get($i->options, 'discount', 0);
        $tax   = (int) data_get($i->options, 'tax', 0);
        return max(0, $price * $qty - $disc + $tax);
    });

    // Render 1 baris <tr> siap pakai (blade partial di poin 3)
    $rowHtml = view('sale::partials.edit-row', ['it' => $item])->render();

    return response()->json([
        'ok'      => true,
        'rowId'   => $item->rowId,
        'rowHtml' => $rowHtml,
        'summary' => [
            'display_sub' => number_format($subtotal, 0, ',', '.'),
        ],
    ]);
}
    /**
     * Update satu baris item pada instance keranjang "sale".
     * Aman untuk item "second": qty dipaksa = 1.
     * Price akan di-update dengan cara re-add (karena package sering tidak mengizinkan update price langsung).
     */
    public function updateLine(\Illuminate\Http\Request $request)
{
    $cart = \Gloudemans\Shoppingcart\Facades\Cart::instance('sale');

    $data = $request->validate([
        'rowId'    => ['required','string'],
        'price'    => ['required'], // masked/number → normalisasi manual
        'qty'      => ['required'],
        'discount' => ['nullable'],
        'tax'      => ['nullable'],
    ]);

    $rowId = $data['rowId'];
    $item  = $cart->get($rowId);

    if (!$item) {
        return response()->json(['ok' => false, 'message' => 'Item tidak ditemukan'], 404);
    }

    // Normalisasi angka
    $price = $this->toInt($data['price']);
    $qty   = (int) $data['qty'];
    $disc  = $this->toInt($data['discount'] ?? 0);
    $tax   = $this->toInt($data['tax'] ?? 0);

    $price = max(0, $price);
    $qty   = max(0, $qty);
    $disc  = max(0, $disc);
    $tax   = max(0, $tax);

    $src   = (string) data_get($item->options, 'source_type', 'new');
    $code  = (string) data_get($item->options, 'code', '-');
    $hpp   = (int) data_get($item->options, 'hpp', 0);

    // Guard second: qty selalu 1
    if ($src === 'second') {
        $qty = 1;
    }

    // Bangun ulang options
    $newOptions = [
        'source_type'      => $src,
        'code'             => $code,
        'discount'         => $disc,
        'tax'              => $tax,
        'hpp'              => $hpp,
        'product_id'       => data_get($item->options, 'product_id'),
        'productable_type' => data_get($item->options, 'productable_type'),
        'productable_id'   => data_get($item->options, 'productable_id'),
    ];

    // Strategi aman: remove & add lagi (agar price/opti pasti terupdate)
    $cart->remove($rowId);
    $newItem = $cart->add([
        'id'      => $item->id,         // id stabil "detail-XX" atau id produk
        'name'    => $item->name,
        'qty'     => $qty,
        'price'   => $price,
        'weight'  => 0,
        'options' => $newOptions,
    ]);

    // Hitung subtotal server
    $subtotalServer = 0;
    foreach ($cart->content() as $ci) {
        $p  = (int) $ci->price;
        $q  = (int) $ci->qty;
        $d  = (int) data_get($ci->options, 'discount', 0);
        $t  = (int) data_get($ci->options, 'tax', 0);
        $u  = max(0, $p - $d + $t);
        $subtotalServer += $u * $q;
    }

    // Subtotal baris baru
    $unitSell = max(0, $price - $disc + $tax);
    $lineSub  = $unitSell * $qty;

    return response()->json([
        'ok'            => true,
        'rowIdNew'      => $newItem->rowId, // rowId bisa berubah
        'lineSubtotal'  => $lineSub,
        'subtotalItems' => $subtotalServer,
        'formatted'     => [
            'lineSubtotal'  => format_currency($lineSub),
            'subtotalItems' => format_currency($subtotalServer),
        ],
    ]);
}

// Reuse helper dari SaleController - duplikasi kecil agar self-contained.
// (Jika Anda ingin DRY, pindahkan ke trait/helper terpisah).
private function toInt($value): int
{
    if (is_null($value)) return 0;
    if (is_int($value))  return $value;
    $num = preg_replace('/[^\d\-]/', '', (string)$value);
    if ($num === '' || $num === '-') return 0;
    return (int) $num;
}


    protected function sanitizeMoney($value): int
    {
        if (is_null($value)) return 0;
        if (is_int($value))  return $value;
        $digits = preg_replace('/[^\d]/', '', (string) $value);
        return $digits === '' ? 0 : (int) $digits;
    }

    /**
     * Hitung ringkasan cart berdasarkan option `sub_total`.
     * (Jika kamu memakai tax/discount global di form, biarkan front-end/submit yang menambahkannya.)
     */
    protected function recalcSummary($cart): array
    {
        $subtotal = 0;
        foreach ($cart->content() as $it) {
            $subtotal += (int) data_get($it->options, 'sub_total', ($it->price * $it->qty));
        }

        return [
            'items'        => $cart->count(),
            'subtotal'     => $subtotal,
            'display_sub'  => number_format($subtotal, 0, ',', '.'),
        ];
    }

    public function addManual(Request $r)
{
    $data = $r->validate([
        'name'  => 'required|string|max:255',
        'price' => 'required|integer|min:0',
        'qty'   => 'required|integer|min:1',
    ]);

    $row = Cart::instance('sale')->add([
        'id'      => 'manual-'.Str::random(8),
        'name'    => $data['name'],
        'qty'     => (int)$data['qty'],
        'price'   => (int)$data['price'],
        'options' => [
            'source_type' => 'manual',
            'code'        => '-',
            'discount'    => 0,
            'tax'         => 0,
        ],
    ]);

    // hitung subtotal item (seperti di blade)
    $subtotal = Cart::instance('sale')->content()->sum(function ($i) {
        $price = (int)$i->price;
        $qty   = (int)$i->qty;
        $disc  = (int) data_get($i->options,'discount',0);
        $tax   = (int) data_get($i->options,'tax',0);
        $isSecond = data_get($i->options,'source_type') === 'second';
        return max(0, $price * ($isSecond ? 1 : $qty) - $disc + $tax);
    });

    return response()->json([
        'ok'      => true,
        'rowId'   => $row->rowId,
        'summary' => ['display_sub' => number_format($subtotal, 0, ',', '.')],
        'message' => 'Item manual ditambahkan.',
    ]);
}

}
