<?php

namespace App\Services;

use Aws\S3\S3Client;
use App\Models\TempFile;
use App\Libraries\MediaHelper;

class SignedUrlService
{
    public function __construct()
    {
        //
    }

    public function create(array $inputs)
    {
        // Get S3 configuration from filesystem config
        $s3Config = config('filesystems.disks.s3');

        // Set up the S3 client
        $client = new S3Client([
            'version' => 'latest',
            'region' => $s3Config['region'],
            'credentials' => [
                'key' => $s3Config['key'],
                'secret' => $s3Config['secret'],
            ],
        ]);

        // Set the bucket name and object key
        $bucket = $s3Config['bucket'];

        // KEY means folder-name/file-name
        $directory = config('media.directory.' . $inputs['type'], 'default');

        // Generate FileName
        $fileName = MediaHelper::createFileName($inputs['filename'], $inputs['mime_type']);

        // Directory Path
        $key = $directory . '/' . $fileName;

        // Generate a pre-signed URL for uploading the object
        $command = $client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);
        $presignedRequest = $client->createPresignedRequest($command, '+20 minutes');

        TempFile::create([
            'disk' => 's3',
            'directory' => $directory,
            'file_name' => $fileName,
        ]);

        // Get the pre-signed URL
        $presignedUrl = [
            'url' => (string) $presignedRequest->getUri(),
            'key' => $key,
            'directory' => $directory,
            'filename' => $fileName,
        ];

        return $presignedUrl;
    }
}
