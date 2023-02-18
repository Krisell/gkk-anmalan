<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_unless(collect(['admin', 'superadmin'])->contains(auth()->user()->role), 401);

        return $next($request);
    }
}
