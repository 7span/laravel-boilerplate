<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use App\Traits\PaginationTrait;

class PostService
{
    use PaginationTrait;

    private Post $postObj;

    public function __construct()
    {
        $this->postObj = new Post;
    }

    public function collection()
    {
        $query = $this->postObj->getQB()->with(['category']);

        return $this->paginationAttribute($query);
    }

    public function store(array $data): Post
    {
        // Incoming category_id is a Category ULID, convert it to internal numeric id
        if (! empty($data['category_id'])) {
            $category = Category::where('ulid', $data['category_id'])->firstOrFail();
            $data['category_id'] = $category->id;
        }

        return $this->postObj->create($data);
    }

    public function update(Post $post, array $data): Post
    {
        if (! empty($data['category_id'])) {
            $category = Category::where('ulid', $data['category_id'])->firstOrFail();
            $data['category_id'] = $category->id;
        }

        $post->update($data);

        return $post->load('category');
    }

    public function destroy(Post $post): void
    {
        $post->delete();
    }
}

