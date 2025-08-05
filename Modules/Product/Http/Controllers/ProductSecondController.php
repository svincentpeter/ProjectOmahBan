<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\ProductSecond;
use Illuminate\Support\Facades\Storage;

class ProductSecondController extends Controller
{
    /**
     * Menampilkan daftar produk bekas.
     */
    public function index()
    {
        $products = ProductSecond::latest()->paginate(10);
        return view('product::second.index', compact('products'));
    }

    /**
     * Menampilkan form tambah produk bekas.
     */
    public function create()
    {
        return view('product::second.create');
    }

    /**
     * Menyimpan data produk bekas baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'unique_code'     => 'required|string|unique:product_seconds,unique_code',
            'purchase_price'  => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
            'condition_notes' => 'nullable|string',
            'photo'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products_second_photos', 'public');
            $data['photo_path'] = $path;
        }

        $data['status'] = 'available';
        ProductSecond::create($data);

        return redirect()->route('products_second.index')->with('success', 'Produk bekas berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail produk bekas.
     */
    public function show($id)
    {
        $product = ProductSecond::findOrFail($id);
        return view('product::second.show', compact('product'));
    }

    /**
     * Menampilkan form edit produk bekas.
     */
    public function edit($id)
    {
        $product = ProductSecond::findOrFail($id);
        return view('product::second.edit', compact('product'));
    }

    /**
     * Update data produk bekas.
     */
    public function update(Request $request, $id)
    {
        $product = ProductSecond::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'unique_code'     => 'required|string|unique:product_seconds,unique_code,' . $id,
            'purchase_price'  => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
            'condition_notes' => 'nullable|string',
            'photo'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('photo')) {
            // Hapus file lama jika ada
            if ($product->photo_path && Storage::disk('public')->exists($product->photo_path)) {
                Storage::disk('public')->delete($product->photo_path);
            }
            $path = $request->file('photo')->store('products_second_photos', 'public');
            $data['photo_path'] = $path;
        }

        $product->update($data);

        return redirect()->route('products_second.index')->with('success', 'Produk bekas berhasil diperbarui.');
    }

    /**
     * Hapus produk bekas (soft delete).
     */
    public function destroy($id)
    {
        $product = ProductSecond::findOrFail($id);

        // Hapus foto di storage jika ada
        if ($product->photo_path && Storage::disk('public')->exists($product->photo_path)) {
            Storage::disk('public')->delete($product->photo_path);
        }

        $product->delete();

        return redirect()->route('products_second.index')->with('success', 'Produk bekas berhasil dihapus.');
    }
}
