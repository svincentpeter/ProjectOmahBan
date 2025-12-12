<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Product\Entities\Category;
use Modules\Product\DataTables\ProductCategoriesDataTable;

class CategoriesController extends Controller
{

    public function index(Request $request) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $query = Category::query()->orderBy('category_name');

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('category_code', 'like', "%{$search}%")
                  ->orWhere('category_name', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(15);

        return view('product::categories.index', compact('categories'));
    }


    public function store(Request $request) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $request->validate([
            'category_code' => 'required|unique:categories,category_code',
            'category_name' => 'required'
        ]);

        $category = Category::create([
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
        ]);

        // Handle AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori produk berhasil ditambahkan!',
                'category' => $category
            ]);
        }

        toast('Product Category Created!', 'success');
        return redirect()->back();
    }


    public function edit($id) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $category = Category::findOrFail($id);

        return view('product::categories.edit', compact('category'));
    }


    public function update(Request $request, $id) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $request->validate([
            'category_code' => 'required|unique:categories,category_code,' . $id,
            'category_name' => 'required'
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
        ]);

        // Handle AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori produk berhasil diperbarui!',
                'category' => $category
            ]);
        }

        toast('Product Category Updated!', 'info');
        return redirect()->route('product-categories.index');
    }


    public function destroy($id) {
        abort_if(Gate::denies('access_product_categories'), 403);

        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            // Handle AJAX request for error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus karena ada produk yang menggunakan kategori ini.'
                ], 422);
            }
            return back()->withErrors('Can\'t delete because there are products associated with this category.');
        }

        $category->delete();

        // Handle AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori produk berhasil dihapus!'
            ]);
        }

        toast('Product Category Deleted!', 'warning');
        return redirect()->route('product-categories.index');
    }
}
