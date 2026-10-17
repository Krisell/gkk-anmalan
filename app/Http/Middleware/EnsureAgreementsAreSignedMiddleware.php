<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAgreementsAreSignedMiddleware
{
    /**
     * Session key set when the user chooses to handle expiring agreements later.
     */
    public const POSTPONED_SESSION_KEY = 'agreements_reminder_postponed';

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->mustSignAgreements()) {
            return redirect('/sign-agreements');
        }

        if ($this->shouldRemindAboutExpiringAgreements($request)) {
            return redirect()->guest('/sign-agreements');
        }

        return $next($request);
    }

    /**
     * During the last month before the agreements expire the user is shown the signing page
     * once per session, but may postpone it and continue.
     */
    private function shouldRemindAboutExpiringAgreements(Request $request): bool
    {
        return $request->isMethod('GET')
            && ! $request->expectsJson()
            && ! $request->session()->get(self::POSTPONED_SESSION_KEY)
            && $request->user()->hasExpiringAgreements();
    }
}
