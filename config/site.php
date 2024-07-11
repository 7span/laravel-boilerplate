<?php

return [
    'otpExpirationTimeInMinutes' => 10,
    'generateOtpLength' => '6',
    'frontWebsiteUrl' => env('FRONT_WEBSITE_URL', 'http://127.0.0.1:8000'),
    'pagination' => [
        'limit' => 10,
    ],
    'user_status' => [
        'active' => 'active',
        'inactive' => 'inactive',
    ],
];
