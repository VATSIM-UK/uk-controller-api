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
     * Validates VATSIM CIDs
     *
     * @var VatsimCidValidator
     */
    private $cidValidator;

    /**
     * Constructor
     *
     * @param VatsimCidValidator $cidValidator
     */
    public function __construct(VatsimCidValidator $cidValidator)
    {
        $this->cidValidator = $cidValidator;
    }

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
        if (!$this->cidValidator->isValid($request->route('cid'))) {
            return response()->json(
                [
                    'message' => self::FAILURE_MESSAGE,
                ]
            )->setStatusCode(400);
        }

        return $next($request);
    }
}
