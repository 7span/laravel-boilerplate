<?php

namespace App\Http\Resources\Notification;

use App\Data\NotificationData;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Resource as UserResource;

class Resource extends JsonResource
{
    public function toArray($request)
    {
        $data = NotificationData::fromModel($this->resource)->toArray();
        $data['user'] = new UserResource($this->whenLoaded('user'));
        $data['sender'] = new UserResource($this->whenLoaded('sender'));

        return $data;
    }
}
