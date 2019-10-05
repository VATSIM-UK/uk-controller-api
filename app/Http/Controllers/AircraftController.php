<?php

namespace App\Http\Controllers;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Http\JsonResponse;

class AircraftController extends BaseController
{
    public function getAllAircraft() : JsonResponse
    {
        return response()->json(Aircraft::all());
    }

    public function getAllWakeCategories()
    {
        return response()->json(WakeCategory::all());
    }
}
