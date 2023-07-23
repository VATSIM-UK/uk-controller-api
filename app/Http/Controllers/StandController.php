<?php

namespace App\Http\Controllers;

use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Rules\VatsimCallsign;
use App\Services\Stand\AirfieldStandService;
use App\Services\Stand\StandAssignmentsService;
use App\Services\Stand\StandStatusService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StandController extends BaseController
{
    const AIRFIELD_STAND_STATUS_CACHE_MINUTES = 5;
    private readonly StandAssignmentsService $assignmentsService;
    private readonly AirfieldStandService $airfieldStandService;

    public function __construct(
        StandAssignmentsService $assignmentsService,
        AirfieldStandService $airfieldStandService
    ) {
        $this->assignmentsService = $assignmentsService;
        $this->airfieldStandService = $airfieldStandService;
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
                (int)$request->json('stand_id')
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
}
