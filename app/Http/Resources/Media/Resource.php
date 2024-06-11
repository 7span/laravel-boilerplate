<?php

namespace App\Http\Resources\Media;

use App\Models\Media;
use App\Traits\ResourceFilterable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Media::class;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = $this->fields();

        $data['url'] = Storage::disk('profile_image')->url($this->file_name);

        return $data;
    }
}
