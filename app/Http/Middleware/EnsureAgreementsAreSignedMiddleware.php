<?php

namespace App\Http\Middleware;

use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class EnsureAgreementsAreSignedMiddleware
{
    /**
     * Handle an incoming request.
     *
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
        $membership = $user->membership_agreement_signed_at;
        if (! $membership) {
            return false;
        }

        $membership = Carbon::parse($membership);
        if ($membership->lessThan(now()->subYear())) {
            return false;
        }

        $lastUpdatedMembershipAgreement = config('agreements.membership.last_updated_at');
        if ($lastUpdatedMembershipAgreement && $membership->lessThan($lastUpdatedMembershipAgreement)) {
            return false;
        }

        $antiDoping = $user->anti_doping_agreement_signed_at;
        if (! $antiDoping) {
            return false;
        }

        $antiDoping = Carbon::parse($antiDoping);
        if ($antiDoping->lessThan(now()->subYear())) {
            return false;
        }

        $lastUpdatedAntiDopingAgreement = config('agreements.anti_doping.last_updated_at');
        if ($lastUpdatedAntiDopingAgreement && $antiDoping->lessThan($lastUpdatedAntiDopingAgreement)) {
            return false;
        }

        return true;
    }
}
