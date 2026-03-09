<?php

namespace App\Http\Resources\Notification;

use App\Data\NotificationData;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    public function toArray($request)
    {
        $data = NotificationData::fromModel($this->resource)->toArray();

        return $data;
    }
}
