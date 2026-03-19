<?php

namespace App\Http\Resources\Post;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @property Post $resource
 */
#[SchemaName('Post')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Post::class;

    /**
     * @return array{
     *     id: int,
     *     user_id: int,
     *     name: string,
     *     title: string,
     *     slug: string,
     *     description: string|null,
     *     status: string,
     *     published_at: int|null,
     *     created_at: int,
     *     updated_at: int,
     *     is_published: bool,
     *     user: UserResource
     * }
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();
        $data['user'] = new UserResource($this->whenLoaded('user'));

        return $data;
    }
}
