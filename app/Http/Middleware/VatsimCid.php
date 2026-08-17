<?php

namespace App\Http\Middleware;

use App\Helpers\Vatsim\VatsimCidValidator;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware for checking that VATSIM CIDs are valid
 */
class VatsimCid
{
    // The failure message
    public const FAILURE_MESSAGE = 'Invalid VATSIM Certificate ID provided';

    /**
     * Handles the request
     *
     * @param  Request  $request  The request
     * @param  Closure  $next  The next middleware to run
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (! VatsimCidValidator::isValid($request->route('cid'))) {
            return response()->json(
                [
                    'message' => self::FAILURE_MESSAGE,
                ]
            )->setStatusCode(400);
        }

        return $next($request);
    }
}
