<?php

namespace App\Http;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\UserIsBanned;
use App\Http\Middleware\UserIsDisabled;
use App\Http\Middleware\UserLastLogin;
use App\Http\Middleware\UserPluginVersion;
use App\Http\Middleware\VatsimCid;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;
use Laravel\Passport\Http\Middleware\CheckScopes;

class Kernel extends HttpKernel
{
    protected $middleware = [
        'auth' => Authenticate::class,
        'user.banned' => UserIsBanned::class,
        'user.disabled' => UserIsDisabled::class,
    ];

    protected $routeMiddleware = [
        'user.lastlogin' => UserLastLogin::class,
        'user.version' => UserPluginVersion::class,
        'scopes' => CheckScopes::class,
        'scope' => CheckForAnyScope::class,
        'vatsim.cid' => VatsimCid::class,
    ];
}
