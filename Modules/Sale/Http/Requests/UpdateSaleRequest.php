<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('edit_sales');
    }

    public function rules()
    {
        return [
            // Header (opsional saat update)
            'date' => ['sometimes', 'date'],
            'customer_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'customer_email' => ['sometimes', 'nullable', 'string', 'max:191'],
            'customer_phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'note' => ['sometimes', 'nullable', 'string', 'max:500'],

            'shipping_amount' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'tax_percentage' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
            'tax_amount' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'discount_percentage' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
            'discount_amount' => ['sometimes', 'nullable', 'integer', 'min:0'],

            'payment_method' => ['sometimes', 'nullable', 'string', 'max:50'],
            'bank_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
                Rule::requiredIf(function () {
                    return $this->input('payment_method') === 'Transfer';
                }),
            ],

            // Details boleh ada saat update
            'details' => ['sometimes', 'array', 'min:1'],
            'details.*.id' => ['sometimes', 'integer', 'min:1'],
            'details.*.product_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'details.*.productable_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'details.*.productable_type' => ['sometimes', 'nullable', 'string', 'max:191'],
            'details.*.source_type' => ['sometimes', 'nullable', 'string', 'in:new,second,manual'],
            'details.*.manual_kind' => [
                Rule::requiredIf(function () {
                    return $this->isManualRow();
                }),
                'nullable',
                'string',
                'max:50',
            ],
            'details.*.product_name' => ['sometimes', 'required', 'string', 'max:255'],
            'details.*.product_code' => ['sometimes', 'nullable', 'string', 'max:100'],
            'details.*.quantity' => ['sometimes', 'required', 'integer', 'min:1'],

            'details.*.price' => ['sometimes', 'required', 'integer', 'min:0'],
            'details.*.hpp' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'details.*.manual_hpp' => [
                Rule::requiredIf(function () {
                    return $this->isManualRow();
                }),
                'nullable',
                'integer',
                'min:0',
            ],
            'details.*.unit_price' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'details.*.sub_total' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'details.*.subtotal_profit' => ['sometimes', 'nullable', 'integer'],

            'details.*.product_discount_amount' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'details.*.product_discount_type' => ['sometimes', 'nullable', 'string', 'in:fixed,percent'],
            'details.*.product_tax_amount' => ['sometimes', 'nullable', 'integer', 'min:0'],

            'details.*.original_price' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'details.*.is_price_adjusted' => ['sometimes', 'nullable', 'boolean'],
            'details.*.price_adjustment_amount' => ['sometimes', 'nullable', 'integer'],
            'details.*.price_adjustment_note' => ['sometimes', 'nullable', 'string', 'max:255'],
            'details.*.adjusted_by' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'details.*.adjusted_at' => ['sometimes', 'nullable', 'date'],
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

        $merge = [];
        foreach (['shipping_amount', 'tax_amount', 'discount_amount', 'tax_percentage', 'discount_percentage'] as $k) {
            if ($this->has($k)) {
                $merge[$k] = $toInt($this->input($k));
            }
        }

        $details = $this->input('details', []);
        if (is_array($details)) {
            foreach ($details as $i => $row) {
                foreach (['quantity', 'price', 'hpp', 'manual_hpp', 'unit_price', 'sub_total', 'subtotal_profit', 'product_discount_amount', 'product_tax_amount', 'original_price', 'price_adjustment_amount'] as $numKey) {
                    if (array_key_exists($numKey, $row)) {
                        $details[$i][$numKey] = $toInt($row[$numKey]);
                    }
                }

                if (isset($row['source_type'])) {
                    $st = strtolower((string) $row['source_type']);
                    $details[$i]['source_type'] = in_array($st, ['new', 'second', 'manual'], true) ? $st : 'new';
                }
                if (isset($row['product_discount_type'])) {
                    $dt = strtolower((string) $row['product_discount_type']);
                    $details[$i]['product_discount_type'] = in_array($dt, ['fixed', 'percent'], true) ? $dt : 'fixed';
                }

                // Recalc adjusted flags jika ada price/original_price
                if (array_key_exists('price', $details[$i]) || array_key_exists('original_price', $details[$i])) {
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
        }

        $merge['details'] = $details;
        $this->merge($merge);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $details = $this->input('details', []);
            foreach ($details as $idx => $row) {
                if (($row['source_type'] ?? 'new') === 'manual') {
                    if (array_key_exists('manual_kind', $row) && empty($row['manual_kind'])) {
                        $v->errors()->add("details.$idx.manual_kind", 'Jenis input manual wajib diisi.');
                    }
                    if (array_key_exists('manual_hpp', $row) && !isset($row['manual_hpp'])) {
                        $v->errors()->add("details.$idx.manual_hpp", 'HPP manual wajib diisi.');
                    }
                }
                $adj = (int) ($row['price_adjustment_amount'] ?? 0);
                if ($adj > 0 && empty($row['price_adjustment_note'])) {
                    $v->errors()->add("details.$idx.price_adjustment_note", 'Alasan diskon wajib diisi saat harga diturunkan.');
                }
                if (array_key_exists('quantity', $row) && (int) ($row['quantity'] ?? 0) < 1) {
                    $v->errors()->add("details.$idx.quantity", 'Qty minimal 1.');
                }
                if (array_key_exists('price', $row) && (int) ($row['price'] ?? -1) < 0) {
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

    public function attributes()
    {
        return [
            'date' => 'tanggal',
            'details.*.product_name' => 'nama produk',
            'details.*.quantity' => 'jumlah',
            'details.*.price' => 'harga',
            'details.*.price_adjustment_note' => 'alasan diskon',
        ];
    }
}
