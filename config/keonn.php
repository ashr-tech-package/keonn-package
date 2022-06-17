<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Set the base URL used on the KEONN API.
    |
    */

    'base_url' => env('KEONN_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Keonn SFTP
    |--------------------------------------------------------------------------
    |
    | Set the sftp driver for uploading image/video.
    |
    */

    'disks' => [
        'keonn_sftp' => [
            'driver' => 'sftp',
            'host' => env('KEONN_SFTP_HOST'),

            // Settings for basic authentication...
            'username' => env('KEONN_SFTP_USERNAME'),
            'password' => env('KEONN_SFTP_PASSWORD'),

            // Optional SFTP Settings...
            'port' => (int) env('KEONN_SFTP_PORT', 22),
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Keonn App Mode
    |--------------------------------------------------------------------------
    |
    | Set mode app: pre/pro
    | pre for sandbox or testing
    | pro for production
    |
    */

    'app_mode' => env('KEONN_APP_MODE'),

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | keonn username & password for authentication purpose
    |
    */

    'username' => env('KEONN_USERNAME'),

    'password' => env('KEONN_PASSWORD'),

    'grant_type' => env('KEONN_GRANT_TYPE', 'password'),

    'client_id' => env('KEONN_CLIENT_ID', 'cloud'),

    /*
    |--------------------------------------------------------------------------
    | Laravel HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Here is the list of value that will be used on the Laravel HTTP Client Configuration.
    |
    */

    'request_retry_times' => env('KEONN_REQUEST_RETRY_TIMES', 2),

    'request_retry_sleep' => env('KEONN_REQUEST_RETRY_SLEEP', 0),
];