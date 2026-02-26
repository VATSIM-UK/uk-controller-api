<?php

namespace App\Http\Controllers;

use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Models\Stand\StandReservationPlan;
use App\Models\Vatsim\NetworkAircraft;
use App\Models\User\RoleKeys;
use App\Rules\Airfield\AirfieldIcao;
use App\Rules\Coordinates\Latitude;
use App\Rules\Coordinates\Longitude;
use App\Rules\VatsimCallsign;
use App\Services\JsonSchema\StandReservationPlanSchemaValidator;
use App\Services\AirlineService;
use App\Services\NetworkAircraftService;
use App\Services\Stand\AirfieldStandService;
use App\Services\Stand\ArrivalAllocationService;
use App\Services\Stand\DepartureAllocationService;
use App\Services\Stand\StandAssignmentsService;
use App\Services\Stand\StandStatusService;
use App\Imports\Stand\StandReservationsImport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

    public function uploadStandReservationPlan(
        Request $request,
        StandReservationPlanSchemaValidator $schemaValidator
    ): JsonResponse {
        if (!$request->user()->hasRole(RoleKeys::VAA)) {
            return response()->json(['message' => 'Insufficient permissions'], 403);
        }

        $validated = Validator::make(
            $request->json()->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'contact_email' => ['required', 'email'],
                'reservations' => ['nullable', 'array', 'min:1'],
                'stand_slots' => ['nullable', 'array', 'min:1'],
                'start' => ['nullable', 'date'],
                'end' => ['nullable', 'date', 'after:start'],
                'event_start' => ['nullable', 'date'],
                'event_finish' => ['nullable', 'date', 'after:event_start'],
            ]
        )->validate();

        $schemaErrors = $schemaValidator->validateApiRequest($request->json()->all());
        if ($schemaErrors !== []) {
            return response()->json([
                'message' => 'Stand reservation plan request does not match schema',
                'errors' => $schemaErrors,
            ], 422);
        }

        $plan = StandReservationPlan::create([
            'name' => $validated['name'],
            'contact_email' => $validated['contact_email'],
            'payload' => [
                'start' => $validated['start'] ?? null,
                'end' => $validated['end'] ?? null,
                'event_start' => $validated['event_start'] ?? null,
                'event_finish' => $validated['event_finish'] ?? null,
                'reservations' => $validated['reservations'] ?? null,
                'stand_slots' => $validated['stand_slots'] ?? null,
            ],
            'approval_due_at' => Carbon::now()->addDays(7),
            'submitted_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'plan_id' => $plan->id,
            'status' => $plan->status,
            'approval_due_at' => $plan->approval_due_at,
        ], 201);
    }

    public function getPendingStandReservationPlans(): JsonResponse
    {
        return response()->json(
            StandReservationPlan::pending()->orderBy('created_at')->get()->map(function (StandReservationPlan $plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'contact_email' => $plan->contact_email,
                    'approval_due_at' => $plan->approval_due_at,
                    'status' => $plan->status,
                    'created_at' => $plan->created_at,
                ];
            })
        );
    }

    public function approveStandReservationPlan(
        StandReservationPlan $standReservationPlan,
        StandReservationsImport $importer,
        Request $request
    ): JsonResponse {
        if ($standReservationPlan->status !== 'pending') {
            return response()->json(['message' => 'Plan is not pending'], 409);
        }

        if ($standReservationPlan->approval_due_at->isPast()) {
            $standReservationPlan->update(['status' => 'expired']);
            return response()->json(['message' => 'Approval window has expired'], 422);
        }

        $payload = $standReservationPlan->payload;
        $createdReservations = $importer->importReservations($this->rowsFromPayload($payload));

        $standReservationPlan->update([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'approved_by' => $request->user()->id,
            'imported_reservations' => $createdReservations,
        ]);

        return response()->json([
            'status' => $standReservationPlan->status,
            'created' => $createdReservations,
        ]);
    }

    private function rowsFromPayload(array $payload): Collection
    {
        $defaultStart = $payload['event_start'] ?? $payload['start'] ?? null;
        $defaultEnd = $payload['event_finish'] ?? $payload['end'] ?? null;

        $reservationRows = collect($payload['reservations'] ?? [])
            ->filter(fn (mixed $reservation): bool => is_array($reservation))
            ->map(
                fn (array $reservation): Collection => $this->buildReservationRow($reservation, $defaultStart, $defaultEnd)
            );

        $slotRows = collect($payload['stand_slots'] ?? [])
            ->filter(fn (mixed $standSlot): bool => is_array($standSlot))
            ->flatMap(function (array $standSlot) use ($defaultStart, $defaultEnd) {
                $slotAirfield = $standSlot['airfield'] ?? $standSlot['airport'] ?? null;
                $slotStand = $standSlot['stand'] ?? null;

                return collect($standSlot['slot_reservations'] ?? [])
                    ->filter(fn (mixed $slotReservation): bool => is_array($slotReservation))
                    ->map(
                        fn (array $slotReservation): Collection =>
                            $this->buildReservationRow($slotReservation, $defaultStart, $defaultEnd, $slotAirfield, $slotStand)
                    );
            });

        return $reservationRows->concat($slotRows)->values();
    }

    private function buildReservationRow(
        array $reservation,
        ?string $defaultStart,
        ?string $defaultEnd,
        ?string $fallbackAirfield = null,
        ?string $fallbackStand = null
    ): Collection {
        return collect([
            'airfield' => $reservation['airfield'] ?? $reservation['airport'] ?? $fallbackAirfield,
            'stand' => $reservation['stand'] ?? $fallbackStand,
            'callsign' => $reservation['callsign'] ?? null,
            'cid' => $reservation['cid'] ?? null,
            'origin' => $reservation['origin'] ?? null,
            'destination' => $reservation['destination'] ?? null,
            'start' => $reservation['start'] ?? $defaultStart,
            'end' => $reservation['end'] ?? $defaultEnd,
        ]);
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
        $responseStatus = 201;
        $responsePayload = null;

        if ($validated['assignment_type'] === 'arrival') {
            $aircraftTypeId = Aircraft::where('code', $validated['aircraft_type'])->first()?->id;
            if (!$aircraftTypeId) {
                $responseStatus = 422;
                $responsePayload = ['message' => 'Invalid aircraft type'];
            }
        }

        if ($responseStatus !== 201) {
            return response()->json($responsePayload ?? [], $responseStatus);
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

        // Reservation-based event slots must take precedence over distance-based auto allocation.
        $reservation = StandReservation::query()
            ->active()
            ->where(function ($query) use ($aircraft) {
                $query->where('callsign', $aircraft->callsign);

                if ($aircraft->cid !== null) {
                    $query->orWhere('cid', $aircraft->cid);
                }
            })
            ->orderBy('start')
            ->get()
            ->first(function (StandReservation $reservation) use ($aircraft): bool {
                if ($reservation->origin !== null && $reservation->origin !== $aircraft->planned_depairport) {
                    return false;
                }

                if ($reservation->destination !== null && $reservation->destination !== $aircraft->planned_destairport) {
                    return false;
                }

                return true;
            });

        $reservedStandId = $reservation?->stand_id;

        if ($reservedStandId !== null) {
            // Assign exactly what was requested for this active slot window.
            $this->assignmentsService->createStandAssignment($aircraft->callsign, $reservedStandId, 'Reservation');
            $responsePayload = ['stand_id' => $reservedStandId];
        } else {
            // If there is no active reservation, fall back to normal automatic stand assignment logic.
            $stand = $validated['assignment_type'] === 'departure'
                ? $this->departureAllocationService->assignStandToDepartingAircraft($aircraft)
                : $this->arrivalAllocationService->autoAllocateArrivalStandForAircraft($aircraft);

            if ($stand === null) {
                $responseStatus = 404;
                $responsePayload = ['message' => 'No stand available'];
            } else {
                $responsePayload = ['stand_id' => $stand];
            }
        }

        return response()->json($responsePayload ?? [], $responseStatus);
    }


}
