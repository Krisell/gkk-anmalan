<?php

use App\Http\Middleware\DatabaseMaintenanceMiddleware;
use App\Http\Middleware\EnsureUserIsNotInactivatedMiddleware;
use App\Http\Middleware\LogVisitsMiddleware;
use App\Http\Middleware\LowercaseEmails;
use App\Http\Middleware\RedirectToMainDomainMiddleware;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Sentry\Laravel\Integration;

return Application::configure(basePath: \dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        using: function () {
            Route::middleware('web')->group(base_path('routes/web.php'));

            if (app()->environment() === 'testing') {
                Route::middleware('web')->group(base_path('/routes/e2e.php'));
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->validateCsrfTokens(except: [
            '/auth/*',
            '/admin/news/email/preview',
            '*__e2e__*',
        ]);

        // Before everything else: the other domains are redirected even during maintenance, and the maintenance mode
        // also covers route model binding and requests outside the web group
        $middleware->prepend([
            RedirectToMainDomainMiddleware::class,
            DatabaseMaintenanceMiddleware::class,
        ]);

        $middleware->web([
            LowercaseEmails::class,
            LogVisitsMiddleware::class,
            EnsureUserIsNotInactivatedMiddleware::class,
        ]);

        $middleware->priority([
            StartSession::class,
            ShareErrorsFromSession::class,
            ThrottleRequests::class,
            AuthenticateSession::class,
            SubstituteBindings::class,
            Authorize::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Laravel Cloud wakes the app (scaled to zero) when a task is due, and the app then stays awake for the sleep
        // timeout. Keep the tasks few and grouped, and avoid withoutOverlapping/onOneServer, whose cache lookups would
        // also wake the database. Cloud reads the schedule at deploy time, so changes here need a deploy.
        //
        // The times are in UTC on purpose. Cloud stores `schedule:list` as it looks on deploy, which converts a
        // Europe/Stockholm time with that day's offset. After the next daylight saving switch it would wake the app an
        // hour off and the task would not run. In Swedish time these run an hour later in summer.
        $schedule->command('gkk:notify-competition-deadlines')->dailyAt('06:20')->timezone('UTC');

        // Safety net in case a publish after a change failed. Runs in the same wake as the task above. (Cloud also runs
        // it as a deploy command, so that a deploy reloads the open screens.)
        $schedule->command('gkk:publish-slideshow')->dailyAt('06:20')->timezone('UTC');

        $schedule->command('fortnox:verify-payment --all')->dailyAt('06:30')->timezone('UTC');
        $schedule->command('fortnox:verify-payment --all')->dailyAt('12:30')->timezone('UTC');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
