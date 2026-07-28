<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class Register extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|array',
            'first_name.*' => 'required|string|max:120',
            'last_name' => 'required|array',
            'last_name.*' => 'required|string|max:120',
            'username' => 'required|max:120|unique:users,username',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'country_code' => 'nullable|max:5',
            'mobile_no' => ['nullable', 'regex:/^\+?[1-9]\d{7,14}$/'],
        ];
    }
}
