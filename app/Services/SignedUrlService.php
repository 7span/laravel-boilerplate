<?php

declare(strict_types=1);

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

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, string>
     */
    public function create(array $inputs): array
    {
        // Get S3 configuration from filesystem config
        /** @var array<string, string> $s3Config */
        $s3Config = (array) config('filesystems.disks.s3');

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
        $v1 = $inputs['type'] ?? 'default';
        $type = is_scalar($v1) ? (string) $v1 : 'default';
        $v2 = config('media.directory.' . $type, 'default');
        /** @var string $directory */
        $directory = is_scalar($v2) ? (string) $v2 : 'default';

        // Generate FileName
        /** @var string $filename */
        $filename = $inputs['filename'];
        /** @var string $mimeType */
        $mimeType = $inputs['mime_type'];
        $fileName = MediaHelper::createFileName($filename, $mimeType);

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
