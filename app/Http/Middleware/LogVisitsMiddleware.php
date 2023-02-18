<?php

namespace App\Http\Middleware;

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
        if (auth()->user() && (! auth()->user()->last_visited_at || now()->subMinutes(30)->gt(auth()->user()->last_visited_at))) {
            auth()->user()->update([
                'visits' => auth()->user()->visits + 1,
                'last_visited_at' => now(),
            ]);
        }

        return $next($request);
    }
}
