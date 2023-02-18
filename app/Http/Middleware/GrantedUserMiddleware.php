<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GrantedUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->granted_by == 0) {
            return redirect('/insidan');
        }

        return $next($request);
    }
}
