<?php

namespace App\Http\Middleware;

use App\Auth\MaintenanceUserProvider;
use App\Exceptions\DatabaseMaintenanceException;
use Closure;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * While DATABASE_MAINTENANCE is on, every database query throws and is shown as the maintenance page, so pages that
 * don't need the database keep working.
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
            return $next($request);
        }

        DB::connection()->beforeExecuting(fn () => throw new DatabaseMaintenanceException);

        $guard = Auth::guard('web');

        if ($guard instanceof SessionGuard) {
            $guard->setProvider(new MaintenanceUserProvider);
        }

        return $request->isMethodSafe() && ! $request->is(...self::BLOCKED_PATHS)
            ? $next($request)
            : (new DatabaseMaintenanceException)->render($request);
    }
}
