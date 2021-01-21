<?php

namespace App\Http\Controllers;

use App\Services\WakeService;
use Illuminate\Http\JsonResponse;

class WakeController extends BaseController
{
    private WakeService $wakeService;

    public function __construct(WakeService $wakeService)
    {
        $this->wakeService = $wakeService;
    }

    public function getWakeSchemesDependency(): JsonResponse
    {
        return response()->json($this->wakeService->getWakeSchemesDependency());
    }
}
