<?php

namespace App\Http\Middleware;

use Auth;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware that records when a user last logged in to the plugin
 * by hitting the root url.
 */
class UserLastLogin
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
        $user = Auth::user();

        // Record the login time if they've not logged in for an hour.
        if ($user->last_login < Carbon::now()->subHour()->toDateTimeString()) {
            $user->last_login = Carbon::now();
            $user->last_login_ip = $request->ip();
            $user->save();
        }
        
        return $next($request);
    }
}
