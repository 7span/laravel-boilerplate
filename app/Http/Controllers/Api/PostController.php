<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\Post\StorePost;
use App\Http\Requests\Post\UpdatePost;
use Dedoc\Scramble\Attributes\QueryParameter;
use App\Http\Resources\Post\Resource as PostResource;

/**
 * @tags Posts
 */
#[Group('Posts', weight: 10)]
class PostController extends Controller
{
    use ApiResponser;

    private PostService $postService;

    public function __construct()
    {
        $this->postService = new PostService;
    }

    /**
     * List posts.
     */
    #[QueryParameter('appends')]
    public function index(Request $request)
    {
        $posts = $this->postService->collection($request->all());

        return PostResource::collection($posts);
    }

    /**
     * Store a new post.
     *
     * @response array{message: string, post: PostResource}
     */
    public function store(StorePost $request): JsonResponse
    {
        $post = $this->postService->store($request->validated());

        $data = [
            'message' => __('entity.entityCreated', ['entity' => 'Post']),
            'post' => new PostResource($post),
        ];

        return $this->success($data, 201);
    }

    /**
     * Show a post.
     */
    public function show(int $post): PostResource
    {
        $post = $this->postService->resource($post);

        return new PostResource($post);
    }

    /**
     * Update a post.
     *
     * @response array{message: string, post: PostResource}
     */
    public function update(UpdatePost $request, int $post): JsonResponse
    {
        $data = $this->postService->update($post, $request->validated());

        return $this->success($data, 200);
    }

    /**
     * Delete a post.
     *
     * @response array{message: string}
     */
    public function destroy(int $post): JsonResponse
    {
        $data = $this->postService->destroy($post);

        return $this->success($data, 200);
    }
}
