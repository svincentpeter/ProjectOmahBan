<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;

class ProductApiController extends Controller
{
    /**
     * Get all products
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->whereNull('deleted_at')
            ->select([
                'id',
                'product_code',
                'product_name',
                'product_quantity',
                'product_cost',
                'product_price',
                'product_stock_alert',
                'category_id',
                'brand_id',
                'is_active'
            ]);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get single product
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Get low stock products
     */
    public function lowStock()
    {
        $products = Product::query()
            ->whereNull('deleted_at')
            ->whereNotNull('product_stock_alert')
            ->where('product_stock_alert', '>', 0)
            ->whereColumn('product_quantity', '<=', 'product_stock_alert')
            ->orderByRaw('product_quantity - product_stock_alert ASC')
            ->get([
                'id',
                'product_code',
                'product_name',
                'product_quantity',
                'product_stock_alert'
            ]);

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'data' => $products
        ]);
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'operation' => 'required|in:set,add,subtract'
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $quantity = $request->quantity;
        $operation = $request->operation;

        switch ($operation) {
            case 'add':
                $product->product_quantity += $quantity;
                break;
            case 'subtract':
                $product->product_quantity -= $quantity;
                break;
            case 'set':
            default:
                $product->product_quantity = $quantity;
                break;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'new_quantity' => $product->product_quantity
            ]
        ]);
    }
}
