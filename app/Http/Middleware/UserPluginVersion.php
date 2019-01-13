<?php

namespace App\Http\Middleware;

use App\Models\Version\Version;
use Auth;
use Closure;
use Illuminate\Http\Request;

/**
 * A class to track the latest version of the plugin that the user
 * is using. Used so we can keep track of uptake on latest updates
 * etc.
 */
class UserPluginVersion
{
    use UsesRouteParameters;

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
        $version = Version::where('version', '=', $this->getRouteParameter($request, 'version'))->first();

        if ($version !== null) {
            Auth::user()->setLastVersion($version->id);
        }

        return $next($request);
    }
}
