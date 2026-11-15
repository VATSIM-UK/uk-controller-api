<?php

namespace App\Http\Controllers;

use App\Models\Controller\ControllerPosition;
use App\Services\ControllerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ControllerPositionController extends BaseController
{
    private ControllerService $controllerService;

    public function __construct(ControllerService $controllerService)
    {
        $this->controllerService = $controllerService;
    }

    public function getAllControllers(): JsonResponse
    {
        return response()->json(ControllerPosition::all());
    }

    public function getControllerPositionsDependency(): JsonResponse
    {
        return response()->json($this->controllerService->getControllerPositionsDependency());
    }

    public function getTopDownForPosition(string $callsign): JsonResponse
    {
        try {
            return response()->json(
                $this->controllerService->getTopDownPositionsForPosition(strtoupper($callsign))
            );
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Controller position not found'])->setStatusCode(404);
        }
    }
}
