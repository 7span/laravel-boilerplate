<?php

declare(strict_types = 1);

namespace App\Data;

use App\Models\Post;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PostRequestData extends Data
{
    public function __construct(
        public string|Optional $title,
        public string|Optional|null $body,
        public int|Optional|null $published_at,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'published_at' => ['nullable', 'integer'],
        ];
    }

    public static function fromModel(Post $post): self
    {
        return new self(
            title: $post->title,
            body: $post->body,
            published_at: $post->published_at,
        );
    }
}
