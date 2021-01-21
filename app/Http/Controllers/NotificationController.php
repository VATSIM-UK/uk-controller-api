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
                    'id', 'disabled_at', 'created_at', 'updated_at'
                ]);

            });

        return response()->json($notifications);
    }
}
