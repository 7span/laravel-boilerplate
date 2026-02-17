<?php

namespace App\Data;

use App\Models\Media;
use Spatie\LaravelData\Data;

class MediaData extends Data
{
    public function __construct(
        public int $id,
        public ?string $disk,
        public ?string $directory,
        public ?string $filename,
        public ?string $extension,
        public ?string $mime_type,
        public ?string $aggregate_type,
        public ?int $size,
        public ?int $created_at,
        public ?int $updated_at,
        public ?string $url,
        public ?string $cdn_url,
    ) {}

    public static function fromModel(Media $media): self
    {
        return new self(
            id: $media->id,
            disk: $media->disk,
            directory: $media->directory,
            filename: $media->filename,
            extension: $media->extension,
            mime_type: $media->mime_type,
            aggregate_type: $media->aggregate_type,
            size: $media->size,
            created_at: $media->created_at,
            updated_at: $media->updated_at,
            url: $media->getUrl(),
            cdn_url: self::cdnUrl($media),
        );
    }

    private static function cdnUrl(Media $media): ?string
    {
        $cdnEnabled = (bool) config('media.cdn_enable');
        $cdnUrl = rtrim((string) config('media.cdn_url'), '/');

        if (! $cdnEnabled || ($media->disk ?? null) !== 's3' || empty($cdnUrl)) {
            return null;
        }

        $directory = trim((string) ($media->directory ?? ''), '/');
        $filename = $media->filename ?? null;
        $extension = $media->extension ?? null;

        if ($directory && $filename && $extension) {
            return sprintf('%s/%s/%s.%s', $cdnUrl, $directory, $filename, $extension);
        }

        return null;
    }
}
