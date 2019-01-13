<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

trait UsesRouteParameters
{
    /**
     * Returns the given parameter from a route
     *
     * @param Request $request The request object
     * @param string $parameter The route object
     * @return string The parameter
     */
    public function getRouteParameter(Request $request, string $parameter) : string
    {
        return $request->route()[2][$parameter];
    }
}
