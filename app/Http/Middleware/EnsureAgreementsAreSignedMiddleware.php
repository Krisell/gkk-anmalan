<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;

class EnsureAgreementsAreSignedMiddleware
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
        if (! auth()->user() || $this->bothAgreementsAreSigned(auth()->user())) {
            return $next($request);
        }

        return redirect('/sign-agreements');
    }

    /**
     * Determine if the user has signed both agreements.
     */
    private function bothAgreementsAreSigned(User $user)
    {
        return ! empty($user->membership_agreement_signed_at) && ! empty($user->anti_doping_agreement_signed_at);
    }
}
