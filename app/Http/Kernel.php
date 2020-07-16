<?php

namespace App\Http;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\GithubAuth;
use App\Http\Middleware\LogAdminAction;
use App\Http\Middleware\MiddlewareKeys;
use App\Http\Middleware\UpdateDependency;
use App\Http\Middleware\UserIsBanned;
use App\Http\Middleware\UserIsDisabled;
use App\Http\Middleware\UserLastLogin;
use App\Http\Middleware\UserPluginVersion;
use App\Http\Middleware\VatsimCid;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;
use Laravel\Passport\Http\Middleware\CheckScopes;

class Kernel extends HttpKernel
{
    protected $middleware = [];

    protected $routeMiddleware = [
        MiddlewareKeys::AUTH => Authenticate::class,
        MiddlewareKeys::GITHUB_AUTH => GithubAuth::class,
        MiddlewareKeys::ADMIN_LOG => LogAdminAction::class,
        MiddlewareKeys::UPDATE_DEPENDENCY => UpdateDependency::class,
        MiddlewareKeys::USER_BANNED => UserIsBanned::class,
        MiddlewareKeys::USER_DISABLED => UserIsDisabled::class,
        MiddlewareKeys::USER_LASTLOGIN => UserLastLogin::class,
        MiddlewareKeys::USER_PLUGIN_VERSION => UserPluginVersion::class,
        MiddlewareKeys::SCOPES => CheckScopes::class,
        MiddlewareKeys::SCOPE => CheckForAnyScope::class,
        MiddlewareKeys::VATSIM_CID => VatsimCid::class,
    ];

    protected $middlewareGroups = [
        'plugin.user' => [
            MiddlewareKeys::AUTH,
            MiddlewareKeys::USER_BANNED,
            MiddlewareKeys::USER_BANNED,
            MiddlewareKeys::SCOPES . ':' . AuthServiceProvider::SCOPE_USER,
        ],
        'admin.user' => [
            MiddlewareKeys::AUTH,
            MiddlewareKeys::SCOPES . ':' . AuthServiceProvider::SCOPE_USER_ADMIN,
            MiddlewareKeys::ADMIN_LOG,
        ],
        'admin.version' => [
            MiddlewareKeys::AUTH,
            MiddlewareKeys::SCOPES . ':' . AuthServiceProvider::SCOPE_VERSION_ADMIN,
            MiddlewareKeys::ADMIN_LOG,
        ],
        'admin.dependency' => [
            MiddlewareKeys::AUTH,
            MiddlewareKeys::SCOPES . ':' . AuthServiceProvider::SCOPE_DEPENDENCY_ADMIN,
            MiddlewareKeys::ADMIN_LOG,
        ],
        'admin.github' => [
            MiddlewareKeys::GITHUB_AUTH,
        ],
        'public' => [

        ],
    ];
}
