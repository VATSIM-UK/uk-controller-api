<?php

namespace App\Http\Controllers;

use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
use App\Models\Release\Departure\DepartureReleaseRequest;
use App\Rules\Controller\ControllerPositionValid;
use App\Services\DepartureReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                'expires_in_seconds' => 'required|integer|min:1',
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

    public function approveReleaseRequest(
        Request $request,
        int $id
    ): JsonResponse {
        $departureReleaseRequest = DepartureReleaseRequest::findOrFail($id);

        $validated = $request->validate(
            [
                'controller_position_id' => 'required|integer',
                'expires_in_seconds' => 'required|integer|min:1',
            ]
        );

        $responseData = null;
        try {
            $this->departureReleaseService->approveReleaseRequest(
                $departureReleaseRequest,
                $validated['controller_position_id'],
                Auth::id(),
                $validated['expires_in_seconds']
            );
            $responseCode = 200;
        } catch (DepartureReleaseDecisionNotAllowedException $decisionNotAllowedException) {
            Log::warning(
                sprintf(
                    'User %d attempted to approve release %d without permission',
                    Auth::id(),
                    $departureReleaseRequest->id
                )
            );
            $responseCode = 422;
            $responseData = ['message' => 'You cannot approve this release'];
        }
        return response()->json($responseData, $responseCode);
    }
}
