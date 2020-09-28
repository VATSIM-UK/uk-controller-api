<?php

namespace App\Http\Controllers;

use App\Exceptions\Stand\StandNotFoundException;
use App\Rules\VatsimCallsign;
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

        try {
            $this->standService->assignStandToAircraft(
                $request->json('callsign'),
                (int) $request->json('stand_id')
            );
            return response()->json([], 201);
        } catch (StandNotFoundException $notFoundException) {
            return response()->json([], 404);
        }
    }

    public function deleteStandAssignment(string $callsign): JsonResponse
    {
        $this->standService->deleteStandAssignment($callsign);
        return response()->json([], 204);
    }
}
