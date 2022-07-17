<?php

namespace App\Providers;

use App\Models\Stand\Stand;
use App\Policies\DefaultFilamentPolicy;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    // Scopes
    const SCOPE_USER = 'user';
    const SCOPE_USER_ADMIN = 'user-admin';
    const SCOPE_VERSION_ADMIN = 'version-admin';
    const SCOPE_DEPENDENCY_ADMIN = 'dependency-admin';
    const SCOPE_DATA_ADMIN = 'data-admin';

    const AUTH_SCOPES = [
        self::SCOPE_USER => 'Can perform plugin user functions',
        self::SCOPE_USER_ADMIN => 'Can perform user administration functions',
        self::SCOPE_VERSION_ADMIN => 'Can perform plugin version administration functions',
        self::SCOPE_DEPENDENCY_ADMIN => 'Can perform dependency administration functions',
        self::SCOPE_DATA_ADMIN => 'Can administer live data stored in the system'
    ];

    const ADMIN_SCOPES = [
        self::SCOPE_USER_ADMIN,
        self::SCOPE_DEPENDENCY_ADMIN,
        self::SCOPE_VERSION_ADMIN,
        self::SCOPE_DATA_ADMIN
    ];

    protected $policies = [
        Stand::class => DefaultFilamentPolicy::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::personalAccessTokensExpireIn(Carbon::now()->addDecade());
        Passport::tokensCan(self::AUTH_SCOPES);
    }
}
