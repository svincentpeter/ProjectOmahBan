<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Product\Entities\Brand;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\ProductSecond;

class ProductSecondController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('access_products'), 403);

        $products = ProductSecond::with(['category', 'brand'])
            ->orderByDesc('created_at')
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
            'unique_code' => 'required|string|max:255|unique:product_seconds,unique_code',
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'size' => 'nullable|string|max:255',
            'ring' => 'nullable|string|max:50',
            'product_year' => 'nullable|integer|digits:4',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'condition_notes' => 'nullable|string',
            'status' => 'nullable|in:available,sold',
        ]);

        // default status second = available
        if (empty($validated['status'])) {
            $validated['status'] = 'available';
        }

        $product = ProductSecond::create($validated);

        // handle upload foto (dropzone)
        if ($request->has('document') && is_array($request->document) && count($request->document) > 0) {
            foreach ($request->document as $file) {
                $filePath = storage_path('temp/dropzone/' . $file);

                if (file_exists($filePath)) {
                    $product->addMedia($filePath)->toMediaCollection('images');
                    @unlink($filePath);
                }
            }
        }

        toast('Produk Bekas berhasil ditambahkan!', 'success');

        return redirect()->route('products_second.index');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit_products'), 403);

        $product = ProductSecond::findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();

        return view('product::second.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit_products'), 403);

        $product = ProductSecond::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unique_code' => 'required|string|max:255|unique:product_seconds,unique_code,' . $id,
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'size' => 'nullable|string|max:255',
            'ring' => 'nullable|string|max:50',
            'product_year' => 'nullable|integer|digits:4',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'condition_notes' => 'nullable|string',
            'status' => 'required|in:available,sold',
        ]);

        $product->update($validated);

        // replace foto jika ada kiriman baru
        if ($request->has('document') && is_array($request->document) && count($request->document) > 0) {
            $product->clearMediaCollection('images');

            foreach ($request->document as $file) {
                $filePath = storage_path('temp/dropzone/' . $file);

                if (file_exists($filePath)) {
                    $product->addMedia($filePath)->toMediaCollection('images');
                    @unlink($filePath);
                }
            }
        }

        toast('Produk Bekas berhasil diperbarui!', 'info');

        return redirect()->route('products_second.index');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete_products'), 403);

        $product = ProductSecond::findOrFail($id);
        $product->delete();

        toast('Produk Bekas berhasil dihapus!', 'warning');

        return redirect()->route('products_second.index');
    }
}
