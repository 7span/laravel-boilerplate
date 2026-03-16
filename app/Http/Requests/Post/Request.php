<?php

namespace App\Http\Requests\Post;

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
        return [
            // Here category_id is the Category ULID coming from client
            'category_id' => 'required|exists:categories,ulid',
            'post_name' => 'required|string|max:255',
            'post_date' => 'nullable|date',
        ];
    }
}

