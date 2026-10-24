<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],
    'fortnox' => [
        'client_id' => env('FORTNOX_CLIENT_ID'),
        'client_secret' => env('FORTNOX_CLIENT_SECRET'),
    ],
    'powerliftinglive' => [
        'url' => env('POWERLIFTINGLIVE_URL', 'https://se.powerliftinglive.com'),
    ],

    // The PIN for controlling the live overlay (/live/control), e.g. from an iPad. Without one, remote control is off.
    'live_control' => [
        'pin' => env('LIVE_CONTROL_PIN'),
    ],
];
