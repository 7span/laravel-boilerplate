<?php

namespace App\Enums;

enum UserOtpFor: string
{
    case FORGOT_PASSWORD = 'forgot_password';
    case EMAIL_VERIFICATION = 'email_verification';

    public function label(): string
    {
        return match ($this) {
            self::FORGOT_PASSWORD => 'Forgot Password',
            self::EMAIL_VERIFICATION => 'Email Verification',
        };
    }
}
