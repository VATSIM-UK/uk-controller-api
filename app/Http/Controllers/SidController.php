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

    public function getInitialAltitudeDependency(): JsonResponse
    {
        return response()->json($this->sidService->getInitialAltitudeDependency());
    }

    /**
     * @deprecated
     */
    public function getSidHandoffsDependency(): JsonResponse
    {
        return response()->json($this->handoffService->mapSidsToHandoffs());
    }

    public function getSid(int $id): JsonResponse
    {
        $sid = $this->sidService->getSid($id);
        return response()->json($sid, $sid ? 200 : 404);
    }

    public function getAllSids(): JsonResponse
    {
        return response()->json($this->sidService->getAllSids());
    }

    public function deleteSid(int $id): JsonResponse
    {
        $deleted = $this->sidService->deleteSid($id);
        return response()->json(null, $deleted ? 204 : 404);
    }

    public function createSid(Request $request): JsonResponse
    {
        $expectedData = [
            'airfield_id' => 'integer|required',
            'identifier' => 'string|required',
            'initial_altitude' => 'integer|required',
        ];

        $badDataResponse = $this->checkForSuppliedData($request, $expectedData);
        if ($badDataResponse) {
            return $badDataResponse;
        }

        $this->sidService->createSid(
            $request->json('airfield_id'),
            $request->json('identifier'),
            $request->json('initial_altitude')
        );

        return response()->json(null, 201);
    }

    public function updateSid(int $id, Request $request): JsonResponse
    {
        $expectedData = [
            'airfield_id' => 'integer|required',
            'identifier' => 'string|required',
            'initial_altitude' => 'integer|required',
        ];

        $badDataResponse = $this->checkForSuppliedData($request, $expectedData);
        if ($badDataResponse) {
            return $badDataResponse;
        }

        $this->sidService->updateSid(
            $id,
            $request->json('airfield_id'),
            $request->json('identifier'),
            $request->json('initial_altitude')
        );

        return response()->json(null, 200);
    }

    public function getSidsDependency(): JsonResponse
    {
        return response()->json($this->sidService->getSidsDependency());
    }
}
