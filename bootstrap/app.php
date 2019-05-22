<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    Dotenv\DotEnv::create(__DIR__.'/../')->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->withFacades();

$app->withEloquent();


// Laravel config

$app->configure('auth');
$app->configure('filesystems');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->bind(
    \App\Services\ManifestService::class,
    function ($app) {
        return new \App\Services\ManifestService($app);
    }
);

$app->bind(
    \App\Services\VersionService::class,
    function ($app) {
        return new \App\Services\VersionService($app);
    }
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//    App\Http\Middleware\ExampleMiddleware::class
// ]);

$app->middleware(
    [
        'auth' => App\Http\Middleware\Authenticate::class,
        'user.banned' => App\Http\Middleware\UserIsBanned::class,
        'user.disabled' => App\Http\Middleware\UserIsDisabled::class,
    ]
);

$app->routeMiddleware(
    [
        'user.lastlogin' => App\Http\Middleware\UserLastLogin::class,
        'user.version' => App\Http\Middleware\UserPluginVersion::class,
        'scopes' => \Laravel\Passport\Http\Middleware\CheckScopes::class,
        'scope' => \Laravel\Passport\Http\Middleware\CheckForAnyScope::class,
        'vatsim.cid' => App\Http\Middleware\VatsimCid::class,
    ]
);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
$app->register(Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\BroadcastServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->bind(\App\Providers\SquawkService::class, function ($app) {
    return new \App\Providers\SquawkService();
});
$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\App\Providers\RegionalPressureServiceProvider::class);
$app->register(\App\Providers\MinStackCalculationServiceProvider::class);
$app->register(\Illuminate\Redis\RedisServiceProvider::class);
if (class_exists('Vluzrmos\Tinker\TinkerServiceProvider')) {
    $app->register(Vluzrmos\Tinker\TinkerServiceProvider::class);
}
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group(
    [
    'namespace' => 'App\Http\Controllers',
    ],
    function ($router) {
        include __DIR__.'/../routes/routes.php';
    }
);

return $app;
