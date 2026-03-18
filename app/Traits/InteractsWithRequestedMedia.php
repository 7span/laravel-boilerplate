<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Data\Response\MediaData;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\DataCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Plank\Mediable\Mediable
 */
trait InteractsWithRequestedMedia
{
    /**
     * Extract media tags from ?media=profile_image,cover
     *
     * @return array<int, string>
     */
    protected static function mediaTags(): array
    {
        $mediaParam = trim(request()->query('media', ''));

        if ($mediaParam === '') {
            return [];
        }

        return collect(explode(',', $mediaParam))
            ->map(static fn (string $tag) => trim($tag))
            ->filter(static fn (string $tag) => $tag !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Determine if the client wants a specific media tag.
     */
    protected static function shouldIncludeMedia(string $tag): bool
    {
        return in_array($tag, static::mediaTags(), true);
    }

    /**
     * Return first media item for a tag or omit field.
     *
     * - If tag not requested → field omitted
     * - If requested but not found → null
     * - If found → MediaData
     */
    protected static function firstMedia(Model $model, string $tag): MediaData|Optional|null
    {
        if (! static::shouldIncludeMedia($tag)) {
            return Optional::create();
        }

        $media = $model->getMedia($tag)->first(); // @phpstan-ignore-line

        return $media ? MediaData::fromModel($media) : null;
    }

    /**
     * Return media collection for a tag or omit field.
     *
     * @return DataCollection<int, MediaData>|Optional
     */
    protected static function getMediaCollection(Model $model, string $tag): DataCollection|Optional
    {
        if (! static::shouldIncludeMedia($tag)) {
            return Optional::create();
        }

        $items = $model // @phpstan-ignore-line
            ->getMedia($tag)
            ->map(static fn ($media) => MediaData::fromModel($media));

        return new DataCollection(MediaData::class, $items);
    }
}
