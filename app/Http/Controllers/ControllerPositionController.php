<?php

namespace App\Http\Controllers;

use App\Models\Controller\ControllerPosition;
use Illuminate\Http\JsonResponse;

class ControllerPositionController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function getAllControllers() : JsonResponse
    {
        return response()->json(ControllerPosition::all());
    }

    public function getControllerPositionsDependency() : JsonResponse
    {
        $positions = ControllerPosition::all()->mapWithKeys(function (ControllerPosition $position) {
            return [
                $position->callsign => [
                    'id' => $position->id,
                    'frequency' => $position->frequency,
                    'top-down' => $position->topDownAirfields->pluck('code')->toArray(),
                    'requests_departure_releases' => $position->requests_departure_releases,
                    'receives_departure_releases' => $position->receives_departure_releases,
                ],
            ];
        });

        return response()->json($positions);
    }
}
