<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

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

    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        dd('HIT AUTH SERVICE PROVIDER');
        $this->registerPolicies();
        Passport::tokensExpireIn(Carbon::now()->addYear());
        Passport::tokensCan(self::AUTH_SCOPES);
    }
}
