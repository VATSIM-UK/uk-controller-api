<?php

namespace App\Services;

use App\Models\Plugin\PluginEvent;
use Illuminate\Support\Collection;

class PluginEventsService
{
    public function getLatestPluginEventId(): int
    {
        return PluginEvent::max('id') ?? 0;
    }

    public function getRecentPluginEvents(int $lastReceivedEventId): Collection
    {
        return PluginEvent::where('id', '>', $lastReceivedEventId)->orderBy('id')->get()->map(
            function (PluginEvent $event) {
                return [
                    'id' => $event->id,
                    'event' => $event->event,
                ];
            }
        );
    }
}
