<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sale_id' => 'required|exists:sales,id',
            'date' => 'nullable|date',
            'refund_method' => 'nullable|in:Cash,Credit,Store Credit',
            'refund_amount' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.sale_detail_id' => 'nullable|exists:sale_details,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_code' => 'nullable|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.source_type' => 'nullable|in:new,second,service,manual',
            'items.*.reason' => 'nullable|string|max:500',
            'items.*.condition' => 'nullable|in:good,damaged,defective',
            'items.*.restock' => 'nullable|boolean',
            'items.*.productable_type' => 'nullable|string',
            'items.*.productable_id' => 'nullable|integer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sale_id.required' => 'Pilih transaksi penjualan yang akan diretur.',
            'sale_id.exists' => 'Transaksi penjualan tidak ditemukan.',
            'items.required' => 'Minimal satu item harus ditambahkan.',
            'items.min' => 'Minimal satu item harus ditambahkan.',
            'items.*.product_name.required' => 'Nama produk wajib diisi.',
            'items.*.quantity.required' => 'Jumlah item wajib diisi.',
            'items.*.quantity.min' => 'Jumlah item minimal 1.',
            'items.*.unit_price.required' => 'Harga satuan wajib diisi.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'sale_id' => 'transaksi',
            'items.*.product_name' => 'nama produk',
            'items.*.quantity' => 'jumlah',
            'items.*.unit_price' => 'harga satuan',
        ];
    }
}
