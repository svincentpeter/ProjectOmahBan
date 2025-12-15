<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateQuotationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'tax_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'shipping_amount' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Pending,Sent,Accepted,Rejected,Converted'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return Gate::allows('edit_sales'); 
    }
}
