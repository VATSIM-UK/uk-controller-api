<?php

namespace App\Http\Controllers;

use App\Services\HoldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    /**
     * Delete the given user hold profile
     *
     * @param int $holdProfileId Profile to delete
     * @return Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function deleteUserHoldProfile(int $holdProfileId)
    {
        $this->holdService->deleteUserHoldProfile($holdProfileId);
        return response('', 204);
    }
}
