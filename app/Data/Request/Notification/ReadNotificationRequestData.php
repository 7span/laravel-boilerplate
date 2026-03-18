<?php

declare(strict_types = 1);

namespace App\Data\Request\Notification;

use Spatie\LaravelData\Data;

final class ReadNotificationRequestData extends Data
{
    /**
     * @param  array<int, string>|null  $ids
     */
    public function __construct(
        public readonly ?array $ids,
    ) {}

    /**
     * Build a ReadNotificationRequestData instance from validated request array.
     *
     * @param  array{ids?: array<int, string>|null}  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            ids: $data['ids'] ?? null,
        );
    }
}
