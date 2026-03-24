<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtp extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:120',
            'otp' => 'required|digits:' . (is_scalar($l = config('site.otp.length', '6')) ? (string) $l : '6'),
        ];
    }
}
