<?php

namespace App\Http\Controllers;

use App\Services\DepartureIntervalService;
use Illuminate\Http\JsonResponse;

class DepartureController extends BaseController
{
    private DepartureIntervalService $departureIntervalService;

    public function __construct(DepartureIntervalService $departureIntervalService)
    {
        $this->departureIntervalService = $departureIntervalService;
    }

    public function getActiveDepartureIntervals(): JsonResponse
    {
        return response()->json($this->departureIntervalService->getActiveIntervals());
    }

    public function deleteInterval(int $id): JsonResponse
    {
        $this->departureIntervalService->expireDepartureInterval($id);
        return response()->json([], 204);
    }
}
