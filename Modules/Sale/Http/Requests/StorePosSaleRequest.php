<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StorePosSaleRequest extends FormRequest
{
    public function authorize()
    {
        // Sesuai permission POS kamu
        return Gate::allows('create_pos_sales');
    }

    public function rules()
    {
        return [
            // Header dasar
            'date' => ['required', 'date'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'string', 'max:191'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:500'],

            // Angka (disiapkan di prepareForValidation)
            'shipping_amount' => ['nullable', 'integer', 'min:0'],
            'tax_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tax_amount' => ['nullable', 'integer', 'min:0'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],

            // Pembayaran (header ringkas; riwayat detail ada di sale_payments)
            'payment_method' => ['nullable', 'string', 'max:50'],
            'bank_name' => [
                'nullable',
                'string',
                'max:50',
                Rule::requiredIf(function () {
                    return $this->input('payment_method') === 'Transfer';
                }),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $toInt = function ($v) {
            if (is_null($v) || $v === '') {
                return null;
            }
            if (is_int($v)) {
                return $v;
            }
            // hilangkan pemisah ribuan/IDR
            $s = preg_replace('/[^\d\-]/', '', (string) $v);
            return (int) $s;
        };

        $merge = [];
        foreach (['shipping_amount', 'tax_amount', 'discount_amount', 'tax_percentage', 'discount_percentage'] as $k) {
            if ($this->has($k)) {
                $merge[$k] = $toInt($this->input($k));
            }
        }
        $this->merge($merge);
    }

    public function messages()
    {
        return [
            'date.required' => 'Tanggal transaksi wajib diisi.',
            'bank_name.required' => 'Nama bank wajib diisi bila metode pembayaran Transfer.',
        ];
    }

    public function attributes()
    {
        return [
            'date' => 'tanggal',
            'customer_name' => 'nama pelanggan',
            'shipping_amount' => 'ongkir',
            'payment_method' => 'metode pembayaran',
            'bank_name' => 'nama bank',
        ];
    }
}
