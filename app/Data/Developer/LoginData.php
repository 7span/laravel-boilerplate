<?php

namespace App\Data\Developer;

use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public string $username,
        public string $password
    ) {
    }
}
