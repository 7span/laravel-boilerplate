<?php

declare(strict_types = 1);

namespace App\Data\Request\Notification;

use Spatie\LaravelData\Data;

final class OneSignalRequestData extends Data
{
    public function __construct(
        public readonly string $onesignal_player_id,
        public readonly ?string $device_id,
        public readonly ?string $device_type,
    ) {}

    /**
     * Build a OneSignalRequestData instance from validated request array.
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            onesignal_player_id: $data['onesignal_player_id'],
            device_id: $data['device_id'] ?? null,
            device_type: $data['device_type'] ?? null,
        );
    }
}
