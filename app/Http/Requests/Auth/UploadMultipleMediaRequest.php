<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UploadMultipleMediaRequest extends FormRequest
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
            'media' => 'nullable|array',
            'media.*.file_name' => 'required_with:media',
            'media.*.directory' => 'required_with:media',
            'media.*.original_file_name' => 'required_with:media',
            'media.*.mime_type' => 'required_with:media',
            'media.*.size' => 'required_with:media|integer',
        ];
    }

    /**
     * Get media validation rules.
     */
    public function getMultipleMediaRules(): array
    {
        return $this->rules();
    }
}
