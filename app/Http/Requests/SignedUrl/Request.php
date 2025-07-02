<?php

namespace App\Http\Requests\SignedUrl;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'filename' => 'required',
            'mime_type' => 'required|in:' . implode(',', array_keys(config('media.mime_types'))),
            'type' => 'required|in:' . implode(',', config('media.tags')),
        ];
    }
}
