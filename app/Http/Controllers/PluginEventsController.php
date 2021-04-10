<?php

namespace App\Http\Controllers;

use App\Services\PluginEventsService;
use Illuminate\Http\JsonResponse;

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
}
