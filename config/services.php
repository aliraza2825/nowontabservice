<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'toast' => [
        'base_url' => env('TOAST_BASE_URL', 'https://ws-api.toasttab.com'),
        'client_id' => env('TOAST_CLIENT_ID'),
        'client_secret' => env('TOAST_CLIENT_SECRET'),
        'restaurant_guid' => env('TOAST_RESTAURANT_GUID'),
        'user_access_type' => env('TOAST_USER_ACCESS_TYPE', 'TOAST_MACHINE_CLIENT'),
    ],

];
