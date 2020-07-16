<?php

namespace App\Http\Middleware;

class MiddlewareKeys
{
    const AUTH = 'auth';
    const GITHUB_AUTH = 'auth.github';
    const ADMIN_LOG = 'admin.log';
    const UPDATE_DEPENDENCY = 'dependency.update';
    const USER_BANNED = 'user.banned';
    const USER_DISABLED = 'user.disabled';
    const USER_LASTLOGIN = 'user.lastlogin';
    const USER_PLUGIN_VERSION = 'user.version';
    const SCOPES = 'scopes';
    const SCOPE = 'scope';
    const VATSIM_CID = 'vatsim.cid';

    private function __construct()
    {
    }
}
