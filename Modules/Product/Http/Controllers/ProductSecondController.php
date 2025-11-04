<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Brand;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\ProductSecond;
use Modules\Adjustment\Entities\StockMovement;
use Modules\Product\Http\Requests\StoreProductSecondRequest; // Asumsi request
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\DB;

class ProductSecondController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('access_products'), 403);

        $products = ProductSecond::query()
            // kalau ingin hanya yang ready:
            ->where('status', 'available')
            ->with(['category', 'brand'])
            // contoh agregasi in/out dari stock_movements (polymorphic)
            ->withCount([
                'stockMovements as qty_in' => fn($q) => $q->where('type', 'in')->select(DB::raw('COALESCE(SUM(quantity),0)')),
                'stockMovements as qty_out' => fn($q) => $q->where('type', 'out')->select(DB::raw('COALESCE(SUM(quantity),0)')),
            ])
            ->paginate(10);

        return view('product::second.index', compact('products'));
    }

    public function create()
    {
        abort_if(Gate::denies('create_products'), 403);
        $categories = Category::all();
        $brands = Brand::all();
        return view('product::second.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_products'), 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'uniquecode' => 'required|string|unique:products,sku', // Map ke sku
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'size' => 'nullable|string|max:255',
            'ring' => 'nullable|string|max:50',
            'productyear' => 'nullable|integer|digits:4',
            'buy_price' => 'required|numeric|min:0', // purchaseprice → buy_price
            'sell_price' => 'required|numeric|min:0',
            'conditionnotes' => 'nullable|string',
        ]);
        // UPDATE: Map legacy ke unified, condition fixed 'second'
        $validated['condition'] = 'second';
        $validated['sku'] = $validated['uniquecode'];
        $validated['is_active'] = $validated['status'] ?? true; // available → active
        $validated['meta'] = ['notes' => $validated['conditionnotes'], 'size' => $validated['size'], 'ring' => $validated['ring'], 'year' => $validated['productyear']];
        $product = Product::create($validated);

        // UPDATE: Record in jika available (stokawal=1 default second)
        if (isset($validated['status']) && $validated['status'] === 'available') {
            StockMovement::record($product, 'in', 1, 'Second product available', null, 'opening');
        }

        // Existing upload (adapt ke unified media)
        if ($request->has('document') && count($request->document) > 0) {
            foreach ($request->document as $file) {
                $filePath = storage_path('temp/dropzone/' . $file);
                if (file_exists($filePath)) {
                    $product->addMedia($filePath)->toMediaCollection('images');
                    unlink($filePath);
                }
            }
        }

        toast('Produk Bekas berhasil ditambahkan!', 'success');
        return redirect()->route('products_second.index');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit_products'), 403);
        $product = Product::second()->findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        return view('product::second.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit_products'), 403);
        $product = Product::second()->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'uniquecode' => 'required|string|unique:products,sku,' . $id,
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'size' => 'nullable|string|max:255',
            'ring' => 'nullable|string|max:50',
            'productyear' => 'nullable|integer|digits:4',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'conditionnotes' => 'nullable|string',
        ]);
        // UPDATE: Map & update
        $validated['condition'] = 'second';
        $validated['sku'] = $validated['uniquecode'];
        $validated['is_active'] = $validated['status'] ?? true;
        $validated['meta'] = ['notes' => $validated['conditionnotes']]; // Update partial
        $product->update($validated);

        // Existing upload
        if ($request->has('document') && count($request->document) > 0) {
            $product->clearMediaCollection('images');
            foreach ($request->document as $file) {
                $filePath = storage_path('temp/dropzone/' . $file);
                if (file_exists($filePath)) {
                    $product->addMedia($filePath)->toMediaCollection('images');
                    unlink($filePath);
                }
            }
        }

        toast('Produk Bekas berhasil diperbarui!', 'info');
        return redirect()->route('products_second.index');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete_products'), 403);
        $product = Product::second()->findOrFail($id);
        $product->delete();
        toast('Produk Bekas berhasil dihapus!', 'warning');
        return redirect()->route('products_second.index');
    }
}
