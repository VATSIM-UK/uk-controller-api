<?php

namespace App\Http\Controllers;

use App\Rules\Airfield\AirfieldIcao;
use App\Services\DepartureIntervalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartureController extends BaseController
{
    private DepartureIntervalService $departureIntervalService;

    public function __construct(DepartureIntervalService $departureIntervalService)
    {
        $this->departureIntervalService = $departureIntervalService;
    }

    public function getActiveDepartureIntervals(): JsonResponse
    {
        return response()->json($this->departureIntervalService->getActiveIntervals());
    }

    public function deleteInterval(int $id): JsonResponse
    {
        $this->departureIntervalService->expireDepartureInterval($id);
        return response()->json([], 204);
    }

    public function createInterval(Request $request): JsonResponse
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
            ]
        );

        if ($badData) {
            return $badData;
        }


        $interval = call_user_func(
            [
                $this->departureIntervalService,
                $request->json('type') === 'mdi' ? 'createMinimumDepartureInterval' : 'createAverageDepartureInterval'
            ],
            $request->json('interval'),
            $request->json('airfield'),
            $request->json('sids'),
            Carbon::parse($request->json('expires_at'))
        );

        return response()->json($interval, 201);
    }
}
