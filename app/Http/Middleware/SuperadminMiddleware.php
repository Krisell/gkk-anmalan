<?php

namespace App\Http\Middleware;

use Closure;

class SuperadminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_unless(collect(['superadmin'])->contains(auth()->user()->role), 401);

        return $next($request);
    }
}
