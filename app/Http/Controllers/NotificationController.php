<?php

namespace App\Http\Controllers;

use App\Models\Controller\ControllerPosition;
use App\Models\Notification\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends BaseController
{
    public function getActiveNotifications() : JsonResponse
    {
        $notifications = Notification::active()
            ->orderBy('valid_from', 'desc')
            ->with('controllers')
            ->get()
            ->each(function (Notification $notification) {

                $notification->controllers->each(function (ControllerPosition $controllerPosition) {
                    $controllerPosition->setHidden([
                        'pivot', 'id', 'frequency', 'created_at', 'updated_at'
                    ]);
                });

                $notification->setHidden([
                    'created_at', 'updated_at', 'deleted_at'
                ]);

            });

        return response()->json($notifications);
    }

    public function getUnreadNotifications() : JsonResponse
    {
        $unreadNotifications = Notification::active()
            ->orderBy('valid_from', 'desc')
            ->with('controllers')
            ->unreadBy(auth()->user())
            ->get()
            ->each(function (Notification $notification) {

                $notification->controllers->each(function (ControllerPosition $controllerPosition) {
                    $controllerPosition->setHidden([
                        'pivot', 'id', 'frequency', 'created_at', 'updated_at'
                    ]);
                });

                $notification->setHidden([
                    'created_at', 'updated_at', 'deleted_at'
                ]);

            });

        return response()->json($unreadNotifications);
    }

    public function readNotification($id) : JsonResponse
    {
        Notification::findOrFail($id)
            ->readBy()
            ->attach(auth()->user());

        return response()->json(['message' => 'ok']);
    }
}
