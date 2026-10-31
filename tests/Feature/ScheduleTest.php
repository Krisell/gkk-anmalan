<?php

use Illuminate\Support\Facades\Artisan;

// Laravel Cloud reads `schedule:list` at deploy time to know when to wake the app for the scheduled tasks.
test('the scheduled tasks run at fixed UTC times', function () {
    // Not in Europe/Stockholm, since Cloud would otherwise wake the app at the wrong time after a daylight saving switch.
    Artisan::call('schedule:list', ['--timezone' => 'UTC']);

    $lines = collect(\explode("\n", Artisan::output()))
        ->filter(fn ($line) => \str_contains($line, 'artisan'))
        ->map(fn ($line) => \preg_replace('/\s+/', ' ', \trim(str($line)->before('Next Due')->rtrim(' .'))))
        ->sort()
        ->values()
        ->all();

    expect($lines)->toBe([
        '20 6 * * * php artisan gkk:notify-competition-deadlines',
        '20 6 * * * php artisan gkk:publish-slideshow',
        '30 12 * * * php artisan fortnox:verify-payment --all',
        '30 6 * * * php artisan fortnox:verify-payment --all',
    ]);
});
