<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Digits;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Confirmed;

class ResetPasswordData extends Data
{
    public function __construct(
        #[Email,
            Exists('users', 'email')]
        public string $email,
        #[Password(min: 8),
            Confirmed]
        public string $password,
        #[Digits(4)]
        public int $otp
    ) {
    }
}
