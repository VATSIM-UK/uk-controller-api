<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\Http\JsonResponse;

class StatsController extends BaseController
{
    /**
     * @var StatsService
     */
    private $statsService;

    /**
     * StatsController constructor
     */
    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Get the stats
     *
     * @return JsonResponse
     */
    public function get() : JsonResponse
    {
        return response()->json($this->statsService->getStats());
    }
}
