<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Extending this class causes events to be broadcast on the high priority queue,
 * which keeps important events (e.g. stand allocations) as close to realtime as possible.
 */
abstract class HighPriorityBroadcastEvent implements ShouldBroadcast
{
    public $queue = 'high';
}
