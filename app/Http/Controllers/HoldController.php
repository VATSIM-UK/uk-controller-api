<?php

namespace App\Http\Controllers;

use App\Services\HoldService;
use Illuminate\Http\JsonResponse;

class HoldController extends BaseController
{
    /**
     * @var HoldService
     */
    private $holdService;

    /**
     * @param HoldService $holdService
     */
    public function __construct(HoldService $holdService)
    {
        $this->holdService = $holdService;
    }

    /**
     * Return the hold data as JSON
     *
     * @return JsonResponse
     */
    public function getAllHolds() : JsonResponse
    {
        return response()->json($this->holdService->getHolds())->setStatusCode(200);
    }

    /**
     * Get all the generic (non-user-specific) hold profiles
     *
     * @return JsonResponse
     */
    public function getGenericHoldProfiles() : JsonResponse
    {
        return response()->json($this->holdService->getGenericHoldProfiles())->setStatusCode(200);
    }

    /**
     * Get all the hold profiles pertaining to a user
     *
     * @return JsonResponse
     */
    public function getUserHoldProfiles() : JsonResponse
    {
        return response()->json($this->holdService->getUserHoldProfiles())->setStatusCode(200);
    }
}
