<?php

namespace App\Providers;

use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Models\Controller\Prenote;
use App\Models\Hold\Hold;
use App\Models\Navigation\Navaid;
use App\Models\Notification\Notification;
use App\Models\Runway\Runway;
use App\Models\Sid;
use App\Models\Squawk\SquawkAssignment;
use App\Models\Srd\SrdNote;
use App\Models\Srd\SrdRoute;
use App\Models\Stand\Stand;
use App\Models\User\User;
use App\Models\Version\Version;
use App\Policies\ActivityLogPolicy;
use App\Policies\DefaultFilamentPolicy;
use App\Policies\PluginEditableDataPolicy;
use App\Policies\PluginVersionPolicy;
use App\Policies\ReadOnlyPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Spatie\Activitylog\Models\Activity;

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
        self::SCOPE_DATA_ADMIN => 'Can administer live data stored in the system',
    ];

    protected $policies = [
        // The defaults
        Airfield::class => DefaultFilamentPolicy::class,
        Airline::class => DefaultFilamentPolicy::class,
        ControllerPosition::class => DefaultFilamentPolicy::class,
        Handoff::class => DefaultFilamentPolicy::class,
        Hold::class => DefaultFilamentPolicy::class,
        Navaid::class => DefaultFilamentPolicy::class,
        Notification::class => DefaultFilamentPolicy::class,
        Prenote::class => DefaultFilamentPolicy::class,
        Runway::class => DefaultFilamentPolicy::class,
        Sid::class => DefaultFilamentPolicy::class,
        Stand::class => DefaultFilamentPolicy::class,

        // Things the plugin can assign
        SquawkAssignment::class => PluginEditableDataPolicy::class,

        // Things that can only be updated by external processes
        SrdNote::class => ReadOnlyPolicy::class,
        SrdRoute::class => ReadOnlyPolicy::class,

        // Special policies
        Activity::class => ActivityLogPolicy::class,
        User::class => UserPolicy::class,
        Version::class => PluginVersionPolicy::class,
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
