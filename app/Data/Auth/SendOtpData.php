<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;

class SendOtpData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $email,
        public string $otp_for
    ) {
    }
}
