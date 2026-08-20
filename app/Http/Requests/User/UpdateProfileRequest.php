<?php

namespace App\Http\Requests\User;

use App\Rules\MediaRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:120', 'unique:users,username,' . Auth::id()],
            'country_code' => ['required_with:mobile_no', 'nullable', 'string', 'max:5'],
            'mobile_no' => ['nullable', 'string', 'digits_between:8,15'],
            'locale' => ['nullable', 'string', Rule::in(array_keys(config('language')))],
        ] + MediaRule::rules(config('media.tags.profile'), true);
    }
}
