<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Exists;

class ForgetPasswordData extends Data
{
    public function __construct(
        #[Email,
            Exists('users', 'email')]
        public string $email
    ) {
    }
}
