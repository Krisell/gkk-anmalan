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
];
