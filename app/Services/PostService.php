<?php

namespace App\Services;

use App\Models\Post;
use App\Data\PostRequestData;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Post\Resource as PostResource;

class PostService
{
    use PaginationTrait;

    public function __construct(
        private readonly Post $postObj
    ) {}

    public function collection(): mixed
    {
        $posts = $this->postObj->getQB();

        return $this->paginationAttribute($posts);
    }

    public function show(Post $post): Post
    {
        return $post;
    }

    public function store(PostRequestData $data): array
    {
        $inputs = $data->toArray();
        $inputs['user_id'] = Auth::id();

        $post = $this->postObj->create($inputs);
        $response['message'] = 'Post created successfully';
        $response['post'] = new PostResource($post);

        return $response;
    }

    public function update(Post $post, PostRequestData $data): array
    {
        $post->update($data->toArray());

        return [
            'message' => 'Post updated successfully',
            'post' => new PostResource($post->refresh()),
        ];
    }

    public function destroy(Post $post): bool
    {
        return $post->delete();
    }
}
