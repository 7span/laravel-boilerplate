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

    protected $model = Notification::class;

    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique UUID of the notification.
             */
            'id' => $this->id,
            /**
             * The ID of the recipient user.
             */
            'user_id' => $this->user_id,
            /**
             * The ID of the user who triggered the notification.
             */
            'sent_by' => $this->sent_by,
            /**
             * Short notification title.
             */
            'title' => $this->title,
            /**
             * Full notification message body.
             */
            'description' => $this->description,
            /**
             * Notification category type.
             */
            'type' => $this->type,
            /**
             * The class name of the related notifiable entity.
             */
            'notifiable_type' => $this->notifiable_type,
            /**
             * The ID of the related notifiable entity.
             */
            'notifiable_id' => $this->notifiable_id,
            /**
             * Additional structured data attached to the notification.
             *
             * @var array<string, mixed>
             */
            'data' => $this->data,
            /**
             * Timestamp when the notification was read, or null if unread.
             */
            'read_at' => $this->read_at,
            /**
             * Timestamp when the notification was created.
             */
            'created_at' => $this->created_at,
            'sender' => new UserResource($this->whenLoaded('user')),
            'user' => new UserResource($this->whenLoaded('sender')),
        ];
    }
}
