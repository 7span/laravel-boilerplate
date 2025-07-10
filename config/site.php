<?php

return [
    'front_website_url' => env('FRONT_WEBSITE_URL', 'http://127.0.0.1:8000'),
    'pagination_limit' => 10,
    'master_password' => env('MASTER_PASSWORD'),
    'onesignal' => [
        'app_id' => env('ONESIGNAL_APP_ID'),
        'api_key' => env('ONESIGNAL_API_KEY'),
    ],
    'roles' => [
        'admin' => 'admin',
        'user' => 'user',
    ],
    'roleIds' => [
        'admin' => 1,
        'user' => 2,
    ],
    'otp' => [
        'master_otp' => env('MASTER_OTP'),
        'expiration_time_in_minutes' => 10,
        'length' => 6,
        'type' => [
            'email_verification' => 'email_verification',
            'forget_password' => 'forget_password',
        ],
    ],
    'user_status' => [
        'active' => 'active',
        'inactive' => 'inactive',
    ],
];
