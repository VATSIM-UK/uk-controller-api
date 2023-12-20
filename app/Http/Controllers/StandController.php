<?php

namespace App\Http\Controllers;

use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Rules\Airfield\AirfieldIcao;
use App\Rules\Coordinates\Latitude;
use App\Rules\Coordinates\Longitude;
use App\Rules\VatsimCallsign;
use App\Services\AirlineService;
use App\Services\NetworkAircraftService;
use App\Services\Stand\AirfieldStandService;
use App\Services\Stand\ArrivalAllocationService;
use App\Services\Stand\DepartureAllocationService;
use App\Services\Stand\StandAssignmentsService;
use App\Services\Stand\StandStatusService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class StandController extends BaseController
{
    const AIRFIELD_STAND_STATUS_CACHE_MINUTES = 5;
    private readonly StandAssignmentsService $assignmentsService;
    private readonly AirfieldStandService $airfieldStandService;
    private readonly ArrivalAllocationService $arrivalAllocationService;
    private readonly DepartureAllocationService $departureAllocationService;
    private readonly AirlineService $airlineService;

    public function __construct(
        StandAssignmentsService $assignmentsService,
        AirfieldStandService $airfieldStandService,
        ArrivalAllocationService $arrivalAllocationService,
        DepartureAllocationService $departureAllocationService,
        AirlineService $airlineService
    ) {
        $this->assignmentsService = $assignmentsService;
        $this->airfieldStandService = $airfieldStandService;
        $this->arrivalAllocationService = $arrivalAllocationService;
        $this->departureAllocationService = $departureAllocationService;
        $this->airlineService = $airlineService;
    }

    public function getStandsDependency(): JsonResponse
    {
        return response()->json(
            $this->airfieldStandService->getAllStandsByAirfield()
                ->mapWithKeys(
                    function (Airfield $airfield) {
                        return [
                            $airfield->code => $airfield->stands
                                ->reject(fn (Stand $stand) => $stand->closed_at !== null)
                                ->values()
                                ->map(fn (Stand $stand) => [
                                    'id' => $stand->id,
                                    'identifier' => $stand->identifier,
                                ]),
                        ];
                    }
                )
        );
    }

    public function getStandAssignments(): JsonResponse
    {
        return response()->json(
            StandAssignment::all()->map(
                function (StandAssignment $assignment) {
                    return [
                        'callsign' => $assignment->callsign,
                        'stand_id' => $assignment->stand_id,
                    ];
                }
            )
        );
    }

    public function createStandAssignment(Request $request): JsonResponse
    {
        $invalidRequest = $this->checkForSuppliedData(
            $request,
            [
                'callsign' => ['string', 'required', new VatsimCallsign],
                'stand_id' => 'integer|required',
            ]
        );

        if ($invalidRequest) {
            return $invalidRequest;
        }

        try {
            $this->assignmentsService->createStandAssignment(
                $request->json('callsign'),
                (int) $request->json('stand_id'),
                'User'
            );
            return response()->json([], 201);
        } catch (StandNotFoundException) {
            return response()->json([], 404);
        }
    }

    public function deleteStandAssignment(string $callsign): JsonResponse
    {
        $this->assignmentsService->deleteAssignmentIfExists(NetworkAircraft::find($callsign));
        return response()->json([], 204);
    }

    public function getAirfieldStandStatus(Request $request): JsonResponse
    {
        if (!$airfield = Airfield::where('code', $request->query('airfield'))->first()) {
            return response()->json([], 404);
        }

        return response()->json($this->getAirfieldStandStatusData($airfield));
    }

    private function getAirfieldStandStatusData(Airfield $airfield): array
    {
        $cacheRefreshTime = $this->getStandStatusRefreshTime();
        return Cache::remember(
            $this->getStandStatusCacheKey($airfield),
            $cacheRefreshTime,
            function () use ($airfield, $cacheRefreshTime) {
                $standStatuses = StandStatusService::getAirfieldStandStatus($airfield->code);
                return [
                    'stands' => $standStatuses,
                    'generated_at' => Carbon::now()->toIso8601String(),
                    'refresh_interval_minutes' => self::AIRFIELD_STAND_STATUS_CACHE_MINUTES,
                    'refresh_at' => $cacheRefreshTime->toIso8601String(),
                ];
            }
        );
    }

    private function getStandStatusCacheKey(Airfield $airfield): string
    {
        return sprintf('STAND_STATUS_%s', $airfield->code);
    }

    private function getStandStatusRefreshTime(): Carbon
    {
        return Carbon::now()->addMinutes(self::AIRFIELD_STAND_STATUS_CACHE_MINUTES);
    }

    public function getStandAssignmentForAircraft(string $aircraft): JsonResponse
    {
        $assignment = $this->assignmentsService->assignmentForCallsign($aircraft);
        return $assignment
            ? response()->json(
                [
                    'id' => $assignment->stand_id,
                    'airfield' => $assignment->stand->airfield->code,
                    'identifier' => $assignment->stand->identifier,
                ],
            )
            : response()->json([], 404);
    }

    public function requestAutomaticStandAssignment(Request $request): JsonResponse
    {
        $validated = Validator::make(
            $request->json()->all(),
            [
                'callsign' => ['string', 'required', new VatsimCallsign],
                'assignment_type' => ['string', 'required', 'in:departure,arrival'],
                'departure_airfield' => ['string', 'required', new AirfieldIcao],
                'arrival_airfield' => ['string', 'required_if:assignment_type,arrival', new AirfieldIcao],
                'aircraft_type' => ['string', 'required_if:assignment_type,arrival'],
                'latitude' => ['numeric', 'required_if:assignment_type,departure', new Latitude],
                'longitude' => ['numeric', 'required_if:assignment_type,departure', new Longitude],
            ]
        )->validate();

        $aircraftTypeId = null;
        if ($validated['assignment_type'] === 'arrival') {
            $aircraftTypeId = Aircraft::where('code', $validated['aircraft_type'])->first()?->id;
            if (!$aircraftTypeId) {
                return response()->json(['message' => 'Invalid aircraft type'], 422);
            }
        }

        // Grab aircraft from the network, if it doesn't exist, create a placeholder.
        $aircraft = NetworkAircraft::find($validated['callsign']);
        if (!$aircraft) {
            NetworkAircraftService::createPlaceholderAircraft($validated['callsign']);
            $aircraft = new NetworkAircraft(['callsign' => $validated['callsign']]);
        }

        // Fill with data provided by the user - will always be the latest data we have.
        $aircraft->fill(
            [
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'planned_depairport' => $validated['departure_airfield'],
                'planned_destairport' => $validated['arrival_airfield'] ?? null,
                'planned_aircraft_short' => $validated['aircraft_type'] ?? null,
                'aircraft_id' => $aircraftTypeId,
                'airline_id' => $this->airlineService->airlineIdForCallsign($validated['callsign']),
            ]
        );

        $stand = $validated['assignment_type'] === 'departure'
            ? $this->departureAllocationService->assignStandToDepartingAircraft($aircraft)
            : $this->arrivalAllocationService->autoAllocateArrivalStandForAircraft($aircraft);

        if ($stand === null) {
            return response()->json(['message' => 'No stand available'], 404);
        }

        return response()->json(['stand_id' => $stand], 201);
    }
}
