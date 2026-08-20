<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Media\Resource as MediaResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

/**
 * @property User $resource
 */
#[SchemaName('User')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string<User> */
    protected $model = User::class;

    /**
     * @return array{
     *     id: int,
     *     first_name: string|null,
     *     last_name: string|null,
     *     username: string|null,
     *     email: string,
     *     email_verified_at: int|null,
     *     locale: string|null,
     *     status: string,
     *     country_code: string|null,
     *     mobile_no: string|null,
     *     last_login_at: int|null,
     *     created_at: int|null,
     *     name: string,
     *     display_status: string|null,
     *     display_mobile_no: string,
     *     profile_image: MediaResource|null,
     *     user_devices: AnonymousResourceCollection<UserDeviceResource>,
     *     media: AnonymousResourceCollection<MediaResource>
     * }
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();
        $data['profile_image'] = new MediaResource($this->whenLoadedMedia(config('media.tags.profile'), true));
        $data['user_devices'] = UserDeviceResource::collection($this->whenLoaded('userDevices'));
        $data['media'] = MediaResource::collection($this->whenLoaded('media'));

        return $data;
    }
}
