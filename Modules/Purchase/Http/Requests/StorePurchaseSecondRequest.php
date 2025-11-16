<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StorePurchaseSecondRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create_purchases');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Main purchase info
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255|unique:purchase_seconds,reference',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            
            // Status
            'status' => 'required|in:Pending,Completed',
            
            // Payment info
            'payment_method' => 'required|in:Tunai,Transfer',
            'bank_name' => 'required_if:payment_method,Transfer|nullable|string|max:100',
            
            // Amounts
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            
            // Note
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
            'date.required' => 'Tanggal pembelian harus diisi.',
            'date.date' => 'Format tanggal tidak valid.',
            
            'reference.unique' => 'Reference sudah digunakan.',
            
            'customer_name.required' => 'Nama customer harus diisi.',
            'customer_name.max' => 'Nama customer maksimal 255 karakter.',
            
            'customer_phone.max' => 'Nomor HP maksimal 50 karakter.',
            
            'status.required' => 'Status pembelian harus dipilih.',
            'status.in' => 'Status harus Pending atau Completed.',
            
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_method.in' => 'Metode pembayaran harus Tunai atau Transfer.',
            
            'bank_name.required_if' => 'Nama bank harus diisi jika metode Transfer.',
            'bank_name.max' => 'Nama bank maksimal 100 karakter.',
            
            'total_amount.required' => 'Total pembelian harus diisi.',
            'total_amount.numeric' => 'Total pembelian harus berupa angka.',
            'total_amount.min' => 'Total pembelian tidak boleh negatif.',
            
            'paid_amount.required' => 'Jumlah bayar harus diisi.',
            'paid_amount.numeric' => 'Jumlah bayar harus berupa angka.',
            'paid_amount.min' => 'Jumlah bayar tidak boleh negatif.',
            
            'due_amount.required' => 'Sisa yang belum dibayar harus diisi.',
            'due_amount.numeric' => 'Sisa harus berupa angka.',
            'due_amount.min' => 'Sisa tidak boleh negatif.',
            
            'note.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'date' => 'tanggal',
            'reference' => 'reference',
            'customer_name' => 'nama customer',
            'customer_phone' => 'nomor HP',
            'status' => 'status',
            'payment_method' => 'metode pembayaran',
            'bank_name' => 'nama bank',
            'total_amount' => 'total pembelian',
            'paid_amount' => 'jumlah bayar',
            'due_amount' => 'sisa',
            'note' => 'catatan',
        ];
    }
}
