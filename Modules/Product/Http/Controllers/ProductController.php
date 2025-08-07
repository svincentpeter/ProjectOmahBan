<?php

namespace Modules\Product\Http\Controllers;

use Modules\Product\DataTables\ProductDataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Brand;
// Hapus 'ProductCategory' karena duplikat dengan 'Category'
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
        $brands = Brand::all();

        return view('product::products.create', compact('categories', 'brands'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(StoreProductRequest $request)
    {
        // Perintah $request->validated() secara otomatis menjalankan semua aturan
        // di file `StoreProductRequest.php`. Jika valid, ia akan mengembalikan
        // array berisi semua data yang sudah bersih dan aman.
        // Anda tidak perlu lagi menulis validasi manual di sini.
        $validatedData = $request->validated();

        // Logika bisnis: Saat produk baru dibuat, Stok Sisa (product_quantity)
        // nilainya sama dengan Stok Awal yang diinput.
        $validatedData['product_quantity'] = $validatedData['stok_awal'];

        // Karena semua nama kolom di form sudah sesuai dengan nama kolom di database,
        // dan sudah kita daftarkan di $fillable pada model Product,
        // kita bisa langsung memasukkan semua data tervalidasi sekaligus.
        // Ini lebih singkat, aman, dan mudah dirawat.
        $product = Product::create($validatedData);

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

        // Eager load relasi: Ambil semua data 'adjustedProducts'
        // dan untuk setiap item, ambil juga data 'adjustment' induknya (yang berisi note).
        $product->load('adjustedProducts.adjustment');

        return view('product::products.show', compact('product'));
    }

    public function edit(Product $product) {
        abort_if(Gate::denies('edit_products'), 403);

        $categories = Category::all();
        $brands = Brand::all();

        return view('product::products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Memperbarui data produk yang sudah ada.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        // Sama seperti store(), method ini mengambil semua data yang sudah lolos
        // validasi dari file `UpdateProductRequest.php`.
        $validatedData = $request->validated();

        // Logika cerdas untuk update stok sisa:
        // Jika admin mengubah nilai "Stok Awal", maka "Stok Sisa" harus ikut disesuaikan.
        if (isset($validatedData['stok_awal'])) {
            // 1. Hitung selisih antara stok awal yang baru dengan yang lama.
            $selisihStokAwal = $validatedData['stok_awal'] - $product->stok_awal;
            
            // 2. Tambahkan selisih tersebut ke stok sisa saat ini.
            // Ini membuat stok tetap akurat meskipun stok awal diubah.
            $validatedData['product_quantity'] = $product->product_quantity + $selisihStokAwal;
        }

        // Langsung update semua data yang tervalidasi.
        $product->update($validatedData);

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
            
            $path = $file->storeAs('uploads/products', $filename, 'public');

            return response()->json(['name' => $filename, 'path' => $path]);
        }

        return response()->json(['error' => 'Upload failed.'], 400);
    }
}