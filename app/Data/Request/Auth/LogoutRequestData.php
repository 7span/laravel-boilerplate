<?php

declare(strict_types = 1);

namespace App\Data\Request\Auth;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class LogoutRequestData extends Data
{
    public function __construct(
        public readonly ?string $onesignal_player_id,
    ) {}

    /**
     * Build a LogoutRequestData instance from validated request array.
     *
     * @param  array{onesignal_player_id?: string|null}  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            onesignal_player_id: $data['onesignal_player_id'] ?? null,
        );
    }
}
