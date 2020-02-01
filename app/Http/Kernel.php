<?php

namespace App\Http;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\GithubAuth;
use App\Http\Middleware\LogAdminAction;
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
        'auth' => Authenticate::class,
        'auth.github' => GithubAuth::class,
        'admin.log' => LogAdminAction::class,
        'user.banned' => UserIsBanned::class,
        'user.disabled' => UserIsDisabled::class,
        'user.lastlogin' => UserLastLogin::class,
        'user.version' => UserPluginVersion::class,
        'scopes' => CheckScopes::class,
        'scope' => CheckForAnyScope::class,
        'vatsim.cid' => VatsimCid::class,
    ];

    protected $middlewareGroups = [
        'plugin.user' => [
            'auth',
            'user.banned',
            'user.disabled',
            'scopes:' . AuthServiceProvider::SCOPE_USER,
        ],
        'admin.user' => [
            'auth',
            'scopes:' . AuthServiceProvider::SCOPE_USER_ADMIN,
            'admin.log',
        ],
        'admin.version' => [
            'auth',
            'scopes:' . AuthServiceProvider::SCOPE_VERSION_ADMIN,
            'admin.log',
        ],
        'admin.dependency' => [
            'auth',
            'scopes:' . AuthServiceProvider::SCOPE_DEPENDENCY_ADMIN,
            'admin.log',
        ],
        'admin.github' => [
            'auth.github',
        ],
        'public' => [

        ],
    ];
}
