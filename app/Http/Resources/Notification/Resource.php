<?php

namespace App\Http\Resources\Notification;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @property Notification $resource
 */
#[SchemaName('Notification')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string<Notification> */
    protected $model = Notification::class;

    /**
     * @return array{
     *     id: string,
     *     user_id: int,
     *     sent_by: int|null,
     *     title: string|null,
     *     description: string|null,
     *     type: string|null,
     *     notifiable_type: string|null,
     *     notifiable_id: int|string|null,
     *     data: array<string, mixed>,
     *     read_at: int|null,
     *     created_at: int|null,
     *     user: UserResource|null,
     *     sender: UserResource|null
     * }
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();

        /** @var array<string, mixed> $replacements */
        $replacements = $data['data'] ?? [];

        $data['title'] = isset($data['title']) ? __($data['title'], $replacements) : null;
        $data['description'] = isset($data['description']) ? __($data['description'], $replacements) : null;
        $data['data'] = $replacements;

        $data['user'] = new UserResource($this->whenLoaded('user'));
        $data['sender'] = new UserResource($this->whenLoaded('sender'));

        return $data;
    }
}
