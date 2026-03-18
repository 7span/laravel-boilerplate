<?php

declare(strict_types = 1);

namespace App\Data\Response;

use App\Models\Notification;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

final class NotificationData extends Data
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public readonly string $id,
        public readonly int $user_id,
        public readonly ?int $sent_by,
        public readonly string $title,
        public readonly string $description,
        public readonly string $type,
        public readonly ?string $notifiable_type,
        public readonly ?int $notifiable_id,
        public readonly array $data,
        public readonly ?int $read_at,
        public readonly ?int $created_at,
        public readonly UserData|Lazy|null $sender,
        public readonly UserData|Lazy|null $user,
    ) {}

    /**
     * Build a NotificationData instance from a Notification Eloquent model.
     */
    public static function fromModel(Notification $model): self
    {
        return new self(
            id: $model->id,
            user_id: $model->user_id,
            sent_by: $model->sent_by,
            title: $model->title,
            description: $model->description,
            type: $model->type,
            notifiable_type: $model->notifiable_type,
            notifiable_id: $model->notifiable_id,
            data: $model->data ?? [],
            read_at: $model->read_at,
            created_at: $model->created_at,
            sender: Lazy::whenLoaded('sender', $model, fn () => UserData::fromModel($model->sender)),
            user: Lazy::whenLoaded('user', $model, fn () => UserData::fromModel($model->user)),
        );
    }
}
