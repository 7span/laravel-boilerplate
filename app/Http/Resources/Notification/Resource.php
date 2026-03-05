<?php

namespace App\Http\Resources\Notification;

use App\Data\NotificationData;
use App\Traits\InteractsWithApiResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use InteractsWithApiResponse;

    public function toArray($request)
    {
        $data = NotificationData::fromModel($this->resource)->toArray();

        return $data;
    }
}
