<?php

namespace App\Http\Controllers;

use App\Events\HoldAssignedEvent;
use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Navigation\Navaid;
use App\Models\Vatsim\NetworkAircraft;
use App\Rules\VatsimCallsign;
use App\Services\HoldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
    public function getAllHolds() : JsonResponse
    {
        return response()->json($this->holdService->getHolds())->setStatusCode(200);
    }

    /**
     * Get all the hold profiles pertaining to a user
     *
     * @return JsonResponse
     */
    public function getUserHoldProfiles() : JsonResponse
    {
        return response()->json($this->holdService->getUserHoldProfiles())->setStatusCode(200);
    }

    /**
     * Delete the given user hold profile
     *
     * @param int $holdProfileId Profile to delete
     * @return Response
     */
    public function deleteUserHoldProfile(int $holdProfileId) : Response
    {
        $this->holdService->deleteUserHoldProfile($holdProfileId);
        return response('', 204);
    }

    /**
     * Creates the given user hold profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createUserHoldProfile(Request $request) : JsonResponse
    {
        $invalidRequest = $this->checkForSuppliedData(
            $request,
            [
                'name' => 'string|required',
                'holds' => 'array|required',
            ]
        );

        if ($invalidRequest) {
            return $invalidRequest;
        }

        $holdsValid = array_reduce(
            $request->json('holds'),
            function ($carry, $hold) {
                return $carry && is_integer($hold);
            },
            true
        );

        if (!$holdsValid) {
            Log::debug('Invalid holds submitted');
            return response()->json(null)->setStatusCode(400);
        }

        $createdProfile = $this->holdService->createUserHoldProfile($request->json('name'), $request->json('holds'));
        return response()->json(['id' => $createdProfile->id])->setStatusCode(201);
    }

    /**
     * Creates the given user hold profile
     *
     * @param Request $request
     * @param int $profileId
     * @return JsonResponse
     */
    public function updateUserHoldProfile(Request $request, int $profileId) : JsonResponse
    {
        $invalidRequest = $this->checkForSuppliedData(
            $request,
            [
                'name' => 'string|required',
                'holds' => 'array|required',
            ]
        );

        if ($invalidRequest) {
            return $invalidRequest;
        }

        $holdsValid = array_reduce(
            $request->json('holds'),
            function ($carry, $hold) {
                return $carry && is_integer($hold);
            },
            true
        );

        if (!$holdsValid) {
            Log::debug('Invalid holds submitted');
            return response()->json(null)->setStatusCode(400);
        }

        $this->holdService->updateUserHoldProfile($profileId, $request->json('name'), $request->json('holds'));
        return response()->json(null)->setStatusCode(204);
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
                'callsign' => ['string' , 'required', new VatsimCallsign],
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

        $networkAircraft = NetworkAircraft::firstOrCreate(['callsign' => $request->json('callsign')]);
        $assignedHold = AssignedHold::updateOrCreate(
            ['callsign' => $request->json('callsign')],
            [
                'callsign' => $networkAircraft->callsign,
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
}
