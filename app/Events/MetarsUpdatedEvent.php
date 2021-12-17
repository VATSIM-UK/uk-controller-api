<?php

namespace App\Events;

use App\Models\Metars\Metar;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Collection;

class MetarsUpdatedEvent extends HighPriorityBroadcastEvent
{
    private Collection $metars;

    public function __construct(Collection $metars)
    {
        $this->metars = $metars;
    }

    public function getMetars(): Collection
    {
        return $this->metars;
    }

    public function broadcastWith(): array
    {
        return $this->metars->map(function (Metar $metar) {
            return [
                'airfield_id' => $metar->airfield_id,
                'raw' => $metar->raw,
                'parsed' => $metar->parsed,
            ];
        })->toArray();
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('metar_updates')];
    }
    
    public function broadcastAs()
    {
        return "metars.updated"
    }
}
