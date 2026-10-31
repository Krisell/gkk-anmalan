<?php

namespace App\Http\Middleware;

use App\Auth\MaintenanceUserProvider;
use App\Exceptions\DatabaseMaintenanceException;
use Closure;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * While DATABASE_MAINTENANCE is on, every database query throws and is shown as the maintenance page, so pages that
 * don't need the database keep working. Every request is also logged to storage/logs/maintenance-requests.log, to see
 * when DNS has stopped sending visitors to the old server.
 */
class DatabaseMaintenanceMiddleware
{
    /**
     * Member and login pages, which would otherwise render as empty guest versions of themselves.
     */
    private const BLOCKED_PATHS = [
        'login',
        'register',
        'password/*',
        'insidan',
        'insidan/*',
        'admin',
        'admin/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('gkk.database-maintenance')) {
            return $this->withServedBy($next($request));
        }

        DB::connection()->beforeExecuting(fn () => throw new DatabaseMaintenanceException);

        $guard = Auth::guard('web');

        if ($guard instanceof SessionGuard) {
            $guard->setProvider(new MaintenanceUserProvider);
        }

        $response = $request->isMethodSafe() && ! $request->is(...self::BLOCKED_PATHS)
            ? $next($request)
            : (new DatabaseMaintenanceException)->render($request);

        // Laravel Cloud's health checks would otherwise drown out the visitors in the log
        if (! $request->is('up')) {
            $this->log($request, $response);
        }

        return $this->withServedBy($response);
    }

    private function log(Request $request, Response $response): void
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/maintenance-requests.log'),
        ])->info(\sprintf(
            '%s %s %s %s %d %s "%s"',
            config('gkk.served-by') ?? '-',
            $request->getHost(),
            $request->method(),
            $request->getRequestUri(),
            $response->getStatusCode(),
            $request->ip(),
            $request->userAgent(),
        ));
    }

    private function withServedBy(Response $response): Response
    {
        if (config('gkk.served-by')) {
            $response->headers->set('X-Served-By', config('gkk.served-by'));
        }

        return $response;
    }
}
