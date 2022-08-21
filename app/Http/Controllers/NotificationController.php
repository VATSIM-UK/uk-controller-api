<?php

namespace App\Http\Controllers;

use App\Models\Notification\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class NotificationController extends BaseController
{
    public function getActiveNotifications(): JsonResponse
    {
        return response()->json($this->getNotifications(false));
    }

    public function getUnreadNotifications(): JsonResponse
    {
        return response()->json($this->getNotifications(true));
    }

    private function getNotifications(bool $unreadOnly): Collection
    {
        $query = Notification::active()
            ->orderBy('valid_from', 'desc')
            ->with('controllers');

        if ($unreadOnly) {
            $query->unreadBy(auth()->user());
        }

        return $query->get()
            ->map(fn(Notification $notification) => array_merge(
                $notification->toArray(),
                [
                    'valid_from' => $notification->valid_from->toDateTimeString(),
                    'valid_to' => $notification->valid_to->toDateTimeString(),
                ]
            ));
    }

    public function readNotification($id): JsonResponse
    {
        Notification::findOrFail($id)
            ->readBy()
            ->attach(auth()->user());

        return response()->json(['message' => 'ok']);
    }
}
