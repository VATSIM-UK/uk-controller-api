<?php

namespace App\Http\Controllers;

use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Airfield\Airfield;
use App\Rules\VatsimCallsign;
use App\Services\StandService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StandController extends BaseController
{
    const AIRFIELD_STAND_STATUS_CACHE_MINUTES = 5;

    /**
     * @var StandService
     */
    private $standService;

    public function __construct(StandService $standService)
    {
        $this->standService = $standService;
    }

    public function getStandsDependency(): JsonResponse
    {
        return response()->json($this->standService->getStandsDependency());
    }

    public function getStandAssignments(): JsonResponse
    {
        return response()->json($this->standService->getStandAssignments());
    }

    public function createStandAssignment(Request $request): JsonResponse
    {
        $invalidRequest = $this->checkForSuppliedData(
            $request,
            [
                'callsign' => ['string' , 'required', new VatsimCallsign],
                'stand_id' => 'integer|required',
            ]
        );

        if ($invalidRequest) {
            return $invalidRequest;
        }

        try {
            $this->standService->assignStandToAircraft(
                $request->json('callsign'),
                (int) $request->json('stand_id')
            );
            return response()->json([], 201);
        } catch (StandNotFoundException $notFoundException) {
            return response()->json([], 404);
        }
    }

    public function deleteStandAssignment(string $callsign): JsonResponse
    {
        $this->standService->deleteStandAssignmentByCallsign($callsign);
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
        if ($cachedResponse = Cache::get($this->getStandStatusCacheKey($airfield))) {
            return $cachedResponse;
        }

        $standStatuses = $this->standService->getAirfieldStandStatus($airfield->code);
        $response = [
            'stands' => $standStatuses,
            'generated_at' => Carbon::now()->toIso8601String(),
            'refresh_interval_minutes' => self::AIRFIELD_STAND_STATUS_CACHE_MINUTES,
            'refresh_at' => $this->getStandStatusRefreshTime()->toIso8601String(),
        ];
        Cache::put(
            $this->getStandStatusCacheKey($airfield),
            $response,
            $this->getStandStatusRefreshTime()
        );
        return $response;
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
        $stand = $this->standService->getAssignedStandForAircraft($aircraft);
        return $stand
            ? response()->json(
                ['id' => $stand->id, 'airfield' => $stand->airfield->code, 'identifier' => $stand['identifier']], 200
            )
            : response()->json([], 404);
    }
}
