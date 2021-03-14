<?php

namespace App\Http\Controllers\Admin;

use App\Models\Stand\Stand;
use Illuminate\Http\Request;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use Illuminate\Http\JsonResponse;
use App\Services\StandAdminService;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StandCreateRequest;

class StandAdminController extends BaseController
{
    private StandAdminService $service;

    public function __construct(StandAdminService $standAdminService)
    {
        $this->service = $standAdminService;
    }

    public function getAirfields(Request $request)
    {
        $shouldIncludeAirfieldsWithoutStands = $request->query('all');

        if ($shouldIncludeAirfieldsWithoutStands) {
            $airfields = Airfield::withCount('stands')->get();
        } else {
            $airfields = Airfield::has('stands')->withCount('stands')->get();
        }

        return response()->json(compact('airfields'));
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

    /**
     * Create a new stand from a validated request.
     *
     * @param Airfield $airfield
     * @param StandCreateRequest $request
     * @return JsonResponse
     */
    public function createNewStand(Airfield $airfield, StandCreateRequest $request): JsonResponse
    {
        $validatorsInUse = $airfield->stands->pluck('identifier');
        if ($validatorsInUse->contains($request->get('identifier'))) {
            return response()->json(['message' => 'Stand identifier in use for airfield.'], 400);
        }

        // form request will validate existence of terminal if specified.
        if ($terminal = Terminal::find($request->get('terminal_id'))) {
            if ($terminal->airfield_id != $airfield->id) { // NOSONAR (cant merge the if statements, despite what sonar says!)
                return response()->json(['message' => 'Invalid terminal for airfield.'], 400);
            } 
        }

        $stand = Stand::create([
            'identifier' => $request->get('identifier'),
            'airfield_id' => $airfield->id,
            'type_id' => $request->get('type_id'),
            'latitude' => $request->get('latitude'),
            'longitude' => $request->get('longitude'),
            'wake_category_id' => $request->get('wake_category_id'),
            'max_aircraft_id' => $request->get('max_aircraft_id'),
            'terminal_id' => $request->get('terminal_id'),
        ]);

        return response()->json(['stand_id' => $stand->id], 201);   
    }
}
