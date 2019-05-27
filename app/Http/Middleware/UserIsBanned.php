<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Middleware to handle banned users.
 */
class UserIsBanned
{
    const FAILURE_MESSAGE = 'Your plugin account has been banned. ' .
        'If you believe this to be an error, please contact VATSIM UK support.';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * @return Response
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (auth()->user()->accountStatus->banned) {
            Log::info(
                'Banned user ' . $request->user()->id . " attempted access but was blocked.",
                [
                    'route' => $request->getRequestUri(),
                    'type' => $request->getContentType(),
                    'data' => $request->getContent(),
                ]
            );
            return response()->json(
                [
                    'message' => self::FAILURE_MESSAGE,
                ]
            )->setStatusCode(403);
        }

        return $next($request);
    }
}
