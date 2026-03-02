<?php

namespace App\Http\Resources\User;

use App\Data\UserData;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    public function toArray($request)
    {
        $data = UserData::fromModel($this->resource)->toArray();

        return $data;
    }
}
