<?php

declare(strict_types = 1);

namespace App\Data\Response;

use Spatie\LaravelData\Data;

final class MessageData extends Data
{
    public function __construct(
        public readonly string $message,
    ) {}

    /**
     * Build a MessageData instance from a service response array.
     *
     * @param  array{message: string}  $response
     */
    public static function fromArray(array $response): self
    {
        return new self(
            message: $response['message'],
        );
    }
}
