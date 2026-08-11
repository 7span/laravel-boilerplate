<?php

namespace App\Libraries;

use App\Models\Media;
use App\Models\TempFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    public static function createFileName(string $fileName, string $mimeType): string
    {
        $extension = self::getExtension($fileName, $mimeType);

        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $fileName = $name . '-' . Str::random(10);

        return empty($extension) ? $fileName : "{$fileName}.{$extension}";
    }

    public static function getAggregateType(string $mimeType): string
    {
        /** @var array<string, array<int, string>> $aggregateTypeLists */
        $aggregateTypeLists = config('media.aggregate_types', []);

        foreach ($aggregateTypeLists as $key => $aggregateTypes) {
            if (in_array($mimeType, $aggregateTypes, true)) {
                return $key;
            }
        }

        return 'all';
    }

    /**
     * @param  array<string, mixed>|array<int, array<string, mixed>>  $media
     * @return array<int, int>
     */
    public static function attachMedia(array $media): array
    {
        $mediaObjs = is_array($media[0] ?? null) ? $media : [$media];

        $mediaIds = [];

        foreach ($mediaObjs as $mediaObj) {
            $record = Media::updateOrCreate(
                [
                    'filename' => pathinfo($mediaObj['filename'], PATHINFO_FILENAME),
                ],
                [
                    'disk' => $mediaObj['disk'] ?? config('filesystems.default'),
                    'directory' => $mediaObj['directory'],
                    'filename' => pathinfo($mediaObj['filename'], PATHINFO_FILENAME),
                    'extension' => self::getExtension($mediaObj['filename'], $mediaObj['mime_type']),
                    'mime_type' => $mediaObj['mime_type'],
                    'size' => $mediaObj['size'],
                    'aggregate_type' => self::getAggregateType($mediaObj['mime_type']),
                ]
            );

            $mediaIds[] = $record->id;

            // Delete the entry from TempFile if it exists
            TempFile::where('file_name', $mediaObj['filename'])->delete();
        }

        return $mediaIds;
    }

    /**
     * @return array{message: string}
     */
    public static function destroyMedia(Media $fileObj): array
    {
        Storage::disk($fileObj->disk ?? config('filesystems.default'))->delete($fileObj->getDiskPath());

        $fileObj->delete();

        return [
            'message' => __('entity.entityDeleted', ['entity' => 'Media']),
        ];
    }

    public static function getExtension(string $fileName, string $mimeType): string
    {
        $fromFileName = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($mimeType === 'application/octet-stream') {
            return $fromFileName;
        }

        /** @var array<string, string> $mimeTypes */
        $mimeTypes = config('media.mime_types', []);

        return $mimeTypes[$mimeType] ?? $fromFileName;
    }
}
