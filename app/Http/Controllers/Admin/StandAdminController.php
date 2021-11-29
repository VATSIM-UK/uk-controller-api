<?php

namespace App\Http\Controllers\Admin;

use App\Models\Stand\Stand;
use Illuminate\Http\Request;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use Illuminate\Http\JsonResponse;
use App\Services\StandAdminService;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StandRequest;

class StandAdminController extends BaseController
{
    private StandAdminService $service;

    private const STAND_NOT_IN_AIRFIELD_ERROR = ['message' => 'Stand not part of airfield.'];
    private const INVALID_TERMINAL_ERROR = ['message' => 'Invalid terminal for airfield.'];
    private const INVALID_IDENTIFIER_ERROR = ['message' => 'Stand identifier in use for airfield.'];

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
            return response()->json(self::STAND_NOT_IN_AIRFIELD_ERROR, 404);
        }

        $stand->load(['terminal', 'wakeCategory', 'type', 'airlines']);

        return response()->json(['stand' => $stand]);
    }

    /**
     * Create a new stand from a validated request.
     *
     * @param Airfield $airfield
     * @param StandRequest $request
     * @return JsonResponse
     */
    public function createNewStand(Airfield $airfield, StandRequest $request): JsonResponse
    {
        $validatorsInUse = $airfield->stands->pluck('identifier');
        if ($validatorsInUse->contains($request->get('identifier'))) {
            return response()->json(self::INVALID_IDENTIFIER_ERROR, 409);
        }

        // form request will validate existence of terminal if specified.
        if (!$this->checkForTerminalValidity($request->get('terminal_id', null), $airfield->id)) {
            return response()->json(self::INVALID_TERMINAL_ERROR, 400);
        }

        $stand = Stand::create($this->formatObjectForStandFromRequest($request, $airfield->id));

        return response()->json(['stand_id' => $stand->id], 201);
    }

    /**
     * Modify a stand which is contained within a given airfield.
     *
     * @param Airfield $airfield
     * @param Stand $stand
     * @param StandRequest $request
     * @return JsonResponse
     */
    public function modifyStand(Airfield $airfield, Stand $stand, StandRequest $request) : JsonResponse //NOSONAR
    {
        if ($stand->airfield_id != $airfield->id) {
            return response()->json(self::STAND_NOT_IN_AIRFIELD_ERROR, 404);
        }

        $validatorsInUse = $airfield->stands->pluck('identifier');
        // we don't need to check the identifier if it is the same.
        $identifierUnchanged = $stand->identifier == $request->get('identifier');
        if ($validatorsInUse->contains($request->get('identifier')) && !$identifierUnchanged) {
            return response()->json(self::INVALID_IDENTIFIER_ERROR, 409);
        }

        if (!$this->checkForTerminalValidity($request->get('terminal_id', null), $airfield->id)) {
            return response()->json(self::INVALID_TERMINAL_ERROR, 400);
        }

        $stand->update($this->formatObjectForStandFromRequest($request, $airfield->id));

        return response()->json([], 204);
    }

    /**
     * Delete a stand which is contained within a given airfield.
     *
     * @param Airfield $airfield
     * @param Stand $stand
     * @return JsonResponse
     */
    public function deleteStand(Airfield $airfield, Stand $stand) : JsonResponse
    {
        if ($stand->airfield_id != $airfield->id) {
            return response()->json(self::STAND_NOT_IN_AIRFIELD_ERROR, 404);
        }

        $stand->delete();

        return response()->json([], 204);
    }

    /**
     * Close a stand which is contained within a given airfield.
     *
     * @param Airfield $airfield
     * @param Stand $stand
     * @return JsonResponse
     */
    public function closeStand(Airfield $airfield, Stand $stand) : JsonResponse
    {
        if ($stand->airfield_id != $airfield->id) {
            return response()->json(self::STAND_NOT_IN_AIRFIELD_ERROR, 404);
        }

        $stand->close();

        return response()->json([], 204);
    }

    /**
     * Open a stand which is contained within a given airfield.
     *
     * @param Airfield $airfield
     * @param Stand $stand
     * @return JsonResponse
     */
    public function openStand(Airfield $airfield, Stand $stand) : JsonResponse
    {
        if ($stand->airfield_id != $airfield->id) {
            return response()->json(self::STAND_NOT_IN_AIRFIELD_ERROR, 404);
        }

        $stand->open();

        return response()->json([], 204);
    }

    /**
     * Get a list of terminals for an Airfield if configured.
     *
     * @param Airfield $airfield
     * @return JsonResponse
     */
    public function getTerminals(Airfield $airfield) : JsonResponse
    {
        $terminals = $airfield->load('terminals')->terminals;

        if ($terminals->count() < 1) {
            return response()->json(['message' => 'Airfield does not have terminals configured.'], 404);
        }

        return response()->json(['terminals' => $terminals]);
    }

    /**
     * Get stands by an existing terminal.
     *
     * @param Airfield $airfield
     * @param Terminal $terminal
     * @return JsonResponse
     */
    public function getStandsByTerminal(Airfield $airfield, Terminal $terminal) : JsonResponse
    {
        $stands = $terminal->load('stands')->stands;
        
        return response()->json(['stands' => $stands->load(['type', 'wakeCategory'])]);
    }

    /**
     * Produce an object ideally used when doing mass assignment from
     * the validated request.
     *
     * @param StandRequest $request
     * @param integer $airfield_id
     * @return array
     */
    private function formatObjectForStandFromRequest(StandRequest $request, int $airfield_id) : array
    {
        $standDefaultAssignmentPriority = 100;

        return [
            'identifier' => $request->get('identifier'),
            'airfield_id' => $airfield_id,
            'type_id' => $request->get('type_id'),
            'latitude' => $request->get('latitude'),
            'longitude' => $request->get('longitude'),
            'wake_category_id' => $request->get('wake_category_id'),
            'max_aircraft_id' => $request->get('max_aircraft_id'),
            'terminal_id' => $request->get('terminal_id'),
            'assignment_priority' => $request->get('assignment_priority', $standDefaultAssignmentPriority)
        ];
    }
    
    /**
     * Check that a terminal is attached to a given airfield.
     *
     * @param integer|null $terminal_id
     * @param integer $airfield_id
     * @return boolean
     */
    private function checkForTerminalValidity(?int $terminal_id, int $airfield_id): bool
    {
        if ($terminal = Terminal::find($terminal_id)) {
            if ($terminal->airfield_id != $airfield_id) { // NOSONAR (cant merge the if statements, despite what sonar says!)
                return false;
            }
        }
        return true;
    }
}
