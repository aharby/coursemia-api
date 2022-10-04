<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_ID'),
        'client_secret' => env('FACEBOOK_SECRET'),
        'redirect' => env('FACEBOOK_URL'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_ID', '9JUqOsv0FOiIPRsEAxk6eDo88'),
        'client_secret' => env('TWITTER_SECRET', 'hsRJOyHJkP6mfBl5umJNyF2wHOPUL05vbu3iVacQALCq0XU6Dh'),
        'redirect' => env('TWITTER_URL', 'https//ouredu.testenv.tech/twitter/callback'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    // temporary key
    // todo: make sure you give the sender id to mbile app dev to use it when generate device token
    'fcm' => [
        'key' => env('FCM_SECRET_KEY', 'AAAAdJ0lfs4:APA91bHNCt4wyEMJYFaNBYSIGcotfW6YRNKXNFpmmaf1i28jVjWjMQqcvtajqUjv3unxyRPixh8DX_wczXi0Qvb1Y7NeDCV4yfcQNJSEZrbRzA0JNyOWPnJwhqO0P-lTZz_vchW_QAHh'),
     ],
    'opentok' => [
        'api_key' => env('OPEN_TOK_API_KEY', '46514992'),
        'api_secret' => env('OPEN_TOK_API_SECRET', '128bcd64d9c9fc02738dfa53cb989b1bfbd3aa05'),
    ],
    'yamamah' => [
        'username' => env('YAMAMAH_USERNAME',''),
        'password' => env('YAMAMAH_PASSWORD',''),
        'tagName' => 'Ouredu'
    ]
];
