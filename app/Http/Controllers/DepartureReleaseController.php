<?php

namespace App\Http\Controllers;

use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
use App\Models\Controller\ControllerPosition;
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
                    function ($attribute, $value, $fail) {
                        if (!ControllerPosition::where('id', $value)->canRequestDepartureReleases()->exists()) {
                            $fail(sprintf('Controller position %d cannot request departure releases', $value));
                        }
                    },
                    'not_in:target_controller_ids.*',
                ],
                'target_controller_ids' => 'required|array',
                'target_controller_ids.*' => [
                    'integer',
                    function ($attribute, $value, $fail) {
                        if (!ControllerPosition::where('id', $value)->canReceiveDepartureReleases()->exists()) {
                            $fail(sprintf('Controller position %d cannot receive departure releases', $value));
                        }
                    },
                    'different:requesting_controller_id',
                ],
                'expires_in_seconds' => 'required|integer|min:1',
            ]
        );

        $releaseId = $this->departureReleaseService->makeReleaseRequest(
            $validated['callsign'],
            Auth::id(),
            $validated['requesting_controller_id'],
            $validated['target_controller_ids'],
            $validated['expires_in_seconds']
        );

        return response()->json(['id' => $releaseId], 201);
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

    public function rejectReleaseRequest(
        Request $request,
        int $id
    ): JsonResponse {
        $departureReleaseRequest = DepartureReleaseRequest::findOrFail($id);

        $validated = $request->validate(
            [
                'controller_position_id' => 'required|integer',
            ]
        );

        $responseData = null;
        try {
            $this->departureReleaseService->rejectReleaseRequest(
                $departureReleaseRequest,
                $validated['controller_position_id'],
                Auth::id()
            );
            $responseCode = 200;
        } catch (DepartureReleaseDecisionNotAllowedException $decisionNotAllowedException) {
            Log::warning(
                sprintf(
                    'User %d attempted to reject release %d without permission',
                    Auth::id(),
                    $departureReleaseRequest->id
                )
            );
            $responseCode = 422;
            $responseData = ['message' => 'You cannot reject this release'];
        }
        return response()->json($responseData, $responseCode);
    }

    public function acknowledgeReleaseRequest(
        Request $request,
        int $id
    ): JsonResponse {
        $departureReleaseRequest = DepartureReleaseRequest::findOrFail($id);

        $validated = $request->validate(
            [
                'controller_position_id' => 'required|integer',
            ]
        );

        $responseData = null;
        try {
            $this->departureReleaseService->acknowledgeReleaseRequest(
                $departureReleaseRequest,
                $validated['controller_position_id'],
                Auth::id()
            );
            $responseCode = 200;
        } catch (DepartureReleaseDecisionNotAllowedException $decisionNotAllowedException) {
            Log::warning(
                sprintf(
                    'User %d attempted to reject release %d without permission',
                    Auth::id(),
                    $departureReleaseRequest->id
                )
            );
            $responseCode = 422;
            $responseData = ['message' => 'You cannot reject this release'];
        }
        return response()->json($responseData, $responseCode);
    }
}
