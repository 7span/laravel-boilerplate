<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Confirmed;

class SignUpData extends Data
{
    public function __construct(
        #[Max(20)]
        public string $name,
        #[Email,
            Unique('users', 'email'),
            Max(255)]
        public string $email,
        #[Password(min: 8),
            Confirmed]
        public string $password
    ) {
    }
}
