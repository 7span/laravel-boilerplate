<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfile extends FormRequest
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
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'country_code' => 'nullable|max:8',
            'mobile_number' => 'nullable|digits:10',
            'profile_image' => 'nullable|array',
            'profile_image.*.file_name' => 'required_with:profile_image',
            'profile_image.*.directory' => 'required_with:profile_image',
            'profile_image.*.original_file_name' => 'required_with:profile_image',
            'profile_image.*.mime_type' => 'required_with:profile_image',
            'profile_image.*.size' => 'required_with:profile_image|integer'
        ];
    }
}
