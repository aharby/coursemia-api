<?php

return [

    'paths' => ['api/*', 'all-admins'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // or whatever your Vue dev server runs on

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // if you're using cookies/auth
];
