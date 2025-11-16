<?php

namespace Modules\People\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('edit_customers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Get customer ID dari route parameter
        $customerId = $this->route('customer');
        
        return [
            // Nama customer
            'customer_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\.\-\_]+$/u',
            ],

            // Email customer (unique kecuali milik sendiri)
            'customer_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:customers,customer_email,' . $customerId,
            ],

            // Nomor telepon
            'customer_phone' => [
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
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'customer_name.required' => 'Nama customer wajib diisi.',
            'customer_name.regex' => 'Nama customer hanya boleh huruf, angka, spasi, titik, dan strip.',
            'customer_name.max' => 'Nama customer maksimal 255 karakter.',

            'customer_email.required' => 'Email customer wajib diisi.',
            'customer_email.email' => 'Format email tidak valid.',
            'customer_email.unique' => 'Email sudah terdaftar. Gunakan email lain.',

            'customer_phone.required' => 'Nomor telepon wajib diisi.',
            'customer_phone.regex' => 'Format nomor telepon tidak valid.',

            'city.required' => 'Kota wajib diisi.',
            'city.regex' => 'Kota hanya boleh berisi huruf dan spasi.',

            'country.in' => 'Negara yang dipilih tidak valid.',

            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.min' => 'Alamat minimal 10 karakter untuk memastikan kelengkapan.',
            'address.max' => 'Alamat maksimal 500 karakter.',
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
            'customer_name' => 'nama customer',
            'customer_email' => 'email',
            'customer_phone' => 'nomor telepon',
            'city' => 'kota',
            'country' => 'negara',
            'address' => 'alamat',
        ];
    }

    /**
     * Prepare the data for validation.
     * Auto-sanitize input sebelum validasi
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'customer_name' => strip_tags(trim($this->customer_name ?? '')),
            'customer_email' => strtolower(trim($this->customer_email ?? '')),
            'customer_phone' => preg_replace('/[^0-9+\-\s\(\)]/', '', $this->customer_phone ?? ''),
            'city' => ucwords(strtolower(trim($this->city ?? ''))),
            'country' => trim($this->country ?? 'Indonesia'),
            'address' => strip_tags(trim($this->address ?? '')),
        ]);
    }
}
