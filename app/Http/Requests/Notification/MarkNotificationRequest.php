<?php

namespace App\Http\Requests\Notification;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MarkNotificationRequest extends FormRequest
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
     * Leaving `ids` empty marks every notification of the authenticated user.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'ids' => ['nullable', 'array'],
            'ids.*' => [
                'uuid',
                Rule::exists('notifications', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
