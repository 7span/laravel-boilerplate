<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Digits;

class VerifyOtpData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $email,
        #[Max(20)]
        public string $otp_for,
        #[Digits(6)]
        public ?int $otp
    ) {
    }
}
