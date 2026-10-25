<?php

namespace App\Auth;

use App\Exceptions\DatabaseMaintenanceException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Used during database maintenance. Logged-in members are treated as guests, so the public pages work for them
 * without loading the user from the database. Their session cookie is left as it is, so they are logged in again
 * once maintenance ends. Logging in shows the maintenance page.
 */
class MaintenanceUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return null;
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token) {}

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials)
    {
        throw new DatabaseMaintenanceException;
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials)
    {
        return false;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false) {}
}
