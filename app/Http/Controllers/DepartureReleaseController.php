<?php

namespace App\Http\Controllers;

use App\Exceptions\Release\Departure\DepartureReleaseAlreadyDecidedException;
use App\Exceptions\Release\Departure\DepartureReleaseDecisionNotAllowedException;
use App\Http\Requests\Release\Departure\AcknowledgeDepartureRelease;
use App\Http\Requests\Release\Departure\ApproveDepartureRelease;
use App\Http\Requests\Release\Departure\RejectDepartureRelease;
use App\Http\Requests\Release\Departure\RequestDepartureRelease;
use App\Models\Release\Departure\DepartureReleaseRequest;
use App\Services\DepartureReleaseService;
use Carbon\CarbonImmutable;
use Closure;
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
        return $this->performReleaseAction(
            $departureReleaseRequest->id,
            'approve',
            function () use ($request, $departureReleaseRequest) {
                $this->departureReleaseService->approveReleaseRequest(
                    $departureReleaseRequest,
                    $request->validated()['controller_position_id'],
                    Auth::id(),
                    $request->validated()['expires_in_seconds'],
                    $request->validated()['released_at'] === null
                        ? CarbonImmutable::now()
                        : CarbonImmutable::parse($request->validated()['released_at'])
                );
            }
        );
    }

    public function rejectReleaseRequest(
        RejectDepartureRelease $request,
        DepartureReleaseRequest $departureReleaseRequest
    ): JsonResponse {
        return $this->performReleaseAction(
            $departureReleaseRequest->id,
            'reject',
            function () use ($request, $departureReleaseRequest) {
                $this->departureReleaseService->rejectReleaseRequest(
                    $departureReleaseRequest,
                    $request->validated()['controller_position_id'],
                    Auth::id()
                );
            }
        );
    }

    public function acknowledgeReleaseRequest(
        AcknowledgeDepartureRelease $request,
        DepartureReleaseRequest $departureReleaseRequest
    ): JsonResponse {
        return $this->performReleaseAction(
            $departureReleaseRequest->id,
            'acknowledge',
            function () use ($request, $departureReleaseRequest) {
                $this->departureReleaseService->acknowledgeReleaseRequest(
                    $departureReleaseRequest,
                    $request->validated()['controller_position_id'],
                    Auth::id()
                );
            }
        );
    }

    public function cancelReleaseRequest(
        DepartureReleaseRequest $departureReleaseRequest
    ): JsonResponse {
        return $this->performReleaseAction(
            $departureReleaseRequest->id,
            'cancel',
            function () use ($departureReleaseRequest) {
                $this->departureReleaseService->cancelReleaseRequest(
                    $departureReleaseRequest,
                    Auth::id()
                );
            }
        );
    }

    private function performReleaseAction(
        int $requestId,
        string $actionType,
        Closure $action
    ): JsonResponse {
        $responseData = null;
        try {
            $action();
            $responseCode = 200;
        } catch (DepartureReleaseDecisionNotAllowedException $decisionNotAllowedException) {
            Log::warning(
                sprintf(
                    'User %d attempted to cancel release %d without permission',
                    Auth::id(),
                    $requestId
                )
            );
            $responseCode = 403;
            $responseData = ['message' => 'You cannot cancel this release'];
        } catch (DepartureReleaseAlreadyDecidedException $alreadyDecidedException) {
            Log::warning(
                sprintf(
                    'User %d attempted to %s release %d, but was already decided',
                    Auth::id(),
                    $actionType,
                    $requestId
                )
            );
            $responseCode = 409;
            $responseData = ['message' => 'You cannot %s this release'];
        }
        return response()->json($responseData, $responseCode);
    }
}
