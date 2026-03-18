<?php

declare(strict_types = 1);

namespace App\Data\Request\Auth;

use Spatie\LaravelData\Data;

final class RegisterRequestData extends Data
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $username,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $country_code,
        public readonly ?string $mobile_no,
    ) {}

    /**
     * Build a RegisterRequestData instance from validated request array.
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            first_name: $data['first_name'],
            last_name: $data['last_name'],
            username: $data['username'],
            email: $data['email'],
            password: $data['password'],
            country_code: $data['country_code'] ?? null,
            mobile_no: $data['mobile_no'] ?? null,
        );
    }
}
