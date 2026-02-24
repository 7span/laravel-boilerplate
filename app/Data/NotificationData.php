<?php

namespace App\Data;

use App\Models\Notification;
use Spatie\LaravelData\Data;

class NotificationData extends Data
{
    public function __construct(
        public string $id,
        public int $user_id,
        public ?int $sent_by,
        public ?string $title,
        public ?string $description,
        public ?string $type,
        public string $notifiable_type,
        public int $notifiable_id,
        /** @var array<string, mixed> */
        public array $data,
        public ?int $read_at,
        public ?int $created_at,
        public ?int $updated_at
    ) {}

    public static function fromModel(Notification $notification): self
    {
        return new self(
            id: $notification->id,
            user_id: $notification->user_id,
            sent_by: $notification->sent_by,
            title: $notification->title,
            description: $notification->description,
            type: $notification->type,
            notifiable_type: $notification->notifiable_type,
            notifiable_id: $notification->notifiable_id,
            data: $notification->data ?? [],
            read_at: $notification->read_at,
            created_at: $notification->created_at,
            updated_at: $notification->updated_at
        );
    }
}
