<?php

namespace Modules\Adjustment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdjustmentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Atau check Gate
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'reason' => 'required|in:Rusak,Hilang,Kadaluarsa,Lainnya',
            'description' => 'required|string|max:1000',
            'files' => 'nullable|array|max:3', // Nullable di edit jika append
            'files.*' => 'file|mimes:jpg,jpeg,png|max:2048',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:0',
            'types' => 'required|array|min:1',
            'types.*' => 'required|in:add,sub',
        ];
    }

    public function messages()
    {
        return [
            'reason.required' => 'Alasan penyesuaian wajib dipilih!',
            'description.required' => 'Keterangan detail wajib diisi!',
            'files.max' => 'Maksimal 3 file bukti!',
            'files.*.mimes' => 'File harus gambar (JPG, PNG)!',
        ];
    }

    public function attributes()
    {
        return [
            'files' => 'bukti gambar',
            'reason' => 'alasan penyesuaian',
            'description' => 'keterangan',
        ];
    }
}
