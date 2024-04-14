<?php

namespace App\Http\Controllers;

use App\Models\Plugin\PluginLog;
use App\Http\Requests\Plugin\PluginLog as PluginLogRequest;
use Illuminate\Http\JsonResponse;

class PluginLogController extends BaseController
{
    public function createPluginLogEntry(PluginLogRequest $log): JsonResponse
    {
        $validated = $log->validated();
        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata']);
        }

        $pluginLog = PluginLog::create($validated);
        return response()->json(['id' => $pluginLog->id], 201);
    }
}
