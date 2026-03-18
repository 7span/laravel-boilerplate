<?php

declare(strict_types = 1);

namespace App\Data\Request\Auth;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class ResetPasswordRequestData extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $token,
    ) {}

    /**
     * Build a ResetPasswordRequestData instance from validated request array.
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            token: $data['token'],
        );
    }
}
