<?php

namespace App\Http\Controllers;

use App\Models\Airfield\Airfield;
use App\Services\AirfieldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function React\Promise\reject;

class AirfieldController extends BaseController
{
    /**
     * @var AirfieldService
     */
    private $airfieldService;

    /**
     * AirfieldController constructor.
     * @param AirfieldService $airfieldService
     */
    public function __construct(AirfieldService $airfieldService)
    {
        $this->airfieldService = $airfieldService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAirfields(Request $request) : JsonResponse
    {
        return response()->json($this->airfieldService->getAllAirfieldsWithRelations());
    }

    public function getAirfieldOwnershipDependency() : JsonResponse
    {
        $ownership = [];
        Airfield::all()->each(function (Airfield $airfield) use (&$ownership) {
            $ownership[$airfield->code] = $airfield->controllers->pluck('callsign')->toArray();
        });

        return response()->json($ownership);
    }
}
