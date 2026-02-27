<?php

namespace App\Http\Resources\User;

use App\Data\UserData;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Media\Resource as MediaResource;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    public function toArray($request)
    {
        $data = UserData::fromModel($this->resource)->toArray();
        $data['user_device'] = new UserDeviceResource($this->whenLoaded('userDevice'));
        $data['profile'] = new MediaResource($this->whenLoadedMedia(config('media.tags.profile'), true));

        return $data;
    }
}
