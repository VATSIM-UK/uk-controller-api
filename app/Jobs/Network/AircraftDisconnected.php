<?php

namespace App\Jobs\Network;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AircraftDisconnected implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public NetworkAircraft $aircraft;

    public function __construct(NetworkAircraft $aircraft)
    {
        $this->aircraft = $aircraft;
    }

    /**
     * @param Collection $subtasks
     */
    public function handle(Collection $subtasks): void
    {
        DB::transaction(function () use ($subtasks) {
            $subtasks->each(function (AircraftDisconnectedSubtask $subtask) {
                $subtask->perform($this->aircraft);
            });
        });
    }
}
