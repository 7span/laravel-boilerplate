<?php

namespace App\Http\Resources\Notification;

use App\Models\Notification;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Resource as UserResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Notification::class;

    public function toArray($request)
    {
        $data = $this->fields();
        $data['sender'] = new UserResource($this->whenLoaded('user'));
        $data['user'] = new UserResource($this->whenLoaded('sender'));

        return $data;
    }
}
