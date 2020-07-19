<?php

namespace App\Http\Controllers;

use App\Events\GroundStatusAssignedEvent;
use App\Models\GroundStatus\GroundStatus;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroundStatusController extends BaseController
{
    public function getGroundStatusDependency(): JsonResponse
    {
        return response()->json(GroundStatus::all());
    }

    public function getAircraftGroundStatuses(): JsonResponse
    {
        $aircraftStatuses = NetworkAircraft::with('groundStatus')
            ->whereHas('groundStatus')
            ->get()
            ->mapWithKeys(function (NetworkAircraft $aircraft) {
                return [
                    $aircraft->callsign => $aircraft->groundStatus->id
                ];
            });

        return response()->json($aircraftStatuses);
    }

    public function setGroundStatus(Request $request, string $callsign): Response
    {
        $invalidRequest = $this->checkForSuppliedData(
            $request,
            [
                'ground_status_id' => 'integer|required',
            ]
        );

        if ($invalidRequest) {
            return $invalidRequest;
        }

        $groundStatus = GroundStatus::find($request->json('ground_status_id'));
        $networkAircraft = NetworkAircraft::find($callsign);
        $statusCode = 200;
        if (is_null($groundStatus)) {
            $statusCode = 404;
        } elseif (is_null($networkAircraft)) {
            $statusCode = 422;
        } else {
            $networkAircraft->groundStatus()->sync([$groundStatus->id]);
            event(new GroundStatusAssignedEvent($networkAircraft->callsign, $groundStatus->id));
            $statusCode = 201;
        }

        return response()->setStatusCode($statusCode);
    }
}
