<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize()
    {
        // Mengikuti file kamu sebelumnya
        return Gate::allows('edit_sales');
    }

    public function rules()
    {
        return [
            // Header
            'date' => ['required', 'date'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'string', 'max:191'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:500'],

            // Angka (dinormalisasi)
            'shipping_amount' => ['nullable', 'integer', 'min:0'],
            'tax_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tax_amount' => ['nullable', 'integer', 'min:0'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],

            // Pembayaran (opsional)
            'payment_method' => ['nullable', 'string', 'max:50'],
            'bank_name' => [
                'nullable',
                'string',
                'max:50',
                Rule::requiredIf(function () {
                    return $this->input('payment_method') === 'Transfer';
                }),
            ],

            // ===== Details (wajib jika create manual)
            'details' => ['required', 'array', 'min:1'],
            'details.*.product_id' => ['nullable', 'integer', 'min:1'],
            'details.*.productable_id' => ['nullable', 'integer', 'min:1'],
            'details.*.productable_type' => ['nullable', 'string', 'max:191'],
            'details.*.source_type' => ['nullable', 'string', 'in:new,second,manual'],
            'details.*.manual_kind' => [
                Rule::requiredIf(function () {
                    return $this->isManualRow();
                }),
                'nullable',
                'string',
                'max:50',
            ],
            'details.*.product_name' => ['required', 'string', 'max:255'],
            'details.*.product_code' => ['nullable', 'string', 'max:100'],
            'details.*.quantity' => ['required', 'integer', 'min:1'],

            // harga & biaya
            'details.*.price' => ['required', 'integer', 'min:0'],
            'details.*.hpp' => ['nullable', 'integer', 'min:0'],
            'details.*.manual_hpp' => [
                Rule::requiredIf(function () {
                    return $this->isManualRow();
                }),
                'nullable',
                'integer',
                'min:0',
            ],
            'details.*.unit_price' => ['nullable', 'integer', 'min:0'],
            'details.*.sub_total' => ['nullable', 'integer', 'min:0'],
            'details.*.subtotal_profit' => ['nullable', 'integer'],

            // diskon/pajak per item
            'details.*.product_discount_amount' => ['nullable', 'integer', 'min:0'],
            'details.*.product_discount_type' => ['nullable', 'string', 'in:fixed,percent'],
            'details.*.product_tax_amount' => ['nullable', 'integer', 'min:0'],

            // edit harga
            'details.*.original_price' => ['nullable', 'integer', 'min:0'],
            'details.*.is_price_adjusted' => ['nullable', 'boolean'],
            'details.*.price_adjustment_amount' => ['nullable', 'integer'], // bisa negatif jika markup
            'details.*.price_adjustment_note' => ['nullable', 'string', 'max:255'],
            'details.*.adjusted_by' => ['nullable', 'integer', 'min:1'],
            'details.*.adjusted_at' => ['nullable', 'date'],
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
            $s = preg_replace('/[^\d\-]/', '', (string) $v);
            return (int) $s;
        };

        // Header numbers
        $merge = [];
        foreach (['shipping_amount', 'tax_amount', 'discount_amount', 'tax_percentage', 'discount_percentage'] as $k) {
            if ($this->has($k)) {
                $merge[$k] = $toInt($this->input($k));
            }
        }

        // Details numbers
        $details = $this->input('details', []);
        if (is_array($details)) {
            foreach ($details as $i => $row) {
                foreach (['quantity', 'price', 'hpp', 'manual_hpp', 'unit_price', 'sub_total', 'subtotal_profit', 'product_discount_amount', 'product_tax_amount', 'original_price', 'price_adjustment_amount'] as $numKey) {
                    if (array_key_exists($numKey, $row)) {
                        $details[$i][$numKey] = $toInt($row[$numKey]);
                    }
                }

                // Normalisasi enum
                if (isset($row['source_type'])) {
                    $st = strtolower((string) $row['source_type']);
                    $details[$i]['source_type'] = in_array($st, ['new', 'second', 'manual'], true) ? $st : 'new';
                }
                if (isset($row['product_discount_type'])) {
                    $dt = strtolower((string) $row['product_discount_type']);
                    $details[$i]['product_discount_type'] = in_array($dt, ['fixed', 'percent'], true) ? $dt : 'fixed';
                }

                // Hitung adjusted jika kosong
                $p = $details[$i]['price'] ?? null;
                $op = $details[$i]['original_price'] ?? null;
                if ($op === null && $p !== null) {
                    $details[$i]['original_price'] = $p;
                }
                if ($p !== null && $details[$i]['original_price'] !== null) {
                    $details[$i]['is_price_adjusted'] = (int) ($p !== $details[$i]['original_price']);
                    $details[$i]['price_adjustment_amount'] = (int) ($details[$i]['original_price'] - $p);
                }
            }
        }

        $merge['details'] = $details;
        $this->merge($merge);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $details = $this->input('details', []);
            foreach ($details as $idx => $row) {
                // Jika row manual → wajib manual_kind & manual_hpp
                if (($row['source_type'] ?? 'new') === 'manual') {
                    if (empty($row['manual_kind'])) {
                        $v->errors()->add("details.$idx.manual_kind", 'Jenis input manual wajib diisi.');
                    }
                    if (!isset($row['manual_hpp'])) {
                        $v->errors()->add("details.$idx.manual_hpp", 'HPP manual wajib diisi.');
                    }
                }
                // Jika ada potongan (adjustment_amount > 0) → wajib ada note
                $adj = (int) ($row['price_adjustment_amount'] ?? 0);
                if ($adj > 0 && empty($row['price_adjustment_note'])) {
                    $v->errors()->add("details.$idx.price_adjustment_note", 'Alasan diskon wajib diisi saat harga diturunkan.');
                }
                // Quantity & price minimal
                if ((int) ($row['quantity'] ?? 0) < 1) {
                    $v->errors()->add("details.$idx.quantity", 'Qty minimal 1.');
                }
                if ((int) ($row['price'] ?? -1) < 0) {
                    $v->errors()->add("details.$idx.price", 'Harga tidak boleh negatif.');
                }
            }
        });
    }

    private function isManualRow(): bool
    {
        $details = $this->input('details', []);
        foreach ($details as $r) {
            if (($r['source_type'] ?? 'new') === 'manual') {
                return true;
            }
        }
        return false;
    }

    public function messages()
    {
        return [
            'date.required' => 'Tanggal transaksi wajib diisi.',
            'details.required' => 'Detail item minimal 1 baris.',
            'details.*.product_name.required' => 'Nama produk wajib diisi.',
        ];
    }

    public function attributes()
    {
        return [
            'date' => 'tanggal',
            'details.*.product_name' => 'nama produk',
            'details.*.quantity' => 'jumlah',
            'details.*.price' => 'harga',
            'details.*.hpp' => 'HPP',
            'details.*.price_adjustment_note' => 'alasan diskon',
        ];
    }
}
