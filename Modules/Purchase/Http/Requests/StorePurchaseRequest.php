<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StorePurchaseRequest extends FormRequest
{
    /**
    {
        $amount = $this->total_amount;
        $paid = $this->paid_amount;

        // Clean formatting: Rp 1.000.000 -> 1000000
        $amount = str_replace(['Rp', '.', ' '], '', $amount);
        $paid = str_replace(['Rp', '.', ' '], '', $paid);

        $this->merge([
            'total_amount' => $amount,
            'paid_amount' => $paid,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date',
            'supplier_id' => 'required|numeric|exists:suppliers,id',
            'reference' => 'required|string|max:255',

            // Kolom yang disederhanakan - tidak ada tax, discount, shipping
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',

            'status' => 'required|string|in:Pending,Completed,Ordered',

            // Field baru untuk UMKM
            'payment_method' => 'required|string|in:Cash,Tunai,Transfer,Credit,Other',
            'bank_name' => 'required_if:payment_method,Transfer|nullable|string|max:100',

            'note' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'date.required' => 'Tanggal pembelian wajib diisi.',
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists' => 'Supplier yang dipilih tidak valid.',
            'reference.required' => 'Nomor referensi wajib diisi.',
            'total_amount.required' => 'Total pembelian wajib diisi.',
            'total_amount.min' => 'Total pembelian tidak boleh negatif.',
            'paid_amount.required' => 'Jumlah bayar wajib diisi.',
            'paid_amount.min' => 'Jumlah bayar tidak boleh negatif.',
            'status.required' => 'Status pembelian wajib dipilih.',
            'status.in' => 'Status pembelian harus Pending, Completed, atau Ordered.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran harus Cash, Tunai, Transfer, Credit, atau Other.',
            'bank_name.required_if' => 'Nama bank wajib diisi jika metode pembayaran Transfer.',
        ];
    }
}
