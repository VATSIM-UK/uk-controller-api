<?php

namespace App\Http\Controllers;

use App\Services\AirfieldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AirfieldController extends BaseController
{
    private AirfieldService $airfieldService;

    public function __construct(AirfieldService $airfieldService)
    {
        $this->airfieldService = $airfieldService;
    }

    public function getAllAirfields(Request $request) : JsonResponse
    {
        return response()->json($this->airfieldService->getAllAirfieldsWithRelations());
    }

    public function getAirfieldDependency(): JsonResponse
    {
        return response()->json($this->airfieldService->getAirfieldsDependency());
    }
}
