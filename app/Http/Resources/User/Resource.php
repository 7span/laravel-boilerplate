<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Media\Resource as MediaResource;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string */
    protected $model = User::class;

    /** @return array<string, mixed> */
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
