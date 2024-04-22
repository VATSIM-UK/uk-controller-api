<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Middleware to handle users whose accounts have been disabled.
 */
class UserIsDisabled
{
    const FAILURE_MESSAGE = 'Your plugin account has been disabled. Please contact VATSIM UK Support.';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->accountStatus->disabled) {
            Log::info(
                'Disabled user ' . $request->user()->id . " attempted access but was blocked.",
                [
                    'route' => $request->getRequestUri(),
                    'type' => $request->header('Content-Type'),
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
