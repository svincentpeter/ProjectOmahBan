<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StorePosSaleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tax_percentage' => 'required|integer|min:0|max:100',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'shipping_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:Tunai,Transfer,QRIS', // 👈 Tunai, Transfer, QRIS
            'bank_name' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        // Bank name HANYA required untuk Transfer
        $validator->sometimes('bank_name', 'required', function ($input) {
            return ($input->payment_method ?? null) === 'Transfer';
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create_pos_sales');
    }
}
