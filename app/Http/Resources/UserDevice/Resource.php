<?php

namespace App\Http\Resources\UserDevice;

use App\Models\UserDevice;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property UserDevice $resource
 */
#[SchemaName('UserDevice')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = UserDevice::class;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the device record.
             */
            'id' => $this->id,
            /**
             * The ID of the owning user.
             */
            'user_id' => $this->user_id,
            /**
             * The OneSignal player ID for push notifications.
             */
            'onesignal_player_id' => $this->onesignal_player_id,
            /**
             * The unique hardware device ID.
             */
            'device_id' => $this->device_id,
            /**
             * The type of device (e.g. ios, android, web).
             */
            'device_type' => $this->device_type,
        ];
    }
}
