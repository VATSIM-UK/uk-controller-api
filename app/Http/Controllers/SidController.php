<?php

namespace App\Http\Controllers;

use App\Services\SidService;
use Illuminate\Http\JsonResponse;

class SidController extends BaseController
{
    /**
     * @var SidService
     */
    private $sidService;

    /**
     * SidController constructor.
     * @param SidService $sidService
     */
    public function __construct(SidService $sidService)
    {
        $this->sidService = $sidService;
    }

    public function getInitialAltitudeDependency() : JsonResponse
    {
        return response()->json($this->sidService->getInitialAltitudeDependency());
    }
}
