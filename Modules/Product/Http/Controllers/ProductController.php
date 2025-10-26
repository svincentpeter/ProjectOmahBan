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

class ProductController extends Controller
{
    public function index(ProductDataTable $dataTable)
    {
        abort_if(Gate::denies('access_products'), 403);

        return $dataTable->render('product::products.index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_products'), 403);

        $categories = Category::all();
        $brands = Brand::all();

        return view('product::products.create', compact('categories', 'brands'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        // Logika bisnis: Stok Sisa = Stok Awal saat produk baru dibuat
        $validatedData['product_quantity'] = $validatedData['stok_awal'];

        $product = Product::create($validatedData);

        // ===== PERBAIKAN UPLOAD GAMBAR =====
        if ($request->has('document') && is_array($request->document) && count($request->document) > 0) {
            foreach ($request->document as $filename) {
                $filePath = storage_path('app/temp/dropzone/' . $filename);

                // Cek apakah file benar-benar ada
                if (file_exists($filePath)) {
                    try {
                        // addMedia dengan COPY bukan MOVE (preserveOriginal)
                        $product
                            ->addMedia($filePath)
                            ->preservingOriginal() // Jangan move file, copy saja
                            ->toMediaCollection('images');

                        \Log::info('✅ Image uploaded: ' . $filename);

                        // Hapus file temp SETELAH berhasil copy
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                            \Log::info('🗑️ Temp file deleted: ' . $filename);
                        }
                    } catch (\Exception $e) {
                        \Log::error('❌ Error uploading image: ' . $e->getMessage());
                        \Log::error('File path: ' . $filePath);
                    }
                } else {
                    \Log::warning('⚠️ File not found: ' . $filePath);
                }
            }
        }

        toast('Produk Baru Berhasil Dibuat!', 'success');
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('show_products'), 403);

        return view('product::products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('edit_products'), 403);

        $categories = Category::all();
        $brands = Brand::all();

        return view('product::products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update produk existing di database.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validatedData = $request->validated();

        // Logika penyesuaian stok otomatis
        if (isset($validatedData['stok_awal'])) {
            $selisihStokAwal = $validatedData['stok_awal'] - $product->stok_awal;
            $validatedData['product_quantity'] = $product->product_quantity + $selisihStokAwal;
        }

        $product->update($validatedData);

        // ===== PERBAIKAN UPLOAD GAMBAR =====
        if ($request->has('document') && is_array($request->document) && count($request->document) > 0) {
            // Hapus gambar lama terlebih dahulu
            $product->clearMediaCollection('images');

            foreach ($request->document as $filename) {
                $filePath = storage_path('app/temp/dropzone/' . $filename);

                // Cek apakah file ada
                if (file_exists($filePath)) {
                    try {
                        // Upload dengan preservingOriginal untuk avoid move issues
                        $product->addMedia($filePath)->preservingOriginal()->toMediaCollection('images');

                        \Log::info('✅ Image uploaded: ' . $filename);

                        // Hapus temp file setelah berhasil
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                            \Log::info('🗑️ Temp file deleted: ' . $filename);
                        }
                    } catch (\Exception $e) {
                        \Log::error('❌ Error uploading image: ' . $e->getMessage());
                        \Log::error('File path: ' . $filePath);
                    }
                } else {
                    \Log::warning('⚠️ File not found: ' . $filePath);
                }
            }
        }

        toast('Produk Berhasil Diperbarui!', 'success');
        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        abort_if(Gate::denies('delete_products'), 403);

        $product->delete();

        toast('Produk Berhasil Dihapus!', 'warning');

        return redirect()->route('products.index');
    }

    /**
     * Upload image via Dropzone
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Path ke temp folder
            $tempPath = storage_path('app/temp/dropzone');

            // Buat folder jika belum ada
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            // Move file ke temp folder
            $file->move($tempPath, $filename);

            \Log::info('📤 File uploaded to temp: ' . $filename);

            return response()->json([
                'name' => $filename,
                'path' => 'temp/dropzone/' . $filename,
                'size' => filesize($tempPath . '/' . $filename),
            ]);
        }

        return response()->json(['error' => 'Upload failed.'], 400);
    }

    /**
     * Delete image from temp folder
     */
    public function deleteImage(Request $request)
    {
        $filename = $request->input('filename');
        $filePath = storage_path('app/temp/dropzone/' . $filename);

        if (file_exists($filePath)) {
            @unlink($filePath);
            \Log::info('🗑️ Temp file deleted via AJAX: ' . $filename);
            return response()->json(['success' => true, 'message' => 'File deleted']);
        }

        \Log::warning('⚠️ File not found for deletion: ' . $filePath);
        return response()->json(['error' => 'File not found'], 404);
    }
}
