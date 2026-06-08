<?php

declare(strict_types=1);

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

        $fileNameArr = explode('.', $fileName);
        $fileName = $fileNameArr[0] . '-' . Str::random(10) . '.' . $extension;

        return $fileName;
    }

    public static function getAggregateType(string $mimeType): string
    {
        /** @var array<string, array<int, string>> $aggregateTypeLists */
        $aggregateTypeLists = (array) config('media.aggregate_types', []);

        $aggregateType = '';

        foreach ($aggregateTypeLists as $key => $aggregateTypes) {
            if (in_array($mimeType, $aggregateTypes, true)) {
                $aggregateType = $key;
                break;
            }
        }

        return ! empty($aggregateType) ? $aggregateType : 'all';
    }

    /**
     * @param array<int, array<string, mixed>>|array<string, mixed> $media
     * @return array<int, int>
     */
    public static function attachMedia(array $media): array
    {
        /** @var array<int, int> $mediaIds */
        $mediaIds = [];
        
        /** @var array<int, array<string, mixed>> $mediaItems */
        $mediaItems = (isset($media[0]) && is_array($media[0])) ? $media : [$media];

        foreach ($mediaItems as $mediaObj) {
            $filename = is_scalar($v1 = $mediaObj['filename'] ?? null) ? (string) $v1 : '';
            $mimeType = is_scalar($v2 = $mediaObj['mime_type'] ?? null) ? (string) $v2 : '';
            $size = is_scalar($v3 = $mediaObj['size'] ?? null) ? (int) $v3 : 0;
            $disk = is_scalar($v4 = ($mediaObj['disk'] ?? config('filesystems.default', 'local'))) ? (string) $v4 : 'local';
            $directory = is_scalar($v5 = $mediaObj['directory'] ?? null) ? (string) $v5 : '';

            $extension = self::getExtension($filename, $mimeType);
            $aggregateType = self::getAggregateType($mimeType);
            
            $nameOnly = explode('.', $filename)[0];

            $mediaModel = Media::updateOrCreate(
                [
                    'filename' => $nameOnly,
                ],
                [
                    'disk' => $disk,
                    'directory' => $directory,
                    'filename' => $nameOnly,
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'aggregate_type' => $aggregateType,
                ]
            );

            $mediaId = is_scalar($id = $mediaModel->getKey()) ? (int) $id : 0;
            array_push($mediaIds, $mediaId);

            // Delete the entry from TempFile if it exists
            TempFile::where('file_name', $filename)->delete();
        }

        return $mediaIds;
    }

    /**
     * @return array<string, string>
     */
    public static function destroyMedia(Media $fileObj): array
    {
        $directory = $fileObj->directory ?? '';
        $filename = $fileObj->filename ?? '';
        $extension = $fileObj->extension ?? '';
        $imageUrl = $directory . '/' . $filename . '.' . $extension;
        Storage::disk(is_string($disk = config('filesystems.default')) ? $disk : 'local')->delete($imageUrl);
        $fileObj->delete();
        $data = ['message' => (string) __('entity.entityDeleted', ['entity' => 'Media'])];

        return $data;
    }

    public static function getExtension(string $fileName, string $mimeType): string
    {
        /** @var array<string, string> $mimeTypes */
        $mimeTypes = (array) config('media.mime_types', []);
        $extension = null;

            $extension = isset($mimeTypes[$mimeType]) ? $mimeTypes[$mimeType] : explode('.', $fileName)[1];

        return $extension;
    }
}
