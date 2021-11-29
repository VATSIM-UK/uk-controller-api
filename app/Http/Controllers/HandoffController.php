<?php

namespace App\Http\Controllers;

use App\Models\Controller\Handoff;
use App\Services\HandoffService;
use Illuminate\Http\JsonResponse;

class HandoffController extends BaseController
{
    /**
     * @var HandoffService
     */
    private $handoffService;

    public function __construct(HandoffService $handoffService)
    {
        $this->handoffService = $handoffService;
    }

    /**
     * @deprecated
     */
    public function getAllHandoffs(): JsonResponse
    {
        return response()->json($this->handoffService->getAllHandoffsWithControllers());
    }

    public function getHandoffsV2Dependency(): JsonResponse
    {
        return response()->json($this->handoffService->getHandoffsV2Dependency());
    }
}
