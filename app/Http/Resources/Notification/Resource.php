<?php

declare(strict_types=1);

namespace App\Http\Resources\Notification;

use App\Models\Notification;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Resource as UserResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string */
    protected $model = Notification::class;

    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        $data = $this->fields();
        $data['sender'] = new UserResource($this->whenLoaded('user'));
        $data['user'] = new UserResource($this->whenLoaded('sender'));

        return $data;
    }
}
