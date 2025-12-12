<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Brand;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('access_products'), 403);
        
        $query = Brand::query()->orderBy('name');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $brands = $query->withCount('products')->paginate(15);

        return view('product::brands.index', compact('brands'));
    }

    public function create()
    {
        abort_if(Gate::denies('create_products'), 403);
        return view('product::brands.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_products'), 403);
        
        $request->validate(['name' => 'required|string|unique:brands,name']);
        $brand = Brand::create(['name' => $request->name]);

        // Handle AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Merek baru berhasil ditambahkan!',
                'brand' => $brand
            ]);
        }

        toast('Merek Baru Berhasil Dibuat!', 'success');
        return redirect()->route('brands.index');
    }

    public function edit(Brand $brand)
    {
        abort_if(Gate::denies('edit_products'), 403);
        return view('product::brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        abort_if(Gate::denies('edit_products'), 403);
        
        $request->validate(['name' => 'required|string|unique:brands,name,' . $brand->id]);
        $brand->update(['name' => $request->name]);

        // Handle AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Merek berhasil diperbarui!',
                'brand' => $brand
            ]);
        }

        toast('Merek Berhasil Diperbarui!', 'success');
        return redirect()->route('brands.index');
    }

    public function destroy(Brand $brand)
    {
        abort_if(Gate::denies('delete_products'), 403);
        $brand->delete();

        // Handle AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Merek berhasil dihapus!'
            ]);
        }

        toast('Merek Berhasil Dihapus!', 'success');
        return redirect()->route('brands.index');
    }
}