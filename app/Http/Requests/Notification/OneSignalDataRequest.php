<?php

namespace App\Http\Requests\Notification;

use App\Enums\DeviceType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OneSignalDataRequest extends FormRequest
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
            'onesignal_player_id' => ['required', 'string', 'max:255'],
            'device_id' => ['nullable', 'string', 'max:255'],
            'device_type' => ['nullable', Rule::enum(DeviceType::class)],
        ];
    }
}
