<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSaleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reference'            => ['required', 'string'],
            'date'                 => ['required', 'date'],
            'status'               => ['required', 'in:Pending,Shipped,Completed'],
            'payment_method'       => ['required', 'in:Tunai,Transfer,QRIS'],
            'bank_name'            => ['nullable', 'string', 'max:100'],
            'shipping_amount'      => ['nullable', 'numeric', 'min:0'],
            'tax_percentage'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_percentage'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            // 'total_amount' => HAPUS
            'paid_amount'          => ['nullable', 'numeric', 'min:0'], // Hapus 'max:...'
            'note'                 => ['nullable', 'string'],
            'payment_method' => 'required|string|in:Tunai,Transfer,Kredit',
            'bank_name'      => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
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
        return Gate::allows('edit_sales');
    }
}
