<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Vatsim\VatsimCidValidator;

/**
 * Middleware for checking that VATSIM CIDs are valid
 */
class VatsimCid
{
    // The failure message
    const FAILURE_MESSAGE = 'Invalid VATSIM Certificate ID provided';

    /**
     * Handles the request
     *
     * @param Request $request The request
     * @param Closure $next The next middleware to run
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!VatsimCidValidator::isValid($request->route('cid'))) {
            return response()->json(
                [
                    'message' => self::FAILURE_MESSAGE,
                ]
            )->setStatusCode(400);
        }

        return $next($request);
    }
}
