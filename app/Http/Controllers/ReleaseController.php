<?php

namespace App\Http\Controllers;

use App\Events\EnrouteReleaseEvent;
use App\Models\Release\Enroute\EnrouteRelease;
use App\Models\Release\Enroute\EnrouteReleaseType;
use App\Rules\VatsimCallsign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//

class ReleaseController extends BaseController
{
    public function enrouteReleaseTypeDependency(): JsonResponse
    {
        return response()->json(EnrouteReleaseType::all());
    }

    public function enrouteRelease(Request $request): JsonResponse
    {
        // Check that we have a valid squawk type
        $invalidData = $this->checkForSuppliedData(
            $request,
            [
                'callsign' => ['required', 'string', new VatsimCallsign],
                'type' => 'required|integer',
                'initiating_controller' => 'required|string',
                'receiving_controller' => 'required|string',
                'release_point' => 'string',
            ]
        );

        if ($invalidData) {
            return $invalidData;
        }

        if (!EnrouteReleaseType::find($request->json('type'))) {
            return response()->json([], 404);
        }

        $release = EnrouteRelease::create(
            [
                'callsign' => $request->json('callsign'),
                'enroute_release_type_id' => $request->json('type'),
                'initiating_controller' => $request->json('initiating_controller'),
                'receiving_controller' => $request->json('receiving_controller'),
                'release_point' => $request->json('release_point'),
                'user_id' => Auth::user()->id,
            ]
        );

        event(new EnrouteReleaseEvent($release));
        return response()->json([], 201);
    }
}
