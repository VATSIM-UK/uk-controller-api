<?php


namespace App\Http\Controllers;


use App\Http\Requests\Prenote\CreatePrenoteMessageRequest;
use App\Services\PrenoteMessageService;
use Illuminate\Support\Facades\Auth;

class PrenoteMessageController
{
    private PrenoteMessageService $prenoteMessageService;

    public function __construct(PrenoteMessageService $prenoteMessageService)
    {
        $this->prenoteMessageService = $prenoteMessageService;
    }

    public function create(CreatePrenoteMessageRequest $request)
    {
        $validated = $request->validated();
        $messageId = $this->prenoteMessageService->createPrenoteMessage(
            $validated['callsign'],
            $validated['departure_airfield'],
            $validated['departure_sid'],
            $validated['destination_airfield'],
            Auth::id(),
            $validated['requesting_controller_id'],
            $validated['target_controller_id'],
            $validated['expires_in_seconds'],
        );

        return response()->json(['id' => $messageId], 201);
    }
}
