<?php

namespace App\Http\Controllers;

use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Stand\Stand;
use App\Rules\VatsimCallsign;
use App\Services\NetworkDataService;
use App\Services\StandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StandController extends BaseController
{
    /**
     * @var StandService
     */
    private $standService;

    public function __construct(StandService $standService)
    {
        $this->standService = $standService;
    }

    public function getStandsDependency(): JsonResponse
    {
        return response()->json($this->standService->getStandsDependency());
    }

    public function getStandAssignments(): JsonResponse
    {
        return response()->json($this->standService->getStandAssignments());
    }

    public function createStandAssignment(Request $request): JsonResponse
    {
        $invalidRequest = $this->checkForSuppliedData(
            $request,
            [
                'callsign' => ['string' , 'required', new VatsimCallsign],
                'stand_id' => 'integer|required',
            ]
        );

        if ($invalidRequest) {
            return $invalidRequest;
        }

        $response = null;
        try {
            $this->standService->assignStandToAircraft(
                $request->json('callsign'),
                (int) $request->json('stand_id')
            );
            $response = response()->json([], 201);
        } catch (StandNotFoundException $notFoundException) {
            $response = response()->json([], 404);
        }

        return $response;
    }

    public function deleteStandAssignment(string $callsign): JsonResponse
    {
        $this->standService->deleteStandAssignment($callsign);
        return response()->json([], 204);
    }
}
