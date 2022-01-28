<?php

namespace App\Http\Controllers;

use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Navigation\Navaid;
use App\Models\Vatsim\NetworkAircraft;
use App\Rules\VatsimCallsign;
use App\Services\HoldService;
use App\Services\NetworkAircraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HoldController extends BaseController
{
    /**
     * @var HoldService
     */
    private $holdService;

    /**
     * @param HoldService $holdService
     */
    public function __construct(HoldService $holdService)
    {
        $this->holdService = $holdService;
    }

    /**
     * Return the hold data as JSON
     *
     * @return JsonResponse
     */
    public function getAllHolds(): JsonResponse
    {
        return response()->json($this->holdService->getHolds())->setStatusCode(200);
    }

    public function getAssignedHolds(): JsonResponse
    {
        $holdData = AssignedHold::with('navaid')
            ->get()
            ->map(function (AssignedHold $assignedHold) {
                return [
                    'callsign' => $assignedHold->callsign,
                    'navaid' => $assignedHold->navaid->identifier,
                ];
            });

        return response()->json($holdData);
    }

    public function assignHold(Request $request): JsonResponse
    {
        $invalidRequest = $this->checkForSuppliedData(
            $request,
            [
                'callsign' => ['string', 'required', new VatsimCallsign],
                'navaid' => 'alpha|required',
            ]
        );

        if ($invalidRequest) {
            return $invalidRequest;
        }

        $navaid = Navaid::where('identifier', $request->json('navaid'))->first();
        if (is_null($navaid)) {
            return response()->json([], 422);
        }

        $callsign = $request->json('callsign');
        NetworkAircraftService::createPlaceholderAircraft($callsign);
        $assignedHold = AssignedHold::updateOrCreate(
            ['callsign' => $callsign],
            [
                'callsign' => $callsign,
                'navaid_id' => $navaid->id
            ]
        );

        event(new HoldAssignedEvent($assignedHold));
        return response()->json([], 201);
    }

    public function deleteAssignedHold(Request $request): JsonResponse
    {
        $hold = AssignedHold::where('callsign', $request->route('callsign'))->first();
        if (!is_null($hold)) {
            $hold->delete();
            event(new HoldUnassignedEvent($request->route('callsign')));
        }

        return response()->json([], 204);
    }

    public function getProximityHolds(): JsonResponse
    {
        return response()->json(
            NetworkAircraft::with('proximityNavaids')
                ->whereHas('proximityNavaids')
                ->get()
                ->map(
                    fn (NetworkAircraft $aircraft) => $aircraft->proximityNavaids->map(fn (Navaid $navaid) => [
                    'callsign' => $aircraft->callsign,
                    'navaid_id' => $navaid->id,
                    'entered_at' => $navaid->pivot->entered_at,
                ])
                )->flatten(1)
        );
    }
}
