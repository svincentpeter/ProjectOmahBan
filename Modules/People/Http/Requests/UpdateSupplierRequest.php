<?php

namespace Modules\People\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Cek permission via Gate
        return Gate::allows('edit_suppliers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Dapatkan ID supplier yang sedang diupdate
        $supplierId = $this->route('supplier') ? $this->route('supplier')->id : $this->route('supplier');

        return [
            // Nama supplier
            'supplier_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\.\-\_]+$/u',
            ],

            // Email supplier (unique kecuali untuk supplier ini)
            'supplier_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('suppliers', 'supplier_email')->ignore($supplierId),
            ],

            // Nomor telepon
            'supplier_phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\+\-\s\(\)]+$/',
            ],

            // Kota
            'city' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/u',
            ],

            // Country
            'country' => [
                'nullable',
                'string',
                'max:100',
                'in:Indonesia,Malaysia,Singapura,Brunei',
            ],

            // Alamat lengkap
            'address' => [
                'required',
                'string',
                'max:500',
                'min:10',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors (Bahasa Indonesia).
     *
     * @return array
     */
    public function messages()
    {
        return [
            // Nama supplier
            'supplier_name.required' => 'Nama supplier wajib diisi.',
            'supplier_name.string' => 'Nama supplier harus berupa teks.',
            'supplier_name.max' => 'Nama supplier maksimal 255 karakter.',
            'supplier_name.regex' => 'Nama supplier hanya boleh mengandung huruf, angka, spasi, titik, dan strip.',

            // Email
            'supplier_email.required' => 'Email supplier wajib diisi.',
            'supplier_email.email' => 'Format email tidak valid.',
            'supplier_email.max' => 'Email maksimal 255 karakter.',
            'supplier_email.unique' => 'Email ini sudah terdaftar untuk supplier lain.',

            // Telepon
            'supplier_phone.required' => 'Nomor telepon wajib diisi.',
            'supplier_phone.string' => 'Nomor telepon harus berupa teks.',
            'supplier_phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'supplier_phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: 08123456789 atau +62-812-3456-7890.',

            // Kota
            'city.required' => 'Kota supplier wajib diisi.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Nama kota maksimal 100 karakter.',
            'city.regex' => 'Nama kota hanya boleh mengandung huruf dan spasi.',

            // Country
            'country.string' => 'Negara harus berupa teks.',
            'country.max' => 'Nama negara maksimal 100 karakter.',
            'country.in' => 'Negara harus salah satu dari: Indonesia, Malaysia, Singapura, atau Brunei.',

            // Alamat
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'address.min' => 'Alamat minimal 10 karakter untuk memastikan kelengkapan.',
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
            'supplier_name' => 'nama supplier',
            'supplier_email' => 'email supplier',
            'supplier_phone' => 'nomor telepon',
            'city' => 'kota',
            'country' => 'negara',
            'address' => 'alamat',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Jika country kosong, set default Indonesia
        if (empty($this->country)) {
            $this->merge([
                'country' => 'Indonesia',
            ]);
        }

        // Trim whitespace dari semua input
        $this->merge([
            'supplier_name' => trim($this->supplier_name ?? ''),
            'supplier_email' => trim($this->supplier_email ?? ''),
            'supplier_phone' => trim($this->supplier_phone ?? ''),
            'city' => trim($this->city ?? ''),
            'country' => trim($this->country ?? 'Indonesia'),
            'address' => trim($this->address ?? ''),
        ]);
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        abort(403, 'Anda tidak memiliki izin untuk mengubah data supplier.');
    }
}
