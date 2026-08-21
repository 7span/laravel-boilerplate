<?php

namespace App\Enums;

enum UserOtpFor: string
{
    case FORGOT_PASSWORD = 'forgot_password';
    case EMAIL_VERIFICATION = 'email_verification';

    public function label(): string
    {
        return __('enum.' . $this->value);
    }
}
