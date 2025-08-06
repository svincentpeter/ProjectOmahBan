<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Brand;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('access_products'), 403);
        $brands = Brand::latest()->paginate(10);
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
        Brand::create(['name' => $request->name]);
        
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

        toast('Merek Berhasil Diperbarui!', 'success');
        return redirect()->route('brands.index');
    }

    public function destroy(Brand $brand)
    {
        abort_if(Gate::denies('delete_products'), 403);
        $brand->delete();

        toast('Merek Berhasil Dihapus!', 'success');
        return redirect()->route('brands.index');
    }
}