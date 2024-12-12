<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|unique:users,phone',
            'address' => 'required|string',
            'city' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib isi',
            'password.min' => 'Password minimal 8 karakter',
            'phone.required' => 'Nomor telepon harus diisi.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'address.required' => 'Alamat harus diisi.',
            'city.required' => 'Kota harus diisi.',
        ];
    }
}
