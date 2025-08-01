<?php

namespace App\Http\Requests\User;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatus extends FormRequest
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
            'status' => 'required|in:' . implode(',', array_column(UserStatus::cases(), 'value')),
        ];
    }
}
