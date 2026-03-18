<?php

declare(strict_types = 1);

namespace App\Http\Resources\Media;

use App\Models\Media;
use App\Data\Response\MediaData;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    protected $model = Media::class;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return MediaData::fromModel($this->resource)->toArray();
    }
}
