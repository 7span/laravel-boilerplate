<?php

namespace App\Services;

use App\Models\TempFile;
use App\Libraries\MediaHelper;
use Illuminate\Support\Facades\Storage;

class SignedUrlService
{
    /**
     * Issue a temporary upload URL so the client can PUT the file straight to S3.
     *
     * A TempFile row is recorded alongside it: until attachMedia() claims the file it
     * counts as abandoned, and media:delete-temp-files will remove it.
     *
     * @param  array<string, mixed>  $inputs
     * @return array{url: string, key: string, directory: string, filename: string}
     */
    public function create(array $inputs): array
    {
        $directory = config("media.directory.{$inputs['type']}", config('media.directory.default'));

        $filename = MediaHelper::createFileName($inputs['filename'], $inputs['mime_type']);
        
        $key = "{$directory}/{$filename}";

        ['url' => $url] = Storage::disk('s3')->temporaryUploadUrl($key, now()->addMinutes(20));

        TempFile::create([
            'disk' => 's3',
            'directory' => $directory,
            'file_name' => $filename,
        ]);

        return [
            'url' => $url,
            'key' => $key,
            'directory' => $directory,
            'filename' => $filename,
        ];
    }
}
