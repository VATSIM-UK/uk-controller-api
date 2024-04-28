<?php

namespace App\Http\Controllers;

use App\Services\HandoffService;
use App\Services\SidService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SidController extends BaseController
{
    /**
     * @var SidService
     */
    private $sidService;
    /**
     * @var HandoffService
     */
    private $handoffService;

    /**
     * SidController constructor.
     * @param SidService $sidService
     */
    public function __construct(SidService $sidService, HandoffService $handoffService)
    {
        $this->sidService = $sidService;
        $this->handoffService = $handoffService;
    }

    public function getSidsDependency(): JsonResponse
    {
        return response()->json($this->sidService->getSidsDependency());
    }
}
