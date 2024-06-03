<?php

return [
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID', ''),
        'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
    ],
    'region' => env('AWS_REGION', 'us-east-1'),
    'bucket' => env('AWS_BUCKET', ''),
    'version' => 'latest',

    // You can override settings for specific services
    'Ses' => [
        'region' => 'us-east-1',
    ],
];
