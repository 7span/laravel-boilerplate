<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\Request as PostRequest;
use App\Http\Resources\Post\Resource as PostResource;
use App\Services\PostService;

class PostController extends Controller
{
    use ApiResponser;

    private PostService $postService;

    public function __construct()
    {
        $this->postService = new PostService;
    }

    #[OA\Get(
        path: '/api/posts',
        operationId: 'postIndex',
        tags: ['Post'],
        summary: 'List posts',
        x: ['model' => Post::class]
    )]
    public function index()
    {
        $posts = $this->postService->collection();

        return PostResource::collection($posts);
    }

    #[OA\Post(
        path: '/api/posts',
        operationId: 'postStore',
        tags: ['Post'],
        summary: 'Create post',
        security: [['bearerAuth' => []]]
    )]
    public function store(PostRequest $request) 
    {
        $data = $request->validated();

        $post = $this->postService->store($data);

        return new PostResource($post->load('category'));
    }

    #[OA\Get(
        path: '/api/posts/{post}',
        operationId: 'postShow',
        tags: ['Post'],
        summary: 'Show post',
        security: [['bearerAuth' => []]]
    )]
    public function show(Post $post)
    {
        $post->load('category');

        return new PostResource($post);
    }

    #[OA\Put(
        path: '/api/posts/{post}',
        operationId: 'postUpdate',
        tags: ['Post'],
        summary: 'Update post',
        security: [['bearerAuth' => []]]
    )]
    public function update(PostRequest $request, Post $post)
    {
        $post = $this->postService->update($post, $request->validated());

        return new PostResource($post);
    }

    #[OA\Delete(
        path: '/api/posts/{post}',
        operationId: 'postDestroy',
        tags: ['Post'],
        summary: 'Delete post',
        security: [['bearerAuth' => []]]
    )]
    public function destroy(Post $post)
    {
        $this->postService->destroy($post);

        return $this->success([], 'Post deleted successfully');
    }
}

