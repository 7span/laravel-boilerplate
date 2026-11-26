<?php

return [
    // Login credentials
    'username' => env('DEVELOPER_USERNAME'),
    'password' => env('DEVELOPER_PASSWORD'),

    // The key as which the developer session is stored
    'sessionKey' => 'developer.loggedin',

    // The route to which the middleware redirects if a user isn't authenticated
    'authRoute' => 'developer/login',
];
