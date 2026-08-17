<?php

namespace App\Http\Controllers;

use App\Services\DepartureService;
use Illuminate\Http\JsonResponse;

class DepartureController extends BaseController
{
    private DepartureService $departureService;

    public function __construct(DepartureService $departureService)
    {
        $this->departureService = $departureService;
    }

    public function getDepartureSidIntervalGroupsDependency(): JsonResponse
    {
        return response()->json($this->departureService->getDepartureIntervalGroupsDependency());
    }
}
