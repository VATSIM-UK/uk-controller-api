<?php


namespace App\Http\Controllers;

use App\Exceptions\Prenote\PrenoteAcknowledgementNotAllowedException;
use App\Exceptions\Prenote\PrenoteAlreadyAcknowledgedException;
use App\Exceptions\Prenote\PrenoteCancellationNotAllowedException;
use App\Helpers\Prenote\CreatePrenoteMessageData;
use App\Http\Requests\Prenote\AcknowledgePrenoteMessage;
use App\Http\Requests\Prenote\CreatePrenoteMessage;
use App\Models\Prenote\PrenoteMessage;
use App\Services\PrenoteMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PrenoteMessageController
{
    private PrenoteMessageService $prenoteMessageService;

    public function __construct(PrenoteMessageService $prenoteMessageService)
    {
        $this->prenoteMessageService = $prenoteMessageService;
    }

    public function create(CreatePrenoteMessage $request): JsonResponse
    {
        $messageId = $this->prenoteMessageService->createPrenoteMessage(
            CreatePrenoteMessageData::fromRequest($request->validated(), Auth::id())
        );

        return response()->json(['id' => $messageId], 201);
    }

    public function acknowledge(PrenoteMessage $prenoteMessage, AcknowledgePrenoteMessage $request): JsonResponse
    {
        try {
            $this->prenoteMessageService->acknowledgePrenoteMessage(
                $prenoteMessage,
                Auth::id(),
                $request->validated()['controller_position_id']
            );
        } catch (PrenoteAcknowledgementNotAllowedException $notAllowedException) {
            return response()->json(['message' => $notAllowedException->getMessage()], 403);
        } catch (PrenoteAlreadyAcknowledgedException $alreadyAcknowledgedException) {
            return response()->json(['message' => $alreadyAcknowledgedException->getMessage()], 409);
        }

        return response()->json([], 204);
    }

    public function delete(PrenoteMessage $prenoteMessage): JsonResponse
    {
        try {
            $this->prenoteMessageService->cancelPrenoteMessage(
                $prenoteMessage,
                Auth::id(),
            );
        } catch (PrenoteCancellationNotAllowedException $notAllowedException) {
            return response()->json(['message' => $notAllowedException->getMessage()], 403);
        }

        return response()->json([], 204);
    }
}
