<?php

namespace Modules\Expense\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'date'           => ['required','date'],
            'category_id'    => ['required','exists:expense_categories,id'],
            'details'        => ['required','string','max:255'],
            'amount'         => ['required','integer','min:0'],
            'payment_method' => ['required','in:Tunai,Transfer'],
            'bank_name' => 'required_if:payment_method,Transfer|nullable|string|max:100',
            'attachment'     => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:2048'],
        ];
    }
}
