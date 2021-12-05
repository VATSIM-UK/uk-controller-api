<?php

namespace App\Http\Controllers;

use App\Exceptions\MissedApproach\CannotAcknowledgeMissedApproachException;
use App\Exceptions\MissedApproach\MissedApproachAlreadyActiveException;
use App\Http\Requests\MissedApproach\AcknowledgeMissedApproach;
use App\Http\Requests\MissedApproach\CreateMissedApproachNotification;
use App\Models\MissedApproach\MissedApproachNotification;
use App\Services\MissedApproachService;
use Illuminate\Http\JsonResponse;

class MissedApproachController
{
    private MissedApproachService $service;

    public function __construct(MissedApproachService $service)
    {
        $this->service = $service;
    }

    public function create(CreateMissedApproachNotification $request): JsonResponse
    {
        try {
            $notification = $this->service->sendMissedApproachNotification($request->validated()['callsign']);
            return response()->json(
                ['id' => $notification['id'], 'expires_at' => $notification->expires_at->toDateTimeString()],
                201
            );
        } catch (MissedApproachAlreadyActiveException $alreadyActiveException) {
            return response()->json(['message' => $alreadyActiveException->getMessage()], 409);
        }
    }

    public function acknowledge(
        AcknowledgeMissedApproach $request,
        MissedApproachNotification $missedApproachNotification
    ): JsonResponse {
        try {
            $this->service->acknowledge($missedApproachNotification, $request->validated()['remarks']);
        } catch (CannotAcknowledgeMissedApproachException $cannotAcknowledge) {
            return response()->json(['message' => 'You cannot acknowledge this missed approach'])->setStatusCode(403);
        }

        return response()->json();
    }
}
