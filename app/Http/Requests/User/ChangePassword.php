<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ChangePassword extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|current_password|min:8',
            'password' => 'required|min:8|confirmed|different:current_password',
        ];
    }

    public function messages(): array
    {
        return [
            'password.different' => __('validation.custom_messages.password_difference'),
            'current_password.current_password' => __('validation.custom_messages.current_password'),
        ];
    }
}
