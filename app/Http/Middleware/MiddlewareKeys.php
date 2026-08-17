<?php

namespace App\Http\Middleware;

class MiddlewareKeys
{
    public const AUTH = 'auth.api';

    public const ADMIN_WEB = 'auth.web_admin';

    public const GITHUB_AUTH = 'auth.github';

    public const ADMIN_LOG = 'admin.log';

    public const GUEST = 'guest';

    public const USER_BANNED = 'user.banned';

    public const USER_DISABLED = 'user.disabled';

    public const USER_LASTLOGIN = 'user.lastlogin';

    public const SCOPES = 'scopes';

    public const SCOPE = 'scope';

    public const VATSIM_CID = 'vatsim.cid';

    public const CONTROLLING_LIVE = 'network.controlling';

    private function __construct()
    {
        // Class for constants only
    }
}
