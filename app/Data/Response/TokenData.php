<?php

declare(strict_types = 1);

namespace App\Data\Response;

use Spatie\LaravelData\Data;

final class TokenData extends Data
{
    public function __construct(
        public readonly string $message,
        public readonly string $token,
    ) {}

    /**
     * Build a TokenData instance from a service response array.
     *
     * @param  array{message: string, token: string}  $response
     */
    public static function fromArray(array $response): self
    {
        return new self(
            message: $response['message'],
            token: $response['token'],
        );
    }
}
