<?php

namespace App\Libraries;

use App\Models\Media;
use App\Models\TempFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    public static function createFileName($fileName, $mimeType)
    {
        $extension = self::getExtension($fileName, $mimeType);

        $fileNameArr = explode('.', $fileName);
        $fileName = $fileNameArr[0] . '-' . Str::random(10) . '.' . $extension;

        return $fileName;
    }

    public static function getAggregateType($mimeType)
    {
        $aggregateTypeLists = config('media.aggregate_types');

        $aggregateType = '';

        foreach ($aggregateTypeLists as $key => $aggregateTypes) {
            if (in_array($mimeType, $aggregateTypes)) {
                $aggregateType = $key;
                break;
            }
        }

        return ! empty($aggregateType) ? $aggregateType : 'all';
    }

    public static function attachMedia($media)
    {
        $mediaIds = [];
        if (! is_array($media[0] ?? null)) {
            $media = [$media];
        }

        foreach ($media as $mediaObj) {
            $extension = self::getExtension($mediaObj['filename'], $mediaObj['mime_type']);
            $aggregateType = self::getAggregateType($mediaObj['mime_type']);
            $fileName = explode('.', $mediaObj['filename'])[0];

            $media = Media::updateOrCreate(
                [
                    'filename' => $fileName,
                ],
                [
                    'disk' => $mediaObj['disk'] ?? config('filesystems.default'),
                    'directory' => $mediaObj['directory'],
                    'filename' => $fileName,
                    'extension' => $extension,
                    'mime_type' => $mediaObj['mime_type'],
                    'size' => $mediaObj['size'],
                    'aggregate_type' => $aggregateType,
                ]
            );

            array_push($mediaIds, $media->id);

            // Delete the entry from TempFile if it exists
            TempFile::where('file_name', $mediaObj['filename'])->delete();
        }

        return $mediaIds;
    }

    public static function destroyMedia($fileObj)
    {
        $imageUrl = $fileObj['directory'] . '/' . $fileObj['filename'] . '.' . $fileObj['extension'];
        Storage::disk(config('filesystems.default'))->delete($imageUrl);
        $fileObj->delete();
        $data['message'] = __('entity.entityDeleted', ['entity' => 'Media']);

        return $data;
    }

    public static function getExtension($fileName, $mimeType)
    {
        $mimeTypes = config('media.mime_types');
        $extension = null;

        if ($mimeType === 'application/octet-stream') {
            $extension = explode('.', $fileName)[1];
        } else {
            $extension = isset($mimeTypes[$mimeType]) ? $mimeTypes[$mimeType] : explode('.', $fileName)[1];
        }

        return $extension;
    }
}
