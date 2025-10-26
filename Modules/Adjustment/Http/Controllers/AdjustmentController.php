<?php

namespace Modules\Adjustment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Adjustment\Entities\Adjustment;
use Modules\Adjustment\Entities\AdjustedProduct;
use Modules\Product\Entities\Product;
use Modules\Adjustment\DataTables\AdjustmentsDataTable;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF; // ✅ Gunakan Snappy

class AdjustmentController extends Controller
{
    public function index(AdjustmentsDataTable $dataTable)
    {
        abort_if(Gate::denies('access_adjustments'), 403);
        return $dataTable->render('adjustment::index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_adjustments'), 403);
        return view('adjustment::create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        $validated = $request->validate([
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:1',
            'types' => 'required|array|min:1',
            'types.*' => 'required|in:add,sub',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $adjustment = Adjustment::create([
                    'date' => $validated['date'],
                    'note' => $validated['note'],
                ]);

                foreach ($validated['product_ids'] as $key => $productId) {
                    $product = Product::findOrFail($productId);
                    $quantity = $validated['quantities'][$key];
                    $type = $validated['types'][$key];

                    if ($type === 'sub' && $product->product_quantity < $quantity) {
                        throw new \Exception("Stok {$product->product_name} tidak mencukupi. Tersedia: {$product->product_quantity}, Dibutuhkan: {$quantity}");
                    }

                    AdjustedProduct::create([
                        'adjustment_id' => $adjustment->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'type' => $type,
                    ]);

                    $newQuantity = $type === 'add' ? $product->product_quantity + $quantity : $product->product_quantity - $quantity;

                    $product->update(['product_quantity' => $newQuantity]);
                }
            });

            toast('Penyesuaian Stok Berhasil Dibuat!', 'success');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            toast('Error: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    public function show(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);
        $adjustment->load(['adjustedProducts.product.category']);
        return view('adjustment::show', compact('adjustment'));
    }

    public function edit(Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);
        $adjustment->load(['adjustedProducts.product']);
        return view('adjustment::edit', compact('adjustment'));
    }

    public function update(Request $request, Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        $validated = $request->validate([
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:1',
            'types' => 'required|array|min:1',
            'types.*' => 'required|in:add,sub',
        ]);

        try {
            DB::transaction(function () use ($validated, $adjustment) {
                // Reverse old adjustments
                foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                    $product = Product::findOrFail($adjustedProduct->product_id);

                    $reverseQuantity = $adjustedProduct->type === 'add' ? $product->product_quantity - $adjustedProduct->quantity : $product->product_quantity + $adjustedProduct->quantity;

                    $product->update(['product_quantity' => $reverseQuantity]);
                }

                $adjustment->adjustedProducts()->delete();

                $adjustment->update([
                    'date' => $validated['date'],
                    'note' => $validated['note'],
                ]);

                foreach ($validated['product_ids'] as $key => $productId) {
                    $product = Product::findOrFail($productId);
                    $quantity = $validated['quantities'][$key];
                    $type = $validated['types'][$key];

                    if ($type === 'sub' && $product->product_quantity < $quantity) {
                        throw new \Exception("Stok {$product->product_name} tidak mencukupi!");
                    }

                    AdjustedProduct::create([
                        'adjustment_id' => $adjustment->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'type' => $type,
                    ]);

                    $newQuantity = $type === 'add' ? $product->product_quantity + $quantity : $product->product_quantity - $quantity;

                    $product->update(['product_quantity' => $newQuantity]);
                }
            });

            toast('Penyesuaian Stok Berhasil Diupdate!', 'info');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            toast('Error: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    public function destroy(Adjustment $adjustment)
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        try {
            DB::transaction(function () use ($adjustment) {
                foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                    $product = Product::findOrFail($adjustedProduct->product_id);

                    $reversedQuantity = $adjustedProduct->type === 'add' ? $product->product_quantity - $adjustedProduct->quantity : $product->product_quantity + $adjustedProduct->quantity;

                    if ($reversedQuantity < 0) {
                        throw new \Exception("Tidak dapat menghapus. Stok {$product->product_name} akan negatif!");
                    }

                    $product->update(['product_quantity' => $reversedQuantity]);
                }

                $adjustment->delete();
            });

            toast('Penyesuaian Stok Berhasil Dihapus!', 'warning');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            toast('Error: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * Generate PDF dengan Snappy (wkhtmltopdf)
     */
    public function pdf(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        // Load relationships
        $adjustment->load(['adjustedProducts.product.category']);

        // Generate PDF dengan Snappy
        $pdf = PDF::loadView('adjustment::print', compact('adjustment'))->setPaper('a4')->setOrientation('portrait')->setOption('margin-top', 10)->setOption('margin-right', 10)->setOption('margin-bottom', 10)->setOption('margin-left', 10)->setOption('enable-local-file-access', true); // Important untuk local assets

        // Stream (preview di browser) atau download
        return $pdf->inline('Penyesuaian_Stok_' . $adjustment->reference . '.pdf');

        // Atau gunakan download() jika ingin langsung download:
        // return $pdf->download('Penyesuaian_Stok_' . $adjustment->reference . '.pdf');
    }
}
