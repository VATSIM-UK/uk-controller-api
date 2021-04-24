<?php

namespace App\Http\Controllers;

use App\Services\PluginEventsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PluginEventsController
{
    private PluginEventsService $pluginEventsService;

    public function __construct(PluginEventsService $pluginEventsService)
    {
        $this->pluginEventsService = $pluginEventsService;
    }

    public function getLatestPluginEventId(): JsonResponse
    {
        return response()->json(
            [
                'event_id' => $this->pluginEventsService->getLatestPluginEventId(),
            ]
        );
    }

    public function getRecentPluginEvents(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'previous' => 'required|integer',
            ]
        );

        return response()->json($this->pluginEventsService->getRecentPluginEvents($validated['previous']));
    }
}
