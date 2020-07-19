<?php

namespace App\Services;

use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NetworkDataService
{
    const NETWORK_DATA_URL = "http://cluster.data.vatsim.net/vatsim-data.json";

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function updateNetworkData(): void
    {
        $data = json_decode($this->client->get(self::NETWORK_DATA_URL)->getBody(), true);
        $this->processClients($data['clients']);
        $this->handleTimeouts();
    }

    private function processClients(array $clients): void
    {
        foreach ($clients as $client) {
            if (!isset($client['clienttype'])) {
                Log::error('Client type missing for aircraft', $client);
                continue;
            }

            if ($client['clienttype'] !== 'PILOT') {
                continue;
            }

            $aircraft = NetworkAircraft::updateOrCreate(
                ['callsign' => $client['callsign']],
                $client
            );
            event(new NetworkAircraftUpdatedEvent($aircraft));
        }
    }

    /**
     * If any aircraft has passed the timeout window, remove it from the list.
     */
    private function handleTimeouts(): void
    {
        NetworkAircraft::all()->each(function (NetworkAircraft $aircraft) {
            if ($aircraft->updated_at < Carbon::now()->subMinutes(10)) {
                event(new NetworkAircraftDisconnectedEvent($aircraft));
                $aircraft->delete();
            }
        });
    }
}
