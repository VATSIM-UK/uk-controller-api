<?php

namespace App\Http\Controllers;

use App\Models\Notification\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class NotificationController extends BaseController
{
    public function getActiveNotifications() : JsonResponse
    {
        return response()->json($this->getNotifications(false));
    }

    public function getUnreadNotifications() : JsonResponse
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

        return $query->get();
    }

    public function readNotification($id) : JsonResponse
    {
        Notification::findOrFail($id)
            ->readBy()
            ->attach(auth()->user());

        return response()->json(['message' => 'ok']);
    }
}
