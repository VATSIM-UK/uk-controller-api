<?php

namespace App\Http\Controllers;

use App\Http\Requests\AircraftState\UpdateAircraftStates;
use App\Services\AircraftStateService;
use Illuminate\Http\JsonResponse;

class AircraftStateController
{
    private readonly AircraftStateService $service;

    public function __construct(AircraftStateService $service)
    {
        $this->service = $service;
    }

    public function getStates(): JsonResponse
    {
        return response()->json($this->service->allStates());
    }

    public function updateStates(UpdateAircraftStates $request): JsonResponse
    {
        $this->service->updateStates($request->validated()['updates']);

        return response()->json([], 200);
    }
}
