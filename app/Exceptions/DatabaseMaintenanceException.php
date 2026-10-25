<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown for any database query while DATABASE_MAINTENANCE is on. Rendered as the maintenance page, also when thrown
 * while rendering a view (ViewException delegates to render() and report()).
 */
class DatabaseMaintenanceException extends RuntimeException
{
    public const RETRY_AFTER_SECONDS = 600;

    public function __construct()
    {
        parent::__construct('The database is unavailable during maintenance.');
    }

    public function render(Request $request): Response
    {
        $headers = ['Retry-After' => self::RETRY_AFTER_SECONDS];

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Vi flyttar till en ny server. Försök igen om en stund.',
            ], 503, $headers);
        }

        return response()->view('maintenance', [], 503, $headers);
    }

    /**
     * Expected during maintenance, so it is neither logged nor sent to Sentry.
     */
    public function report(): void {}
}
