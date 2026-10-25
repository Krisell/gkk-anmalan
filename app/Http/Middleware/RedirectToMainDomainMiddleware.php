<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The club's other domains point to the same app and are permanently redirected to the main domain, keeping the path.
 * Other hosts (the laravel.cloud preview URL, local development) are left alone.
 */
class RedirectToMainDomainMiddleware
{
    private const MAIN_URL = 'https://goteborgkk.se';

    private const REDIRECTED_HOSTS = [
        'www.goteborgkk.se',
        'gkk-styrkelyft.se',
        'www.gkk-styrkelyft.se',
        'goteborgkraftsportklubb.se',
        'www.goteborgkraftsportklubb.se',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (\in_array(\strtolower($request->getHost()), self::REDIRECTED_HOSTS, true)) {
            return redirect()->away(self::MAIN_URL.$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
