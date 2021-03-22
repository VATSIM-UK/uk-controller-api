<?php

namespace App\Http\Controllers;

use App\Rules\Airfield\AirfieldIcao;
use App\Services\DepartureService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
