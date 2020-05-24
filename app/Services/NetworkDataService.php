<?php

namespace App\Services;

use App\Models\Vatsim\NetworkAircraft;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Schema;

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

    public function downloadNetworkData(): void
    {
        $data = json_decode($this->client->get(self::NETWORK_DATA_URL)->getBody(), true);
        $this->processClients($data['clients']);
    }

    private function processClients(array $clients): void
    {
        foreach ($clients as $client) {
            if ($client['clienttype'] !== 'PILOT') {
                continue;
            }

            NetworkAircraft::updateOrCreate(
                ['callsign' => $client['callsign']],
                $client
            );
        }
    }
}
