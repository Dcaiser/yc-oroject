<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $supplierId = $this->route('supplier');
        
        return [
            'supplier_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'supplier_code')->ignore($supplierId),
            ],
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'npwp' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'supplier_code.required' => 'Kode supplier wajib diisi.',
            'supplier_code.unique' => 'Kode supplier sudah digunakan.',
            'supplier_code.max' => 'Kode supplier maksimal 50 karakter.',
            'name.required' => 'Nama supplier wajib diisi.',
            'name.max' => 'Nama supplier maksimal 255 karakter.',
            'contact_person.max' => 'Contact person maksimal 255 karakter.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'npwp.max' => 'NPWP maksimal 50 karakter.',
        ];
    }
}
