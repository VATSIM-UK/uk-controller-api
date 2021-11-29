<?php

namespace App\Http\Controllers;

use App\Models\Flightplan\FlightRules;
use Illuminate\Http\JsonResponse;

class FlightRulesController extends BaseController
{
    public function getFlightRulesDependency(): JsonResponse
    {
        $flightRules = FlightRules::all()->each(function (FlightRules $flightRules) {
            $flightRules->makeHidden(['created_at', 'updated_at']);
        });
        return response()->json($flightRules);
    }
}
