<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtp extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $otpLength = config('site.generateOtpLength');

        return [
            'email' => 'required|email|max:120',
            'otp' => 'required|digits:' . $otpLength,
        ];
    }
}
