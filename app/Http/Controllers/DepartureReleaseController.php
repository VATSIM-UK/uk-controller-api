<?php

namespace App\Http\Controllers;

use App\Models\Controller\ControllerPosition;
use App\Rules\Controller\ControllerPositionValid;
use App\Services\DepartureReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartureReleaseController
{
    private DepartureReleaseService $departureReleaseService;

    public function __construct(DepartureReleaseService $departureReleaseService)
    {
        $this->departureReleaseService = $departureReleaseService;
    }

    public function makeReleaseRequest(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'callsign' => 'required|string',
                'requesting_controller_id' => [
                    'required',
                    'integer',
                    new ControllerPositionValid(),
                ],
                'target_controller_ids' => 'required|array',
                'target_controller_ids.*' => [
                    'integer',
                    new ControllerPositionValid(),
                ],
                'expires_in_seconds' => 'required|integer',
            ]
        );

        $this->departureReleaseService->makeReleaseRequest(
            $validated['callsign'],
            Auth::id(),
            $validated['requesting_controller_id'],
            $validated['target_controller_ids'],
            $validated['expires_in_seconds']
        );

        return response()->json(null, 201);
    }
}
