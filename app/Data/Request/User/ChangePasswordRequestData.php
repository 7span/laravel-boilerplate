<?php

declare(strict_types = 1);

namespace App\Data\Request\User;

use Spatie\LaravelData\Data;

final class ChangePasswordRequestData extends Data
{
    public function __construct(
        public readonly string $current_password,
        public readonly string $password,
    ) {}

    /**
     * Build a ChangePasswordRequestData instance from validated request array.
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            current_password: $data['current_password'],
            password: $data['password'],
        );
    }
}
