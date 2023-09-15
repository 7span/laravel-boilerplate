<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Password;

class ChangePasswordData extends Data
{
    public function __construct(
        #[Password(min: 8)]
        public string $new_password,
        public string $current_password,
    ) {
    }
}
