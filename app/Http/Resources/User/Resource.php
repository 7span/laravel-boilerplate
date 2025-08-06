<?php

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

    protected $model = User::class;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();
        $data['profile_image'] = new MediaResource($this->whenLoadedMedia(config('media.tags.profile'), true));
        $data['user_device'] = new UserDeviceResource($this->whenLoaded('userDevice'));

        return $data;
    }
}
