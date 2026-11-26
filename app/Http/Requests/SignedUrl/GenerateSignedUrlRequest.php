<?php

namespace App\Http\Requests\SignedUrl;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class GenerateSignedUrlRequest extends FormRequest
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
            'filename' => ['required', 'string', 'max:255'],
            'mime_type' => ['required', 'string', Rule::in(array_keys(config('media.mime_types')))],
            'type' => ['required', 'string', Rule::in(config('media.tags'))],
        ];
    }
}
