<?php

namespace Modules\Product\Http\Controllers;

use Modules\Product\DataTables\ProductDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Product\Entities\Brand;
use Modules\Adjustment\Entities\StockMovement; // BARU: Import untuk record opening

class ProductController extends Controller
{
    public function index(ProductDataTable $dataTable)
    {
        abort_if(Gate::denies('access_products'), 403);

        $products = Product::query()
            ->active() // cukup filter aktif
            ->with(['category', 'brand', 'stockMovements'])
            ->paginate(10);

        return $dataTable->render('product::products.index', compact('products'));
    }

    public function create()
    {
        abort_if(Gate::denies('create_products'), 403);
        $categories = Category::all();
        $brands = Brand::all();
        return view('product::products.create', compact('categories', 'brands'));
    }

    public function store(StoreProductRequest $request)
    {
        abort_if(Gate::denies('create_products'), 403);
        $validatedData = $request->validated();
        $validatedData['is_active'] = true;
        $validatedData['stokawal'] = $validatedData['stokawal'] ?? 0; // Default opening
        $product = Product::create($validatedData);

        // UPDATE: Record opening balance ke ledger (in)
        if ($validatedData['stokawal'] > 0) {
            StockMovement::record($product, 'in', $validatedData['stokawal'], 'Opening stock baru', null, 'opening');
        }

        // Existing upload gambar (tetap)
        if ($request->has('document') && is_array($request->document) && count($request->document) > 0) {
            foreach ($request->document as $filename) {
                $filePath = storage_path('app/temp/dropzone/' . $filename);
                if (file_exists($filePath)) {
                    try {
                        $product->addMedia($filePath)->preservingOriginal()->toMediaCollection('images');
                        info('Image uploaded: ' . $filename);
                        unlink($filePath);
                        info('Temp file deleted: ' . $filename);
                    } catch (\Exception $e) {
                        error('Error uploading image: ' . $e->getMessage());
                        error('File path: ' . $filePath);
                    }
                } else {
                    warning('File not found: ' . $filePath);
                }
            }
        }

        toast('Produk Baru Berhasil Dibuat!', 'success');
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('show_products'), 403);
        // UPDATE: Eager load lengkap
        $product->load(['category', 'brand', 'adjustedProducts.adjustment', 'stockMovements']);
        return view('product::products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('edit_products'), 403);
        $categories = Category::all();
        $brands = Brand::all();
        return view('product::products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        abort_if(Gate::denies('edit_products'), 403);
        $validatedData = $request->validated();
        // UPDATE: Tambah condition, is_active
        $oldStokawal = $product->stokawal;
        $product->update($validatedData);

        // UPDATE: Jika stokawal berubah, record adjustment
        if (isset($validatedData['stokawal']) && $validatedData['stokawal'] != $oldStokawal) {
            $delta = $validatedData['stokawal'] - $oldStokawal;
            $type = $delta > 0 ? 'in' : 'out';
            $qty = abs($delta);
            StockMovement::record($product, $type, $qty, 'Adjustment stokawal', null, 'adjustment');
        }

        // Existing upload (tetap, tapi clear lama jika ada)
        if ($request->has('document') && is_array($request->document) && count($request->document) > 0) {
            $product->clearMediaCollection('images');
            foreach ($request->document as $filename) {
                $filePath = storage_path('app/temp/dropzone/' . $filename);
                if (file_exists($filePath)) {
                    try {
                        $product->addMedia($filePath)->preservingOriginal()->toMediaCollection('images');
                        unlink($filePath);
                    } catch (\Exception $e) {
                        error('Error uploading image: ' . $e->getMessage());
                    }
                }
            }
        }

        toast('Produk Berhasil Diperbarui!', 'success');
        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        abort_if(Gate::denies('delete_products'), 403);
        // UPDATE: Soft delete (sudah trait)
        $product->delete();
        toast('Produk Berhasil Dihapus!', 'warning');
        return redirect()->route('products.index');
    }

    // Existing uploadImage & deleteImage (tetap)
    public function uploadImage(Request $request)
    {
        $request->validate(['file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $tempPath = storage_path('app/temp/dropzone');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $file->move($tempPath, $filename);
            info('File uploaded to temp: ' . $filename);
            return response()->json([
                'name' => $filename,
                'path' => 'temp/dropzone/' . $filename,
                'size' => filesize($tempPath . '/' . $filename),
            ]);
        }
        return response()->json(['error' => 'Upload failed.'], 400);
    }

    public function deleteImage(Request $request)
    {
        $filename = $request->input('filename');
        $filePath = storage_path('app/temp/dropzone/' . $filename);
        if (file_exists($filePath)) {
            unlink($filePath);
            info('Temp file deleted via AJAX: ' . $filename);
            return response()->json(['success' => true, 'message' => 'File deleted']);
        }
        warning('File not found for deletion: ' . $filePath);
        return response()->json(['error' => 'File not found'], 404);
    }
}
