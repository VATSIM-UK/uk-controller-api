<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as PreventRequestForgeryMiddleware;

class PreventRequestForgery extends PreventRequestForgeryMiddleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'user/*',
        'token/*',
    ];
}
