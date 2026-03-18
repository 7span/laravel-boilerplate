<?php

declare(strict_types = 1);

namespace App\Data\Request\Auth;

use Spatie\LaravelData\Data;

final class VerifyOtpRequestData extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $otp,
    ) {}

    /**
     * Build a VerifyOtpRequestData instance from validated request array.
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            otp: $data['otp'],
        );
    }
}
