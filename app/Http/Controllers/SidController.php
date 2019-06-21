<?php

namespace App\Http\Controllers;

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

    public function getSid(int $id) : JsonResponse
    {
        $sid = $this->sidService->getSid($id);
        return response()->json($sid, $sid ? 200 : 404);
    }

    public function getAllSids() : JsonResponse
    {
        return response()->json($this->sidService->getAllSids());
    }

    public function deleteSid(int $id) : JsonResponse
    {
        $deleted = $this->sidService->deleteSid($id);
        return response()->json(null, $deleted ? 204 : 404);
    }

    public function createSid(Request $request) : JsonResponse
    {
        $expectedData = [
            'airfield_id' => 'integer',
            'identifier' => 'string',
            'initial_altitude' => 'integer',
        ];

        $this->checkForSuppliedData($request, $expectedData);
        $this->sidService->createSid(
            $request->json('airfield_id'),
            $request->json('identifier'),
            $request->json('initial_altitude')
        );

        return response()->json(null, 201);
    }
}
