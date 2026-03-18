<?php
declare(strict_types = 1);

namespace App\Data\Response;

use App\Models\Media;
use Spatie\LaravelData\Data;

final class MediaData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $disk,
        public readonly string $directory,
        public readonly string $filename,
        public readonly string $extension,
        public readonly string $mime_type,
        public readonly string $aggregate_type,
        public readonly int $size,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly string $url,
        public readonly string $cdn_url,
    ) {}

    /**
     * Build a MediaData instance from a Media Eloquent model.
     */
    public static function fromModel(Media $model): self
    {
        return new self(
            id: $model->id,
            disk: $model->disk,
            directory: $model->directory,
            filename: $model->filename,
            extension: $model->extension,
            mime_type: $model->mime_type,
            aggregate_type: $model->aggregate_type,
            size: $model->size,
            created_at: $model->created_at?->toDateTimeString(),
            updated_at: $model->updated_at?->toDateTimeString(),
            url: $model->url,
            cdn_url: $model->cdn_url,
        );
    }
}
