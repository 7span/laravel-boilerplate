<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Confirmed;

class ChangePasswordData extends Data
{
    public function __construct(
        #[
            Password(min: 8),
        ]
        public string $current_password,
        #[
            Password(min: 8),
            Confirmed
        ]
        public string $password,
    ) {
    }
}
