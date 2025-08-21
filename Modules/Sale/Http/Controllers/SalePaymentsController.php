<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductSecond;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Yajra\DataTables\Facades\DataTables;

class SalePaymentsController extends Controller
{
    /* ============================================================
     |  Halaman klasik (Index daftar pembayaran & Create)
     |============================================================ */

    public function index($sale_id)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        // Untuk index yang memakai DataTables, kita cukup bawa ringkasan sale saja.
        $sale = Sale::select('id','reference','total_amount','paid_amount','due_amount','payment_status')
            ->findOrFail($sale_id);

        return view('sale::payments.index', compact('sale'));
    }

    /**
     * Sumber data untuk DataTables pada halaman index pembayaran per-invoice.
     * Route contoh:
     * GET sales/{sale}/payments/data  -> name('sale-payments.datatable')
     */
    public function datatable(Sale $sale)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        // Select kolom aman (bank_name opsional)
        $select = [
            'id','sale_id','reference','amount','payment_method','note','date','created_at'
        ];
        $hasBank = Schema::hasColumn('sale_payments', 'bank_name');
        if ($hasBank) {
            $select[] = 'bank_name';
        }

        $q = $sale->payments()
            ->select($select)
            ->latest('date')->latest('id');

        return DataTables::of($q)
            ->editColumn('date', fn($r) => optional($r->date)->format('d/m/Y'))
            ->addColumn('amount_formatted', fn($r) => format_currency((int)$r->amount))
            ->addColumn('bank_name', function($r) use ($hasBank) {
                return $hasBank ? ($r->bank_name ?? '') : '';
            })
            ->addColumn('actions', function ($r) use ($sale) {
                // butuh view: sale::payments.partials.actions (hapus, dll)
                return view('sale::payments.partials.actions', [
                    'sale'    => $sale,
                    'payment' => $r
                ])->render();
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create($sale_id)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $sale = Sale::select('id','reference','total_amount','paid_amount','due_amount','payment_status')
            ->findOrFail($sale_id);

        return view('sale::payments.create', compact('sale'));
    }

    /* ============================================================
     |  Classic Store/Update/Destroy
     |============================================================ */

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $data = $request->validate([
            'sale_id'        => 'required|exists:sales,id',
            'date'           => 'required|date',
            'payment_method' => 'required|in:Tunai,Transfer,QRIS',
            'amount'         => 'required|integer|min:1',
            'note'           => 'nullable|string|max:255',
            'bank_name'      => 'nullable|string|max:150',
        ]);

        $sale = Sale::lockForUpdate()->findOrFail($data['sale_id']);

        return DB::transaction(function() use ($sale, $data) {

            // Clamp agar tidak overpay
            $amount = (int) $data['amount'];
            $amount = max(1, $amount);
            $amount = min($amount, (int) $sale->due_amount);

            // Generate reference sederhana
            $nextId    = (int) (SalePayment::max('id') + 1);
            $reference = 'SP-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $payload = [
                'sale_id'        => $sale->id,
                'reference'      => $reference,
                'amount'         => $amount,
                'payment_method' => $data['payment_method'],
                'note'           => $data['note'] ?? null,
                'date'           => $data['date'],
            ];

            // Simpan bank_name bila kolom ada; kalau tidak, gabung ke note
            if (!empty($data['bank_name'])) {
                if (Schema::hasColumn('sale_payments', 'bank_name')) {
                    $payload['bank_name'] = $data['bank_name'];
                } else {
                    $payload['note'] = trim(
                        ($payload['note'] ? $payload['note'].'; ' : '') . 'Bank: '.$data['bank_name']
                    );
                }
            }

            $pay = new SalePayment();
            $pay->sale_id        = $sale->id;
            $pay->reference      = $reference;
            $pay->amount         = $amount;
            $pay->payment_method = $data['payment_method'];
            $pay->note           = $payload['note'] ?? null;
            $pay->date           = $data['date'];

            if (Schema::hasColumn('sale_payments', 'bank_name')) {
                $pay->bank_name = $data['bank_name'] ?? null;
            }
            $pay->save();

            // ==================== [START] REVISI PENGURANGAN STOK ====================
            // Simpan status sebelum dihitung ulang untuk perbandingan.
            $statusSebelumnya = $sale->status;

            // Recalc & auto-map status pembayaran dan penjualan.
            $sale->recalcPaymentAndStatus();

            // Jika status penjualan berubah dari BUKAN 'Completed' menjadi 'Completed',
            // maka lakukan pengurangan stok.
            if ($statusSebelumnya !== 'Completed' && $sale->status === 'Completed') {
                // Muat ulang relasi saleDetails untuk memastikan data terbaru.
                $sale->load('saleDetails');
                
                foreach ($sale->saleDetails as $detail) {
                    // Jika item adalah produk baru (new)
                    if ($detail->source_type === 'new' && $detail->product_id) {
                        if ($produk = Product::find($detail->product_id)) {
                            $produk->decrement('product_quantity', $detail->quantity);
                        }
                    // Jika item adalah produk bekas (second)
                    } elseif ($detail->source_type === 'second' && $detail->productable_id) {
                        if ($produkBekas = ProductSecond::find($detail->productable_id)) {
                            $produkBekas->update(['status' => 'sold']);
                        }
                    }
                }
            }
            // ==================== [END] REVISI PENGURANGAN STOK ====================

            // Sukses: kembali ke daftar bila sudah paid, agar alur cepat
            if ($sale->payment_status === 'Paid') {
                session()->flash('swal-success', 'Pembayaran tersimpan & invoice sudah Lunas.');
                return redirect()->route('sales.index');
            }

            session()->flash('swal-success', 'Pembayaran tersimpan.');
            return redirect()->route('sale-payments.create', $sale->id);
        });
    }

    public function edit($sale_id, SalePayment $salePayment)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $sale = Sale::findOrFail($sale_id);

        return view('sale::payments.edit', compact('sale','salePayment'));
    }

    public function update(Request $request, SalePayment $salePayment)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $data = $request->validate([
            'date'           => 'required|date',
            'payment_method' => 'required|in:Tunai,Transfer,QRIS',
            'amount'         => 'required|integer|min:1',
            'note'           => 'nullable|string|max:255',
            'bank_name'      => 'nullable|string|max:150',
        ]);

        $sale = Sale::lockForUpdate()->findOrFail($salePayment->sale_id);

        return DB::transaction(function() use ($sale, $salePayment, $data) {

            // Untuk edit, headroom = due sekarang + nilai lama baris tsb
            $headroom = (int) $sale->due_amount + (int) $salePayment->amount;
            $amount   = min(max(1, (int)$data['amount']), $headroom);

            $salePayment->payment_method = $data['payment_method'];
            $salePayment->amount         = $amount;
            $salePayment->note           = $data['note'] ?? null;
            $salePayment->date           = $data['date'];

            if (Schema::hasColumn('sale_payments', 'bank_name')) {
                $salePayment->bank_name = $data['bank_name'] ?? null;
            } else {
                // Kolom tidak ada → gabung ke note
                if (!empty($data['bank_name'])) {
                    $salePayment->note = trim(
                        ($salePayment->note ? $salePayment->note.'; ' : '') . 'Bank: '.$data['bank_name']
                    );
                }
            }

            $salePayment->save();

            $sale->recalcPaymentAndStatus();

            session()->flash('swal-success', 'Pembayaran diperbarui.');
            return redirect()->route('sale-payments.index', $sale->id);
        });
    }

    /**
     * Hapus pembayaran.
     * Kompatibel dengan:
     * - rute lama:  /sale-payments/destroy/{payment}
     * - rute nested: /sales/{sale}/payments/{payment}
     * Jika request AJAX/JSON → balas JSON. Selain itu → redirect klasik.
     */
    public function destroy($sale_id = null, SalePayment $salePayment)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $saleId = $salePayment->sale_id;

        return DB::transaction(function() use ($salePayment, $saleId) {
            $salePayment->delete();

            $sale = Sale::lockForUpdate()->findOrFail($saleId);
            $sale->recalcPaymentAndStatus();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['ok' => true]);
            }

            session()->flash('swal-success', 'Pembayaran dihapus & status diperbarui.');
            return redirect()->route('sale-payments.index', $saleId);
        });
    }

    /* ============================================================
     |  Versi AJAX (inline editor / quick pay)
     |============================================================ */

    public function ajaxStore(Request $request)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $request->merge(['amount' => (int) $request->input('amount')]);
        $data = $request->validate([
            'sale_id'        => 'required|exists:sales,id',
            'date'           => 'required|date',
            'payment_method' => 'required|in:Tunai,Transfer,QRIS',
            'amount'         => 'required|integer|min:1',
            'note'           => 'nullable|string|max:255',
            'bank_name'      => 'nullable|string|max:150',
        ]);

        $sale = Sale::lockForUpdate()->findOrFail($data['sale_id']);

        try {
            DB::beginTransaction();

            $amount = min(max(1, (int)$data['amount']), (int)$sale->due_amount);

            $nextId    = (int) (SalePayment::max('id') + 1);
            $reference = 'SP-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $payload = [
                'sale_id'        => $sale->id,
                'reference'      => $reference,
                'amount'         => $amount,
                'payment_method' => $data['payment_method'],
                'note'           => $data['note'] ?? null,
                'date'           => $data['date'],
            ];

            if (!empty($data['bank_name'])) {
                if (Schema::hasColumn('sale_payments', 'bank_name')) {
                    $payload['bank_name'] = $data['bank_name'];
                } else {
                    $payload['note'] = trim(
                        ($payload['note'] ? $payload['note'].'; ' : '') . 'Bank: '.$data['bank_name']
                    );
                }
            }

            $pay = new SalePayment();
            $pay->sale_id        = $sale->id;
            $pay->reference      = $reference;
            $pay->amount         = $amount;
            $pay->payment_method = $data['payment_method'];
            $pay->note           = $payload['note'] ?? null;
            $pay->date           = $data['date'];
            if (Schema::hasColumn('sale_payments', 'bank_name')) {
                $pay->bank_name = $data['bank_name'] ?? null;
            }
            $pay->save();

            $sale->recalcPaymentAndStatus();

            DB::commit();

            return response()->json([
                'ok' => true,
                'payment' => [
                    'id'             => $pay->id,
                    'reference'      => $pay->reference,
                    'amount'         => (int)$pay->amount,
                    'payment_method' => $pay->payment_method,
                    'bank_name'      => $pay->bank_name ?? null,
                    'note'           => $pay->note,
                    'date'           => $pay->date,
                ],
                'summary' => $sale->toMoneySummary(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok'=>false,'message'=>$e->getMessage()], 422);
        }
    }

    public function ajaxUpdate(Request $request, SalePayment $salePayment)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $request->merge(['amount' => (int) $request->input('amount')]);
        $data = $request->validate([
            'date'           => 'required|date',
            'payment_method' => 'required|in:Tunai,Transfer,QRIS',
            'amount'         => 'required|integer|min:1',
            'note'           => 'nullable|string|max:255',
            'bank_name'      => 'nullable|string|max:150',
        ]);

        $sale = Sale::lockForUpdate()->findOrFail($salePayment->sale_id);

        try {
            DB::beginTransaction();

            $headroom = (int) $sale->due_amount + (int) $salePayment->amount;
            $amount   = min(max(1, (int)$data['amount']), $headroom);

            $salePayment->payment_method = $data['payment_method'];
            $salePayment->amount         = $amount;
            $salePayment->note           = $data['note'] ?? null;
            $salePayment->date           = $data['date'];

            if (Schema::hasColumn('sale_payments', 'bank_name')) {
                $salePayment->bank_name = $data['bank_name'] ?? null;
            } else {
                if (!empty($data['bank_name'])) {
                    $salePayment->note = trim(
                        ($salePayment->note ? $salePayment->note.'; ' : '') . 'Bank: '.$data['bank_name']
                    );
                }
            }

            $salePayment->save();

            $sale->recalcPaymentAndStatus();

            DB::commit();

            return response()->json([
                'ok' => true,
                'payment' => [
                    'id'             => $salePayment->id,
                    'reference'      => $salePayment->reference,
                    'amount'         => (int)$salePayment->amount,
                    'payment_method' => $salePayment->payment_method,
                    'bank_name'      => $salePayment->bank_name ?? null,
                    'note'           => $salePayment->note,
                    'date'           => $salePayment->date,
                ],
                'summary' => $sale->toMoneySummary(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok'=>false,'message'=>$e->getMessage()], 422);
        }
    }

    public function ajaxDestroy(SalePayment $salePayment)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $sale = Sale::lockForUpdate()->findOrFail($salePayment->sale_id);

        try {
            DB::beginTransaction();

            $salePayment->delete();

            $sale->recalcPaymentAndStatus();

            DB::commit();

            return response()->json([
                'ok' => true,
                'summary' => $sale->toMoneySummary(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok'=>false,'message'=>$e->getMessage()], 422);
        }
    }

    public function ajaxSummary(Sale $sale)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);
        $sale->refresh();
        return response()->json([
            'ok'=>true,
            'summary'=>$sale->toMoneySummary(),
        ]);
    }
}