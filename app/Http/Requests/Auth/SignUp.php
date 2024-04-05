<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignUp extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|max:120',
            'last_name' => 'required|max:120',
            'username' => 'required|max:120',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'country_code' => 'nullable|max:8',
            'mobile_number' => 'nullable|digits:10',
        ];
    }
}
