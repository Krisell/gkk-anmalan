<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_unless(collect(['admin', 'superadmin'])->contains(auth()->user()->role), 401);

        return $next($request);
    }
}
