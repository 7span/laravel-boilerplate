<?php

namespace App\Data\Concerns;

use App\Data\MediaData;
use Spatie\LaravelData\Optional;

trait InteractsWithRequestedMedia
{
    /**
     * Parse request()->input('media') into a normalized list of tags.
     *
     * Supports:
     * - ?media=profile_image,cover
     * - ?media[]=profile_image&media[]=cover
     * - tolerates quotes: media="profile_image"
     *
     * @return array<int, string>
     */
    protected static function requestedMediaTags(): array
    {
        $mediaParam = request()->input('media');

        if (is_array($mediaParam)) {
            $mediaParam = implode(',', $mediaParam);
        }

        $mediaParam = (string) ($mediaParam ?? '');
        if ($mediaParam === '') {
            return [];
        }

        return collect(explode(',', $mediaParam))
            ->map(fn (string $v) => trim($v, " \t\n\r\0\x0B\"'"))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected static function mediaRequested(string $tag): bool
    {
        return in_array($tag, static::requestedMediaTags(), true);
    }

    /**
     * Equivalent of ResourceFilterable::whenLoadedMedia(..., true) for DTOs:
     * - if the tag isn't requested => omit the key (Optional)
     * - if requested but no media exists => null
     * - if requested and exists => MediaData
     */
    protected static function firstMediaDataOrOptional(object $model, string $tag): MediaData|Optional|null
    {
        if (! static::mediaRequested($tag)) {
            return Optional::create();
        }

        if (! method_exists($model, 'getMedia')) {
            return Optional::create();
        }

        $media = $model->getMedia($tag)->first();

        return $media ? MediaData::fromModel($media) : null;
    }

    /**
     * Equivalent of ResourceFilterable::whenLoadedMedia(..., false) for DTOs:
     * - if the tag isn't requested => omit the key (Optional)
     * - if requested => returns a MediaData collection (transforms to array in output)
     *
     * @return \Spatie\LaravelData\DataCollection<int, MediaData>|Optional
     */
    protected static function mediaDataCollectionOrOptional(object $model, string $tag)
    {
        if (! static::mediaRequested($tag)) {
            return Optional::create();
        }

        if (! method_exists($model, 'getMedia')) {
            return Optional::create();
        }

        /** @var iterable $collection */
        $collection = $model->getMedia($tag);

        // return collection of MediaData objects; Spatie Data will transform correctly
        return MediaData::collect($collection);
    }
}
