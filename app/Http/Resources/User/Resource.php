<?php

namespace App\Http\Resources\User;

use App\Data\UserData;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    public function toArray($request)
    {
        return UserData::fromModel($this->resource)->toArray();
    }
}
