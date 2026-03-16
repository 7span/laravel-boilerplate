<?php

namespace App\Http\Resources\Post;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Category\Resource as CategoryResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Post::class;

    public function toArray(Request $request): array
    {
        $data = $this->fields();
        $data['category'] = new CategoryResource($this->whenLoaded('category'));

        return $data;
    }
}

