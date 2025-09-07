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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Payment providers for webhooks
    'paystack' => [
        'secret' => env('PAYSTACK_SECRET'),
        'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
    ],
    'flutterwave' => [
        'secret' => env('FLUTTERWAVE_SECRET'),
        'webhook_secret' => env('FLW_WEBHOOK_SECRET', env('FLUTTERWAVE_WEBHOOK_SECRET')),
    ],
    'monnify' => [
        'secret' => env('MONNIFY_SECRET'),
        'webhook_secret' => env('MONNIFY_WEBHOOK_SECRET'),
    ],

];
