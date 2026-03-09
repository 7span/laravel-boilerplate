<?php

namespace App\Http\Resources\Post;

use App\Data\PostData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = PostData::fromModel($this->resource)->toArray();

        return $data;
    }
}
