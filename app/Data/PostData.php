<?php

namespace App\Data;

use App\Models\Post;
use App\Libraries\Helper;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

class PostData extends Data
{
    public function __construct(
        public int $id,
        public int $user_id,
        public string $title,
        public ?string $body,
        public ?int $published_at,
        public Lazy|UserData|Optional|null $user,
        public ?int $created_at,
        public ?int $updated_at,
        public string|Optional $display_title,
    ) {}

    public static function fromModel(Post $post): self
    {
        return new self(
            id: $post->id,
            user_id: $post->user_id,
            title: $post->title,
            body: $post->body,
            published_at: $post->published_at,
            user: Lazy::whenLoaded('user', $post, fn () => UserData::fromModel($post->user)),
            created_at: $post->created_at,
            updated_at: $post->updated_at,
            display_title: Helper::getRequestedAppends($post, 'display_title'),
        );
    }
}
