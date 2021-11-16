<?php

namespace App\Http\Middleware;

class MiddlewareKeys
{
    const AUTH = 'auth.api';
    const ADMIN_WEB = 'auth.web_admin';
    const GITHUB_AUTH = 'auth.github';
    const ADMIN_LOG = 'admin.log';
    const GUEST = 'guest';
    const USER_BANNED = 'user.banned';
    const USER_DISABLED = 'user.disabled';
    const USER_LASTLOGIN = 'user.lastlogin';
    const SCOPES = 'scopes';
    const SCOPE = 'scope';
    const VATSIM_CID = 'vatsim.cid';
    const CONTROLLING_LIVE = 'network.controlling';

    private function __construct()
    {
        // Class for constants only
    }
}
