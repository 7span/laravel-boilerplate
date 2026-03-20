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
     * @return array{
     *     id: int,
     *     user_id: int,
     *     onesignal_player_id: string|null,
     *     device_id: string|null,
     *     device_type: string|null,
     *     created_at: int|null,
     *     updated_at: int|null
     * }
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();

        return $data;
    }
}
