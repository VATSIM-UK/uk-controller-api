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

    public function getWakeCategoriesDependency()
    {
        $categories = [];
        Aircraft::all()->each(function (Aircraft $aircraft) use (&$categories) {
            $categories[$aircraft->code] = $aircraft->wakeCategory->code;
        });

        return response()->json($categories);
    }

    public function getRecatCategoriesDependency()
    {
        return response()->json(tap(Aircraft::with('recatCategory')->whereHas('recatCategory')->get()->mapWithKeys(
            function (Aircraft $aircraft) {
                return [$aircraft->code => $aircraft->recatCategory->code];
            }
        ));
    }
}
