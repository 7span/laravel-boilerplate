<?php

return [
    'front_website_url' => env('FRONT_WEBSITE_URL', 'http://127.0.0.1:8000'),
    'pagination_limit' => 10,
    'master_password' => env('MASTER_PASSWORD'),
    'notification_enabled' => env('NOTIFICATION_ENABLED', false),
    'soft_delete_retention_days' => env('SOFT_DELETE_RETENTION_DAYS', 90),
    'roles' => [
        'admin' => 'admin',
        'user' => 'user',
    ],

    /*
     * Keys of the `settings` table that PUT /api/v1/admin/settings may update.
     * Every key listed here becomes a required field of the request.
     */
    'setting_keys' => [],
    'otp' => [
        'master_otp' => env('MASTER_OTP'),
        'expiration_time_in_minutes' => 10,
        'length' => 6,
    ],
];
