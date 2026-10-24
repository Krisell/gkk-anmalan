<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/insidan';

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Json::encodeUsing(
            fn ($value) => \json_encode($value, JSON_UNESCAPED_UNICODE),
        );

        // PIN attempts for the live overlay's remote control, counted on their own: the overlay polls the feed from
        // the same network (the venue's), which must not use up the attempts.
        RateLimiter::for('live-control-pin', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
    }
}
