<?php

declare(strict_types = 1);

namespace App\Http\Resources\Notification;

use App\Models\Notification;
use App\Traits\ResourceFilterable;
use App\Data\Response\NotificationData;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Notification::class;

    public function toArray($request)
    {
        return NotificationData::fromModel($this->resource)->toArray();
    }
}
