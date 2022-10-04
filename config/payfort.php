<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | This value is used to tell the sdk which method you want to used for
    | the payments, at this time only "Merchant Page 2" is in working
    | following will be available soon.
    |
    | redirection, merchant page, naps, sadad
    |
    */
    'payment_method' => env('PAYFORT_PAYMENT_METHOD', 'merchant_page_2'),

    /*
    |--------------------------------------------------------------------------
    | Access Code
    |--------------------------------------------------------------------------
    |
    | This value is used to check and authenticate your
    | request on the Payfort
    |
    | Example: zx0IPmPy5jp1vAz8Kpg7
    |
    */
    'access_code' => env('PAYFORT_ACCESS_CODE', 'nzgQBkXBmDfGIoDKDI8V'),

    /*
    |--------------------------------------------------------------------------
    | Merchant Identifier
    |--------------------------------------------------------------------------
    |
    | This value is the unique identity of your
    | application on the Payfort
    |
    */
    'merchant_identifier' => env('PAYFORT_MERCHANT_IDENTIFIER', 'FudxRueQ'),

    /*
    |--------------------------------------------------------------------------
    | SHA Request Phrase
    |--------------------------------------------------------------------------
    |
    | This value is use to create the signature on all request
    | your application is making to the Payfort
    |
    */
    'sha_request_phrase' => env('PAYFORT_SHA_REQUEST_PHRASE', 'TESTSHAIN'),

    /*
    |--------------------------------------------------------------------------
    | SHA Response Phrase
    |--------------------------------------------------------------------------
    |
    | This value is use to verify the signature of all responses
    | coming to you application from the Payfort
    |
    */
    'sha_response_phrase' => env('PAYFORT_RESPONSE_PHRASE', 'TESTSHAOUT'),

    /*
    |--------------------------------------------------------------------------
    | Language
    |--------------------------------------------------------------------------
    |
    | The language for the checkout page
    | and the messages
    |
    */
    'language' => env('PAYFORT_LANGUAGE', 'ar'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The currency of the transaction’s amount in ISO code 3.
    |
    | E.g AED, KWD
    |
    */
    'currency' => env('PAYFORT_CURRENCY', 'KWD'),

    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    |
    | The type of the secure hash table
    |
    | Available values are sha128, sha256
    |
    */
    'encryption' => 'sha256',

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    |
    | The environment of you application
    |
    | Available values are sha128, sha256
    |
    */
    'sandbox_mode' => env('PAYFORT_SANDBOX_MODE', 1),
];
