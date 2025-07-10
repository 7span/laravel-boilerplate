<?php

namespace App\Enums;

enum UserOtpFor: string
{
    case FORGOT_PASSWORD = 'forgot_password';
    case VERIFY_EMAIL = 'verify_email';

    public function label(): string
    {
        return match ($this) {
            self::FORGOT_PASSWORD => 'Forgot Password',
            self::VERIFY_EMAIL => 'Verify Email',
        };
    }
}
