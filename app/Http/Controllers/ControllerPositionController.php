<?php

namespace App\Http\Controllers;

use App\Models\Controller\ControllerPosition;
use App\Services\ControllerService;
use Illuminate\Http\JsonResponse;

class ControllerPositionController extends BaseController
{
    private ControllerService $controllerService;

    public function __construct(ControllerService $controllerService)
    {
        $this->controllerService = $controllerService;
    }

    /**
     * @return JsonResponse
     */
    public function getAllControllers() : JsonResponse
    {
        return response()->json(ControllerPosition::all());
    }

    public function getLegacyControllerPositionsDependency() : JsonResponse
    {
        return response()->json($this->controllerService->getLegacyControllerPositionsDependency());
    }

    public function getControllerPositionsDependency() : JsonResponse
    {
        return response()->json($this->controllerService->getControllerPositionsDependency());
    }
}
