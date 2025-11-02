<?php

namespace Modules\Adjustment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Modules\Product\Entities\Product;

class StoreAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pakai Gate agar konsisten dengan controller
        // Jika belum set permission, bisa fallback true.
        return Gate::allows('create_adjustments') ?? true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'reason' => 'required|in:Rusak,Hilang,Kadaluarsa,Lainnya',
            'description' => 'required|string|max:1000',

            // Bukti gambar (maks 3 file @ 2MB)
            'files' => 'nullable|array|max:3',
            'files.*' => 'file|mimes:jpg,jpeg,png|max:2048',

            // Array item penyesuaian
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',

            'quantities' => 'required|array|min:1',
            // ✅ min:1 (bukan 0)
            'quantities.*' => 'required|numeric|min:1',

            'types' => 'required|array|min:1',
            'types.*' => 'required|in:add,sub',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Alasan penyesuaian wajib dipilih!',
            'description.required' => 'Keterangan detail wajib diisi!',
            'files.max' => 'Maksimal 3 file bukti!',
            'files.*.mimes' => 'File harus gambar (JPG/PNG).',
            'quantities.*.min' => 'Jumlah minimal adalah 1.',
            'product_ids.*.exists' => 'Produk yang dipilih tidak ditemukan.',
            'types.*.in' => 'Tipe penyesuaian harus Penambahan atau Pengurangan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'files' => 'bukti gambar',
            'reason' => 'alasan penyesuaian',
            'description' => 'keterangan',
            'product_ids' => 'produk',
            'quantities' => 'jumlah',
            'types' => 'tipe',
        ];
    }

    /**
     * Cross-field validation:
     * - Panjang array harus sama
     * - Jika type=sub, qty <= stok saat ini
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $productIds = (array) $this->input('product_ids', []);
            $quantities = (array) $this->input('quantities', []);
            $types = (array) $this->input('types', []);

            // Panjang array harus sama
            $n = count($productIds);
            if ($n !== count($quantities) || $n !== count($types)) {
                $v->errors()->add('product_ids', 'Format item tidak valid: jumlah elemen produk, jumlah, dan tipe harus sama.');
                return;
            }

            if ($n === 0) {
                return;
            }

            // Ambil stok saat ini untuk semua produk sekaligus (hemat query)
            // Gunakan kolom "product_quantity" sebagai stok berjalan; fallback 0 jika null.
            $stocks = Product::query()->whereIn('id', $productIds)->pluck('product_quantity', 'id')->map(fn($s) => (int) ($s ?? 0));

            // Validasi per-baris
            for ($i = 0; $i < $n; $i++) {
                $pid = (int) $productIds[$i];
                $qty = (int) ($quantities[$i] ?? 0);
                $typ = (string) ($types[$i] ?? '');

                // Jika pengurangan, qty tidak boleh melebihi stok saat ini
                if ($typ === 'sub') {
                    $current = (int) ($stocks[$pid] ?? 0);
                    if ($qty > $current) {
                        $v->errors()->add("quantities.$i", "Jumlah pengurangan melebihi stok saat ini ({$current}).");
                    }
                }
            }
        });
    }
}
