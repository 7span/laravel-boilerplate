<?php

namespace App\Data;

use App\Models\UserDevice;
use Spatie\LaravelData\Data;

class UserDeviceData extends Data
{
    public function __construct(
        public int $id,
        public ?int $user_id,
        public ?string $onesignal_player_id,
        public ?string $device_id,
        public ?string $device_type,
        public ?int $created_at,
        public ?int $updated_at,
    ) {}

    public static function fromModel(UserDevice $device): self
    {
        return new self(
            id: $device->id,
            user_id: $device->user_id,
            onesignal_player_id: $device->onesignal_player_id,
            device_id: $device->device_id,
            device_type: $device->device_type,
            created_at: $device->created_at,
            updated_at: $device->updated_at,
        );
    }
}
