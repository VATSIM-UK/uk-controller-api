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
        $notifications = Notification::active()
            ->orderBy('valid_from', 'desc')
            ->with('controllers')
            ->doesntHave('readBy')
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

    public function readNotification($id) : JsonResponse
    {
        // Confused as to why Notification $id doesn't resolve the instance....
        Notification::findOrFail($id)
            ->readBy()
            ->attach(auth()->user());

        return response()->json(['message' => 'ok']);
    }
}
