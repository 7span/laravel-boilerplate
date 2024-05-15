<?php

namespace App\Services;

use Aws\S3\S3Client;
use App\Library\MediaHelper;

class SignedUrlService
{
    public function __construct()
    {
        //
    }

    public function create(array $inputs)
    {
        // Set up the S3 client
        $client = new S3Client([
            'version' => config('aws.version'),
            'region' => config('aws.region'),
            'credentials' => config('aws.credentials'),
        ]);

        // Set the bucket name and object key
        $bucket = config('aws.bucket');

        // KEY means folder-name/file-name
        $directory = $inputs['directory'];

        //Generate FileName
        $fileName = MediaHelper::createFileName($inputs['filename'], $inputs['mime_type']);

        //Directory Path
        $key = $directory . '/' . $fileName;

        // Generate a pre-signed URL for uploading the object
        $command = $client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);
        $presignedRequest = $client->createPresignedRequest($command, '+20 minutes');

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
