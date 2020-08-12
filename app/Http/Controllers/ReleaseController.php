<?php

namespace App\Http\Controllers;

use App\Events\EnrouteReleaseEvent;
use App\Models\Release\Enroute\EnrouteRelease;
use App\Models\Release\Enroute\EnrouteReleaseType;
use App\Rules\VatsimCallsign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReleaseController extends BaseController
{
    public function enrouteReleaseTypeDependency(): JsonResponse
    {
        return response()->json(EnrouteReleaseType::all());
    }

    public function enrouteRelease(Request $request): JsonResponse
    {
        // Check that we have valid data and release type
        $invalidData = $this->checkForSuppliedData(
            $request,
            [
                'callsign' => ['required', 'string', new VatsimCallsign],
                'type' => 'required|integer',
                'initiating_controller' => 'required|string',
                'target_controller' => 'required|string',
                'release_point' => 'string|min:0|max:15',
            ]
        );

        if ($invalidData) {
            return $invalidData;
        }

        if (!EnrouteReleaseType::find($request->json('type'))) {
            return response()->json([], 404);
        }

        // Record the release and broadcast the event
        $release = EnrouteRelease::create(
            [
                'callsign' => $request->json('callsign'),
                'enroute_release_type_id' => $request->json('type'),
                'initiating_controller' => $request->json('initiating_controller'),
                'target_controller' => $request->json('target_controller'),
                'release_point' => $request->json('release_point'),
                'user_id' => Auth::user()->id,
            ]
        );

        event(new EnrouteReleaseEvent($release));
        return response()->json([], 201);
    }
}
