<?php

return [
    'otp_expiration_time_in_minutes' => 10,
    'generate_otp_length' => '6',
    'front_website_url' => env('FRONT_WEBSITE_URL', 'http://127.0.0.1:8000'),
    'pagination' => [
        'limit' => 10,
    ],
    'user_status' => [
        'active' => 'active',
        'inactive' => 'inactive',
    ],
];
