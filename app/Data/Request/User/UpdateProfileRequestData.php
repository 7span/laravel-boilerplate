<?php

declare(strict_types = 1);

namespace App\Data\Request\User;

use Spatie\LaravelData\Data;

final class UpdateProfileRequestData extends Data
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $username,
        public readonly ?string $country_code,
        public readonly ?string $mobile_no,
        public readonly ?array $profile,
    ) {}

    /**
     * Build an UpdateProfileRequestData instance from validated request array.
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            first_name: $data['first_name'],
            last_name: $data['last_name'],
            username: $data['username'],
            country_code: $data['country_code'] ?? null,
            mobile_no: $data['mobile_no'] ?? null,
            profile: $data['profile'] ?? null,
        );
    }
}
