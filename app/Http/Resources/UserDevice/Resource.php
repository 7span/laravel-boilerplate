<?php

namespace App\Http\Resources\UserDevice;

use App\Models\UserDevice;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @property UserDevice $resource
 */
#[SchemaName('UserDevice')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string<UserDevice> */
    protected $model = UserDevice::class;

    /**
     * @return array{
     *     id: int,
     *     user_id: int,
     *     onesignal_player_id: string,
     *     device_id: string|null,
     *     device_type: string|null,
     *     display_device_type: string|null,
     *     created_at: int|null,
     *     updated_at: int|null,
     *     user: UserResource|null
     * }
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();
        $data['user'] = new UserResource($this->whenLoaded('user'));

        return $data;
    }
}
