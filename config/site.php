<?php

return [
    'otpExpirationTimeInMinutes' => 10,
    'generateOtpLength' => '6',
    'media_tags' => [
        'profile_image' => 'profile_image',
    ],
    'aggregate_types' => [
        'image' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ],
    ],
    'disk' => [
        'profile_image' => 'profile_image',
    ],
    'pagination' => [
        'limit' => 10,
    ],

    'user_status' => [
        'active' => 'active',
        'inactive' => 'inactive',
    ],

    'media_type' => [
        'sync_media' => 'sync_media',
        'attach_media' => 'attach_media',
    ],
];
