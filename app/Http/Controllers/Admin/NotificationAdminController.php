<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\NotificationRequest;
use App\Models\Controller\ControllerPosition;
use App\Models\Notification\Notification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationAdminController extends BaseController
{
    /**
     * Get a list of all notifications, optionally including expired
     * notifications via query parameter in request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getNotifications(Request $request) : JsonResponse
    {
        // if the requester wishes to view expired notifications
        // include expired in the query results
        if ($request->query('include_expired', false)) {
            $notifications = Notification::withCount('controllers')->get();
        } else {
            $notifications = Notification::active()->withCount('controllers')->get();
        }

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Retrieve the details on a specified notification, appending
     * relevant details for further consumption
     *
     * @param Notification $notification
     * @return JsonResponse
     */
    public function getNotification(Notification $notification) : JsonResponse
    {
        // eager load controllers relationship
        $notification->load('controllers');
        // include boolean as to if the notification is still active for QoL.
        $notification->setAppends(['active']);

        // to avoid breaking changes, add new key separate to the
        // toArray functionality to add detailed list of controller_positions
        // to allow them to be updated.
        $notification['positions'] = $notification->controllers;
        // only convert attributes to an array, not default relationship conversion defined in toArray.
        return response()->json(['notification' => $notification->attributesToArray()]);
    }

    /**
     * Based upon a valid request, create a new notification and assign it positions.
     *
     * @param NotificationRequest $request
     * @return JsonResponse
     */
    public function createNotification(NotificationRequest $request) : JsonResponse
    {
        // if all_positions is specified, any data at the positions key will be excluded
        // so conditionally select the right data.
        $positions = $request->get('all_positions', false) ? ControllerPosition::all() : collect($request->get('positions'));

        // only need to check the positions validity when they have been specified.
        if (!$request->get('all_positions', false)) {
            $validPositions = ControllerPosition::all()->pluck('id');

            $positionsAreValid = $positions->every(function ($position) use ($validPositions) {
                return $validPositions->contains($position);
            });

            if (!$positionsAreValid) {
                return response()->json(['message' => 'Invalid positions.'], 400);
            }

            // once valid, replace with a collection of models instead of a
            // collection of IDs so it can be used to create a valid relationship.
            $positions = ControllerPosition::findMany($positions);
        }

        $notification = Notification::create([
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'link' => $request->get('link'),
            'valid_from' => Carbon::parse($request->get('valid_from')),
            'valid_to' => Carbon::parse($request->get('valid_to'))
        ]);

        // associate with the relevant positions
        $notification->controllers()->saveMany($positions);

        return response()->json(['notification_id' => $notification->id], 201);
    }

    /**
     * Delete (via soft-delete) a notification.
     *
     * @param Notification $notification
     * @return JsonResponse
     */
    public function deleteNotification(Notification $notification) : JsonResponse
    {
        // this delete operation will SoftDelete the model, and preserve
        // relationships.
        $notification->delete();

        return response()->json([], 204);
    }
}
