<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code,' . (optional($this->product)->id ?: 'NULL') . ',id',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'product_size' => 'nullable|string|max:255',
            
            // ===== KODE PERBAIKAN DIMULAI DI SINI =====
            'ring' => 'nullable|string|max:50', // Ditambahkan: Aturan untuk kolom ring
            'product_year' => 'nullable|integer|digits:4', // Ditambahkan: Aturan untuk tahun produksi
            'stok_awal' => 'required|integer|min:0', // Ditambahkan: Aturan untuk stok awal
            'product_quantity' => 'sometimes|integer|min:0', // 'sometimes' karena nilainya dihitung di controller
            // ===== KODE PERBAIKAN SELESAI DI SINI =====

            'product_cost' => 'required|numeric|min:0',
            'product_price' => 'required|numeric|min:0',
            'product_unit' => 'required|string',
            'product_stock_alert' => 'required|integer|min:0',
            'product_note' => 'nullable|string',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('edit_products');
    }
}