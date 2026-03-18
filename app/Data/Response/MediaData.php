<?php

declare(strict_types = 1);

namespace App\Data\Response;

use App\Models\Media;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
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
        public readonly int $created_at,
        public readonly int $updated_at,
        public readonly string $url,
        public readonly ?string $cdn_url,
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
            created_at: $model->created_at,
            updated_at: $model->updated_at,
            url: $model->getUrl(),
            cdn_url: self::getCdnUrl($model),
        );
    }

    private static function getCdnUrl(Media $model): ?string
    {
        $cdnEnabled = config('media.cdn_enable');
        $cdnUrl = rtrim((string) config('media.cdn_url'), '/');

        if (! $cdnEnabled || $model->disk !== 's3' || $cdnUrl === '') {
            return null;
        }

        $directory = trim((string) $model->directory, '/');
        $filename = $model->filename;
        $extension = $model->extension;

        if ($directory !== '' && $filename !== null && $extension !== null) {
            return sprintf('%s/%s/%s.%s', $cdnUrl, $directory, $filename, $extension);
        }

        return null;
    }
}
