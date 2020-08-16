<?php

namespace App\Http\Controllers;

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
}
