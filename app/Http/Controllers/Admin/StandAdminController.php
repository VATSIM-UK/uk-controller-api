<?php

namespace App\Http\Controllers\Admin;

use App\Models\Stand\Stand;
use Illuminate\Http\Request;
use App\Models\Airfield\Airfield;
use Illuminate\Http\JsonResponse;
use App\Services\StandAdminService;
use App\Http\Controllers\BaseController;

class StandAdminController extends BaseController
{
    private StandAdminService $service;

    public function __construct(StandAdminService $standAdminService)
    {
        $this->service = $standAdminService;
    }

    /**
     * Get a list of the registered stand types.
     *
     * @return JsonResponse
     */
    public function getTypes(): JsonResponse
    {
        return response()->json(['types' => $this->service::standTypes()]);
    }


    /**
     * Get list of stands for an airfield.
     *
     * @param Airfield $airfield
     * @return JsonResponse
     */
    public function getStandsForAirfield(Airfield $airfield) : JsonResponse
    {
        return response()->json(['stands' => $this->service->getStandsByAirfield($airfield)]);
    }

    /**
     * Get details of a given stand which exists for an airfield.
     *
     * @param Airfield $airfield
     * @param Stand $stand
     * @return JsonResponse
     */
    public function getStandDetails(Airfield $airfield, Stand $stand): JsonResponse
    {
        if ($stand->airfield_id != $airfield->id) {
            return response()->json(['message' => 'Stand not part of airfield.'], 404);
        }

        $stand->load(['terminal', 'wakeCategory', 'type', 'airlines']);

        return response()->json(['stand' => $stand]);
    }
}
