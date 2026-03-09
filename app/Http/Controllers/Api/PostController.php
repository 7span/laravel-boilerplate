<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Traits\ApiResponser;
use App\Data\PostRequestData;
use App\Services\PostService;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\Resource as PostResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    use ApiResponser;

    public function __construct(
        private readonly PostService $postService
    ) {}

    #[OA\Get(
        path: '/api/posts',
        operationId: 'postsIndex',
        tags: ['Post'],
        summary: 'List posts',
        x: ['model' => Post::class],
        parameters: [
            new OA\Parameter(
                name: 'media',
                in: 'query',
                description: 'Include media in response',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    example: 'profile'
                )
            ),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function index(): AnonymousResourceCollection
    {
        $posts = $this->postService->collection();

        return PostResource::collection($posts);
    }

    #[OA\Post(
        path: '/api/posts',
        operationId: 'postsStore',
        tags: ['Post'],
        summary: 'Create post',
        security: [['bearerAuth' => []]]
    )]
    public function store(PostRequestData $postRequestData): JsonResponse
    {
        $response = $this->postService->store($postRequestData);

        return $this->success($response, 201);
    }

    #[OA\Get(
        path: '/api/posts/{post}',
        operationId: 'postsShow',
        tags: ['Post'],
        summary: 'Show post details',
        security: [['bearerAuth' => []]]
    )]
    public function show(Post $post): PostResource
    {
        $post = $this->postService->show($post);

        return new PostResource($post);
    }

    #[OA\Put(
        path: '/api/posts/{post}',
        operationId: 'postsUpdate',
        tags: ['Post'],
        summary: 'Update post',
        security: [['bearerAuth' => []]]
    )]
    public function update(PostRequestData $postRequestData, Post $post): JsonResponse
    {
        $response = $this->postService->update($post, $postRequestData);

        return $this->success($response, 200);
    }

    #[OA\Delete(
        path: '/api/posts/{post}',
        operationId: 'postsDestroy',
        tags: ['Post'],
        summary: 'Delete post',
        security: [['bearerAuth' => []]]
    )]
    public function destroy(Post $post): JsonResponse
    {
        $this->postService->destroy($post);

        return $this->success(null, 204);
    }
}
