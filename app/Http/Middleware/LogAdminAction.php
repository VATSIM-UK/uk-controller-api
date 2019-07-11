<?php

namespace App\Http\Middleware;

use App\Models\User\AdminLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware that records any actions that an admin user performs.
 */
class LogAdminAction
{
    /**
     * Handles the request
     *
     * @param Request $request The request
     * @param Closure $next The next middleware to run
     * @param null $guard
     * @return Response
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        AdminLog::create(
            [
                'user_id' => Auth::user()->id,
                'request_uri' => $request->getRequestUri(),
                'request_body' => $request->getContent(),
            ]
        );

        return $next($request);
    }
}
