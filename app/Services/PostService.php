<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Traits\PaginationTrait;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\Post\Resource as PostResource;

class PostService
{
    use PaginationTrait;

    private Post $postObj;

    public function __construct()
    {
        $this->postObj = new Post;
    }

    public function collection(array $inputs = [])
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters(['is_published'])
            ->get();

        return $this->paginationAttribute($posts);
    }

    public function resource(int $id): Post
    {
        return $this->postObj->getQB()->findOrFail($id);
    }

    public function store(array $inputs): Post
    {
        if (empty($inputs['slug'])) {
            $inputs['slug'] = Str::slug($inputs['title']);
        }

        return $this->postObj->create($inputs);
    }

    public function update(int $id, array $inputs = []): array
    {
        $post = $this->resource($id);

        if (isset($inputs['title']) && empty($inputs['slug'])) {
            $inputs['slug'] = Str::slug($inputs['title']);
        }

        $post->update($inputs);

        return [
            'message' => __('entity.entityUpdated', ['entity' => 'Post']),
            'post' => new PostResource($post),
        ];
    }

    public function destroy(int $id): array
    {
        $post = $this->resource($id);
        $post->delete();

        return [
            'message' => __('entity.entityDeleted', ['entity' => 'Post']),
        ];
    }
}
