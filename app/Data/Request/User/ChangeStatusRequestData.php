<?php

declare(strict_types = 1);

namespace App\Data\Request\User;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class ChangeStatusRequestData extends Data
{
    public function __construct(
        public readonly string $status,
    ) {}

    /**
     * Build a ChangeStatusRequestData instance from validated request array.
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            status: $data['status'],
        );
    }
}
