<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Media\Resource as MediaResource;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

/**
 * @property User $resource
 */
#[SchemaName('User')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string */
    protected $model = User::class;

<<<<<<< HEAD
    /** @return array<string, mixed> */
=======
    /**
     * @return array{
     *     id: int,
     *     first_name: string|null,
     *     last_name: string|null,
     *     username: string|null,
     *     email: string,
     *     locale: string|null,
     *     status: string,
     *     country_code: string|null,
     *     mobile_no: string|null,
     *     email_verified_at: int|null,
     *     last_login_at: int|null,
     *     created_at: int|null,
     *     updated_at: int|null,
     *     deleted_at: int|null,
     *     name: string,
     *     display_status: string,
     *     display_mobile_no: string,
     *     profile_image: MediaResource|null,
     *     user_device: UserDeviceResource|null
     * }
     */
>>>>>>> origin/master
    public function toArray(Request $request): array
    {
        $data = $this->fields();
        $val = config('media.tags.profile', '');
        $tag = is_scalar($val) ? (string) $val : '';
        $data['profile_image'] = new MediaResource($this->whenLoadedMedia($tag, true));
        $data['user_device'] = new UserDeviceResource($this->whenLoaded('userDevice'));

        return $data;
    }
}
