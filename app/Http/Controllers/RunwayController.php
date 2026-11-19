<?php

namespace App\Http\Controllers;

use App\Services\RunwayService;
use Illuminate\Http\JsonResponse;

class RunwayController extends BaseController
{
    private RunwayService $runwayService;

    public function __construct(RunwayService $runwayService)
    {
        $this->runwayService = $runwayService;
    }

    public function getRunwaysDependency(): JsonResponse
    {
        return response()->json($this->runwayService->getRunwaysDependency());
    }
}
