<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stand\StoreStandReservationPlan;
use App\Models\Stand\StandReservationPlan;
use App\Models\Stand\StandReservationPlanStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StandReservationPlanController extends Controller
{
    public function store(StoreStandReservationPlan $request): JsonResponse
    {
        $validated = $request->validated();

        $plan = StandReservationPlan::create(
            [
                'name' => $validated['name'],
                'contact_email' => $validated['contact_email'],
                'payload' => $validated['payload'],
                'submitted_by' => $request->user()?->id,
                'submitted_at' => Carbon::now(),
                'status' => StandReservationPlanStatus::SUBMITTED,
            ]
        );

        return response()->json(
            [
                'id' => $plan->id,
                'status' => $plan->status->value,
            ],
            201
        );
    }
}
