<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\Carbon;

/**
 * Provides the authentication serivce and configures scopes.
 */
class AuthServiceProvider extends ServiceProvider
{
    protected $defer = true;

    // Scopes
    const SCOPE_USER = 'user';
    const SCOPE_USER_ADMIN = 'user-admin';
    const SCOPE_VERSION_ADMIN = 'version-admin';

    const AUTH_SCOPES = [
        self::SCOPE_USER => 'Can perform plugin user functions',
        self::SCOPE_USER_ADMIN => 'Can perform user administration functions',
        self::SCOPE_VERSION_ADMIN => 'Can perform plugin version administration functions',
    ];

    /**
     * Sets up the token scopes
     */
    public function boot()
    {
        Passport::tokensExpireIn(Carbon::now()->addYear());
        Passport::tokensCan(self::AUTH_SCOPES);
    }
}
