<?php

use App\Providers\AppServiceProvider;
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
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
