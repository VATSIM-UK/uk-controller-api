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
                    'frequency' => $position->frequency,
                    'top-down' => $position->topDownAirfields->pluck('code')->toArray()
                ],
            ];
        });

        return response()->json($positions);
    }
}
