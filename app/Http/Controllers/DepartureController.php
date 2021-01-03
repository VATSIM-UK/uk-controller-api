<?php

namespace App\Http\Controllers;

use App\Rules\Airfield\AirfieldIcao;
use App\Services\DepartureService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartureController extends BaseController
{
    private DepartureService $departureService;

    public function __construct(DepartureService $departureService)
    {
        $this->departureService = $departureService;
    }

    public function getActiveDepartureRestrictions(): JsonResponse
    {
        return response()->json($this->departureService->getActiveRestrictions());
    }

    public function deleteRestriction(int $id): JsonResponse
    {
        $this->departureService->expireDepartureRestriction($id);
        return response()->json([], 204);
    }

    public function createRestriction(Request $request): JsonResponse
    {
        $badData = $this->checkForSuppliedData(
            $request,
            [
                'type' => [
                    'required',
                    Rule::in(['mdi', 'adi'])
                ],
                'interval' => 'required|integer|min:1',
                'expires_at' => 'required|date|after:now',
                'airfield' => [
                    'required',
                    new AirfieldIcao(),
                ],
                'sids' => 'required|array|filled',
                'sids.*' => 'string'
            ],
        );

        if ($badData) {
            return $badData;
        }


        $interval = call_user_func(
            [
                $this->departureService,
                $request->json('type') === 'mdi' ? 'createMinimumDepartureInterval' : 'createAverageDepartureInterval'
            ],
            $request->json('interval'),
            $request->json('airfield'),
            $request->json('sids'),
            Carbon::parse($request->json('expires_at'))
        );

        return response()->json($interval, 201);
    }

    public function updateRestriction(Request $request, int $id): JsonResponse
    {
        $badData = $this->checkForSuppliedData(
            $request,
            [
                'interval' => 'required|integer|min:1',
                'expires_at' => 'required|date|after:now',
                'airfield' => [
                    'required',
                    new AirfieldIcao(),
                ],
                'sids' => 'required|array|filled',
                'sids.*' => 'string',
            ]
        );

        if ($badData) {
            return $badData;
        }

        $interval = $this->departureService->updateDepartureRestriction(
            $id,
            $request->json('interval'),
            $request->json('airfield'),
            $request->json('sids'),
            Carbon::parse($request->json('expires_at'))
        );

        return response()->json($interval, 200);
    }

    public function getDepartureUkWakeIntervalsDependency(): JsonResponse
    {
        return response()->json($this->departureService->getDepartureUkWakeIntervalsDependency());
    }

    public function getDepartureRecatWakeIntervalsDependency(): JsonResponse
    {
        return response()->json($this->departureService->getDepartureRecatWakeIntervalsDependency());
    }

    public function getDepartureSidIntervalGroupsDependency(): JsonResponse
    {
        return response()->json($this->departureService->getDepartureIntervalGroupsDependency());
    }
}
