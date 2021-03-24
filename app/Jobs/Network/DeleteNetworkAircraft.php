<?php

namespace App\Jobs\Network;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteNetworkAircraft implements ShouldQueue
{
    use Dispatchable, SerializesModels, Queueable;

    private NetworkAircraft $disconnectingAircraft;

    public function __construct(NetworkAircraft $disconnectingAircraft)
    {
        $this->disconnectingAircraft = $disconnectingAircraft;
    }

    public function handle(): void
    {
        $this->disconnectingAircraft->delete();
    }
}
