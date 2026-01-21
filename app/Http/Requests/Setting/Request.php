<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /**
         * Add all setting keys that you want to update and
         * defined in site.php file.
         */
        $rules = [];

        foreach (config('site.setting_keys', []) as $key) {
            $rules[$key] = 'required';
        }

        return $rules;
    }
}
