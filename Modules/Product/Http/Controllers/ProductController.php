<?php

namespace Modules\Product\Http\Controllers;

use Modules\Product\DataTables\ProductDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Brand;
use Modules\Product\Entities\ProductCategory; // <-- Tambahkan Import Brand
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Upload\Entities\Upload;

class ProductController extends Controller
{
    public function index(ProductDataTable $dataTable) {
        abort_if(Gate::denies('access_products'), 403);

        return $dataTable->render('product::products.index');
    }

    public function create() {
        abort_if(Gate::denies('create_products'), 403);

        $categories = Category::all();
        $brands = Brand::all(); // <-- Kirim data merek ke view

        return view('product::products.create', compact('categories', 'brands'));
    }

    public function store(StoreProductRequest $request)
{
    $validatedData = $request->validated();

    $product = Product::create([
        'product_name' => $validatedData['product_name'],
        'product_code' => $validatedData['product_code'],
        'category_id' => $validatedData['category_id'],
        'brand_id' => $validatedData['brand_id'] ?? null,
        'product_cost' => $validatedData['product_cost'],
        'product_price' => $validatedData['product_price'],
        'product_quantity' => $validatedData['product_quantity'], // <-- TAMBAHKAN INI
        'product_unit' => $validatedData['product_unit'],
        'product_stock_alert' => $validatedData['product_stock_alert'],
        'product_note' => $validatedData['product_note'],
    ]);

    if ($request->has('document') && count($request->document) > 0) {
        foreach ($request->document as $file) {
            $product->addMedia(Storage::path('temp/dropzone/' . $file))->toMediaCollection('images');
        }
    }

    toast('Produk Baru Berhasil Dibuat!', 'success');
    return redirect()->route('products.index');
}

    public function show(Product $product) {
        abort_if(Gate::denies('show_products'), 403);

        return view('product::products.show', compact('product'));
    }

    public function edit(Product $product) {
        abort_if(Gate::denies('edit_products'), 403);

        $categories = Category::all();
        $brands = Brand::all(); // <-- Kirim data merek ke view

        return view('product::products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(UpdateProductRequest $request, Product $product)
{
    $validatedData = $request->validated();

    $product->update([
        'product_name' => $validatedData['product_name'],
        'product_code' => $validatedData['product_code'],
        'category_id' => $validatedData['category_id'], // <-- DIPERBAIKI
        'brand_id' => $validatedData['brand_id'] ?? null,
        'product_cost' => $validatedData['product_cost'],
        'product_price' => $validatedData['product_price'],
        'product_quantity' => $validatedData['product_quantity'],
        'product_unit' => $validatedData['product_unit'],
        'product_stock_alert' => $validatedData['product_stock_alert'],
        'product_note' => $validatedData['product_note'],
    ]);

    if ($request->has('document') && count($request->document) > 0) {
        $product->clearMediaCollection('images');
        foreach ($request->document as $file) {
            $product->addMedia(Storage::path('temp/dropzone/' . $file))->toMediaCollection('images');
        }
    }

    toast('Produk Berhasil Diperbarui!', 'success');
    return redirect()->route('products.index');
}

    public function destroy(Product $product) {
        abort_if(Gate::denies('delete_products'), 403);

        $product->delete();

        toast('Product Deleted!', 'warning');
        return redirect()->route('products.index');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Simpan file ke storage/app/public/uploads/products
            $path = $file->storeAs('uploads/products', $filename, 'public');

            // Kembalikan response JSON yang dibutuhkan oleh Dropzone
            return response()->json(['name' => $filename, 'path' => $path]);
        }

        return response()->json(['error' => 'Upload failed.'], 400);
    }
}
