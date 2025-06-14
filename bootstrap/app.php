<?php

use App\Providers\AppServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

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

        $middleware->web([
            \App\Http\Middleware\LogVisitsMiddleware::class,
            \App\Http\Middleware\EnsureUserIsNotInactivatedMiddleware::class,
        ]);

        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Due to php setting disable_functions, we cannot use the schedule:work command.

        // [2025-06-13 11:30:11] production.ERROR: The Process class relies on proc_open, which is not available
        // on your PHP installation. {"exception":"[object] (Symfony\\Component\\Process\\Exception\\LogicException(code: 0):
        // The Process class relies on proc_open, which is not available on your PHP installation. at
        // /customers/9/a/3/goteborgkk.se/httpd.private/web/vendor/symfony/process/Process.php:149)
        // $schedule->command('fortnox:verify-payment --all')->dailyAt('07:30')->timezone('Europe/Stockholm');
        // $schedule->command('fortnox:verify-payment --all')->dailyAt('13:30')->timezone('Europe/Stockholm');

        // The current workaround is to run artisan commands directly in a webhook request cycle.
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
