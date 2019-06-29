<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (empty($request->headers->get('authorization'))) {
            Log::debug(
                "Action attempted with no API key.",
                [
                    'route' => $request->getRequestUri(),
                    'type' => $request->getContentType(),
                    'data' => $request->getContent(),
                ]
            );

            return response()->json(
                [
                    'message' => 'You are not authorised to be here',
                ]
            )->setStatusCode(401);
        }

        // If they don't give us a valid key, tell them to go away and log it.
        if ($this->auth->guard($guard)->guest()) {
            Log::debug(
                "Action attempted with invalid API key.",
                [
                    'route' => $request->getRequestUri(),
                    'type' => $request->getContentType(),
                    'data' => $request->getContent(),
                ]
            );
            return response()->json(
                [
                    'message' => 'You are not authorised to be here',
                ]
            )->setStatusCode(403);
        }

        return $next($request);
    }
}
