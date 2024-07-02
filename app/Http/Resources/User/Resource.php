<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use App\Http\Resources\Media\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = User::class;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();

        $data['profile_image'] = new Collection($this->whenLoaded('media'));

        return $data;
    }
}
