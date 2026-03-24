<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Rules\MediaRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfile extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:120',
            'last_name' => 'required|max:120',
            'username' => 'required|max:120|unique:users,username,' . Auth::id(),
            'country_code' => 'required_with:mobile_no|max:5',
            'mobile_no' => 'nullable|min:8|max:15',
        ] + MediaRule::rules(is_scalar($tag = config('media.tags.profile')) ? (string) $tag : '', false);
    }
}
