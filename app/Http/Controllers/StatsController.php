<?php

namespace App\Http\Controllers;

use App\Services\StatsService;

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

    public function get()
    {
        return response()->json($this->statsService->getStats());
    }
}
