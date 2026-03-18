<?php

declare(strict_types = 1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class Logout extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'onesignal_player_id' => 'nullable|string',
        ];
    }
}
