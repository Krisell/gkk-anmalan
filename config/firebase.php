<?php

return [
    'service_account_email' => env('FIREBASE_SERVICE_ACCOUNT_EMAIL'),
    'private_key' => env('FIREBASE_PRIVATE_KEY'),

    // The Realtime Database the app writes to (the screen slideshow's version, see SlideshowSnapshot). Set only in
    // production: without it the app writes nothing, so a local .env with the production key can't write there.
    'database_url' => env('FIREBASE_DATABASE_URL'),
];
