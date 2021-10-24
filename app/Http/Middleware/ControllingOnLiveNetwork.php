<?php

namespace App\Http\Middleware;

use App\Models\Vatsim\NetworkControllerPosition;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ControllingOnLiveNetwork
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->userLoggedInAndControlling()) {
            return response()->json(
                ['message' => 'You must be controlling on the live network to perform this action']
            )->setStatusCode(403);
        }

        return $next($request);
    }

    private function userLoggedInAndControlling(): bool
    {
        return NetworkControllerPosition::where('cid', Auth::id())
            ->whereNotNull('controller_position_id')
            ->exists();
    }
}
