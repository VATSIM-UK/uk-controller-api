<?php

namespace App\Http\Controllers;

use App\Models\Controller\ControllerPosition;
use Illuminate\Http\JsonResponse;

class ControllerPositionController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function getAllControllers() : JsonResponse
    {
        return response()->json(ControllerPosition::all());
    }
}
