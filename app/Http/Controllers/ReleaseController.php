<?php

namespace App\Http\Controllers;

use App\Models\Release\Enroute\EnrouteReleaseType;
use Illuminate\Http\JsonResponse;

class ReleaseController extends BaseController
{
    public function enrouteReleaseTypeDependency(): JsonResponse
    {
        return response()->json(EnrouteReleaseType::all());
    }
}
