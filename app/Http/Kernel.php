<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'user.banned' => \App\Http\Middleware\UserIsBanned::class,
        'user.disabled' => \App\Http\Middleware\UserIsDisabled::class,
    ];

    protected $routeMiddleware = [
        'user.lastlogin' => \App\Http\Middleware\UserLastLogin::class,
        'user.version' => \App\Http\Middleware\UserPluginVersion::class,
        'scopes' => \Laravel\Passport\Http\Middleware\CheckScopes::class,
        'scope' => \Laravel\Passport\Http\Middleware\CheckForAnyScope::class,
        'vatsim.cid' => \App\Http\Middleware\VatsimCid::class,
    ];
}
