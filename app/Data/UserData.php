<?php

namespace App\Data;

use DateTime;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Unique;

class UserData extends Data
{
    public function __construct(
        public ?int $id,
        #[
            Email,
            Unique('users', 'email')
        ]
        public string $email,
        #[Max(20)]
        public string $firstname,
        #[Max(20)]
        public string $lastname,
        #[Max(20)]
        public string $username,
        #[Max(10)]
        public string $country_code,
        #[Max(10)]
        public string $mobile_number,
        public ?DateTime $created_at
    ) {
    }
}
