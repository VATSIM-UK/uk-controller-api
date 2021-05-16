<?php

namespace App\Http\Controllers;

use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
use App\Http\Requests\DepartureRelease\AcknowledgeDepartureRelease;
use App\Http\Requests\DepartureRelease\ApproveDepartureRelease;
use App\Http\Requests\DepartureRelease\RejectDepartureRelease;
use App\Http\Requests\DepartureRelease\RequestDepartureRelease;
use App\Models\Release\Departure\DepartureReleaseRequest;
use App\Services\DepartureReleaseService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepartureReleaseController
{
    private DepartureReleaseService $departureReleaseService;

    public function __construct(DepartureReleaseService $departureReleaseService)
    {
        $this->departureReleaseService = $departureReleaseService;
    }

    public function makeReleaseRequest(RequestDepartureRelease $request): JsonResponse
    {
        $releaseId = $this->departureReleaseService->makeReleaseRequest(
            $request->validated()['callsign'],
            Auth::id(),
            $request->validated()['requesting_controller_id'],
            $request->validated()['target_controller_id'],
            $request->validated()['expires_in_seconds']
        );

        return response()->json(['id' => $releaseId], 201);
    }

    public function approveReleaseRequest(
        ApproveDepartureRelease $request,
        DepartureReleaseRequest $departureReleaseRequest
    ): JsonResponse {
        $responseData = null;
        try {
            $this->departureReleaseService->approveReleaseRequest(
                $departureReleaseRequest,
                $request->validated()['controller_position_id'],
                Auth::id(),
                $request->validated()['expires_in_seconds'],
                $request->validated()['released_at'] === null
                    ? CarbonImmutable::now()
                    : CarbonImmutable::parse($request->validated()['released_at'])
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
            $responseCode = 403;
            $responseData = ['message' => 'You cannot approve this release'];
        }
        return response()->json($responseData, $responseCode);
    }

    public function rejectReleaseRequest(
        RejectDepartureRelease $request,
        DepartureReleaseRequest $departureReleaseRequest
    ): JsonResponse {
        $responseData = null;
        try {
            $this->departureReleaseService->rejectReleaseRequest(
                $departureReleaseRequest,
                $request->validated()['controller_position_id'],
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
            $responseCode = 403;
            $responseData = ['message' => 'You cannot reject this release'];
        }
        return response()->json($responseData, $responseCode);
    }

    public function acknowledgeReleaseRequest(
        AcknowledgeDepartureRelease $request,
        DepartureReleaseRequest $departureReleaseRequest
    ): JsonResponse {
        $responseData = null;
        try {
            $this->departureReleaseService->acknowledgeReleaseRequest(
                $departureReleaseRequest,
                $request->validated()['controller_position_id'],
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
            $responseCode = 403;
            $responseData = ['message' => 'You cannot reject this release'];
        }
        return response()->json($responseData, $responseCode);
    }

    public function cancelReleaseRequest(
        DepartureReleaseRequest $departureReleaseRequest
    ): JsonResponse {
        $responseData = null;
        try {
            $this->departureReleaseService->cancelReleaseRequest(
                $departureReleaseRequest,
                Auth::id()
            );
            $responseCode = 200;
        } catch (DepartureReleaseDecisionNotAllowedException $decisionNotAllowedException) {
            Log::warning(
                sprintf(
                    'User %d attempted to cancel release %d without permission',
                    Auth::id(),
                    $departureReleaseRequest->id
                )
            );
            $responseCode = 403;
            $responseData = ['message' => 'You cannot cancel this release'];
        }
        return response()->json($responseData, $responseCode);
    }
}
