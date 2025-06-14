<?php

namespace App\Http\Middleware;

use App\Events\UserWasActiveEvent;
use Closure;

class LogVisitsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()?->wasLoggedInByAdmin) {
            return $next($request);
        }

        if ($request->user() && (! $request->user()->last_visited_at || now()->subMinutes(30)->gt($request->user()->last_visited_at))) {
            UserWasActiveEvent::dispatch($request->user());
        }

        return $next($request);
    }
}
