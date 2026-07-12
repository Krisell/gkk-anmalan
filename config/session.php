<?php

return [
    'serialization' => 'json',

    // Keep the pre-Laravel 13 default cookie name so existing sessions survive.
    'cookie' => env('SESSION_COOKIE', 'gkk_session'),
];
