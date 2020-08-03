<?php

namespace App\Services;

use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\QueryException;
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
        try {
            $data = json_decode($this->client->get(self::NETWORK_DATA_URL)->getBody(), true);
        } catch (Exception $exception) {
            Log::warning('Failed to download network data: ' . $exception->getMessage());
            return;
        }
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

            event(
                new NetworkAircraftUpdatedEvent(
                    self::createOrUpdateNetworkAircraft($client['callsign'], $client)
                )
            );
        }
    }

    /**
     * If any aircraft has passed the timeout window, remove it from the list.
     */
    private function handleTimeouts(): void
    {
        NetworkAircraft::all()->each(
            function (NetworkAircraft $aircraft) {
                if ($aircraft->updated_at < Carbon::now()->subMinutes(20)) {
                    $aircraft->delete();
                    event(new NetworkAircraftDisconnectedEvent($aircraft));
                }
            }
        );
    }

    public static function createOrUpdateNetworkAircraft(
        string $callsign,
        array $details = []
    ): NetworkAircraft {
        try {
            $aircraft = NetworkAircraft::updateOrCreate(
                ['callsign' => $callsign],
                array_merge(
                    ['callsign' => $callsign],
                    $details
                )
            );
            $aircraft->touch();
        } catch (QueryException $queryException) {
            if ($queryException->errorInfo[1] !== 1062) {
                throw $queryException;
            }
            $aircraft = NetworkAircraft::find($callsign);
        }

        return $aircraft;
    }

    public static function firstOrCreateNetworkAircraft(
        string $callsign,
        array $details = []
    ): NetworkAircraft {
        try {
            $aircraft = NetworkAircraft::firstOrCreate(
                ['callsign' => $callsign],
                array_merge(
                    ['callsign' => $callsign],
                    $details
                )
            );
        } catch (QueryException $queryException) {
            if ($queryException->errorInfo[1] !== 1062) {
                throw $queryException;
            }
            $aircraft = NetworkAircraft::find($callsign);
        }

        return $aircraft;
    }
}
