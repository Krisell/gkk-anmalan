<?php

namespace App\Providers;

use App\Models\Result;
use App\Models\Slide;
use App\Services\SlideshowSnapshot;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Scheduling\Event as ScheduledTask;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
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

        // The live screens and overlays poll the feed every second, all from the venue's one public IP: a plain
        // throttle:300,1 (shared by all routes using it) ran out with about five screens. Upstream requests are cached,
        // so this only guards against runaway clients: room for ~25 screens.
        RateLimiter::for('live-feed', fn (Request $request) => Limit::perMinute(2000)->by($request->ip()));
        RateLimiter::for('live-competitions', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
        // A lifter's bests from the federation's results database, looked up from the live control (cached per lifter).
        RateLimiter::for('live-lifter-bests', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));

        // The screen slideshow polls a published snapshot of the slides and club records (see SlideshowSnapshot).
        Slide::saved(fn () => SlideshowSnapshot::publishSoon());
        Slide::deleted(fn () => SlideshowSnapshot::publishSoon());
        Result::saved(fn () => SlideshowSnapshot::publishSoon());
        Result::deleted(fn () => SlideshowSnapshot::publishSoon());
        Result::restored(fn () => SlideshowSnapshot::publishSoon());

        // The scheduled tasks print nothing themselves, so without this nothing in Laravel Cloud's logs shows that
        // they ran. Find them with: cloud env:logs <app> <environment> --query="Scheduled task"
        Event::listen(function (ScheduledTaskFinished $event) {
            $line = \sprintf('Scheduled task %s finished in %.1f s', self::taskName($event->task), $event->runtime);

            $event->task->exitCode
                ? Log::warning("{$line} with exit code {$event->task->exitCode}")
                : Log::info($line);
        });

        Event::listen(fn (ScheduledTaskFailed $event) => Log::error(
            \sprintf('Scheduled task %s failed: %s', self::taskName($event->task), $event->exception->getMessage()),
        ));
    }

    /**
     * The artisan command without the PHP and artisan paths, for example "gkk:publish-slideshow".
     */
    private static function taskName(ScheduledTask $task): string
    {
        return $task->description ?? \trim(\str_replace(['php artisan', 'artisan'], '', ScheduledTask::normalizeCommand($task->command ?? '')));
    }
}
