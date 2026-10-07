<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Illuminate\Support\Str;

/**
 * Emails are stored lowercase, and Postgres compares strings case-sensitively,
 * so incoming email fields (including nested ones like accounts.0.email) are
 * normalized before validation and lookups.
 */
class LowercaseEmails extends TransformsRequest
{
    protected function transform($key, $value)
    {
        if (! \is_string($value) || ($key !== 'email' && ! \str_ends_with($key, '.email'))) {
            return $value;
        }

        return Str::lower(\trim($value));
    }
}
