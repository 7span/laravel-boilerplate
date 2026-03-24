<?php

declare(strict_types=1);

namespace App\Http\Resources\Notification;

use App\Models\Notification;
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

    /** @var class-string */
    protected $model = Notification::class;

<<<<<<< HEAD
    /** @return array<string, mixed> */
    public function toArray($request): array
=======
    /**
     * @return array{
     *     id: string,
     *     user_id: int,
     *     sent_by: int|null,
     *     title: string,
     *     description: string,
     *     type: string|null,
     *     notifiable_type: string|null,
     *     notifiable_id: int|string|null,
     *     data: array<string, mixed>,
     *     read_at: int|null,
     *     created_at: int|null,
     *     sender: UserResource|null,
     *     user: UserResource|null
     * }
     */
    public function toArray($request)
>>>>>>> origin/master
    {
        $data = $this->fields();
        $translationData = $data['data'] ?? [];
        $data['title'] = __($data['title'], $translationData);
        $data['description'] = __($data['description'], $translationData);

        $data['data'] = $translationData;
        $data['sender'] = new UserResource($this->whenLoaded('user'));
        $data['user'] = new UserResource($this->whenLoaded('sender'));

        return $data;
    }
}
