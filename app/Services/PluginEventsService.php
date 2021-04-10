<?php

namespace App\Services;

use App\Models\Plugin\PluginEvent;

class PluginEventsService
{
    public function getLatestPluginEventId(): int
    {
        return PluginEvent::max('id') ?? 0;
    }
}
