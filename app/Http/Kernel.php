<?php

namespace App\Http;

use App\Http\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Http\Middleware\HandleCors;
use Bugsnag\BugsnagLaravel\OomBootstrapper;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\ControllingOnLiveNetwork;
use App\Http\Middleware\GithubAuth;
use App\Http\Middleware\LogAdminAction;
use App\Http\Middleware\MiddlewareKeys;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\UserIsBanned;
use App\Http\Middleware\UserIsDisabled;
use App\Http\Middleware\UserLastLogin;
use App\Http\Middleware\VatsimCid;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;
use Laravel\Passport\Http\Middleware\CheckScopes;

class Kernel extends HttpKernel
{
    protected $middleware = [];

    protected $middlewareAliases = [
        MiddlewareKeys::AUTH => Authenticate::class,
        MiddlewareKeys::ADMIN_WEB => Authenticate::class,
        MiddlewareKeys::GITHUB_AUTH => GithubAuth::class,
        MiddlewareKeys::GUEST => RedirectIfAuthenticated::class,
        MiddlewareKeys::ADMIN_LOG => LogAdminAction::class,
        MiddlewareKeys::USER_BANNED => UserIsBanned::class,
        MiddlewareKeys::USER_DISABLED => UserIsDisabled::class,
        MiddlewareKeys::USER_LASTLOGIN => UserLastLogin::class,
        MiddlewareKeys::SCOPES => CheckScopes::class,
        MiddlewareKeys::SCOPE => CheckForAnyScope::class,
        MiddlewareKeys::VATSIM_CID => VatsimCid::class,
        MiddlewareKeys::CONTROLLING_LIVE => ControllingOnLiveNetwork::class,
    ];

    protected $middlewareGroups = [
        'plugin.user' => [
            MiddlewareKeys::AUTH . ':api',
            MiddlewareKeys::USER_BANNED,
            MiddlewareKeys::USER_DISABLED,
            MiddlewareKeys::SCOPES . ':' . AuthServiceProvider::SCOPE_USER,
        ],
        'admin.user' => [
            MiddlewareKeys::AUTH . ':api',
            MiddlewareKeys::SCOPES . ':' . AuthServiceProvider::SCOPE_USER_ADMIN,
            MiddlewareKeys::ADMIN_LOG,
        ],
        'admin.github' => [
            MiddlewareKeys::GITHUB_AUTH,
        ],
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],
        'api' => [
            SubstituteBindings::class,
        ],
        'web_auth' => [
            MiddlewareKeys::AUTH . ':web',
        ],
        'public' => [
            HandleCors::class,
        ],
    ];

    protected function bootstrappers()
    {
        return array_merge(
            [OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
