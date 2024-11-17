<?php

return [
    'mailers' => [
        'mailgun' => [
            'transport' => 'mailgun',
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'info@gkk-styrkelyft.se'),
        'name' => env('MAIL_FROM_NAME', 'Göteborg Kraftsportklubb'),
    ],
];
