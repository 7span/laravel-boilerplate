<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignUp extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'required|max:120',
            'lastname' => 'required|max:120',
            'username' => 'required|max:120',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'country_code' => 'nullable|max:8',
            'mobile_number' =>  'nullable|digits:10',
        ];
    }
}
