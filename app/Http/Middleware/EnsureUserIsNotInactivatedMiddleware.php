<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EnsureUserIsNotInactivatedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->user()) {
            return $next($request);
        }

        if (auth()->user()->inactivated_at && Route::currentRouteName() !== 'inactivated') {
            return redirect('/inactivated');
        }

        return $next($request);
    }
}
