<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Date;
use Mockery;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class NetworkDataServiceTest extends BaseFunctionalTestCase
{
    const NETWORK_DATA = [
        'clients' => [
            [
                'callsign' => 'VIR25A',
                'clienttype' => 'PILOT',
                'latitude' => 'abc',
                'longitude' => 'def',
                'altitude' => '35123',
                'groundspeed' => '123',
                'planned_aircraft' => 'B738',
                'planned_depairport' => 'EGKK',
                'planned_destairport' => 'EGPH',
                'planned_altitude' => '15000',
                'transponder' => '1234',
                'planned_flighttype' => 'I',
                'planned_route' => 'DIRECT',
            ],
            [
                'callsign' => 'BAW123',
                'clienttype' => 'PILOT',
                'latitude' => 'abc',
                'longitude' => 'def',
                'altitude' => '35123',
                'groundspeed' => '123',
                'planned_aircraft' => 'B738',
                'planned_depairport' => 'EGKK',
                'planned_destairport' => 'EGPH',
                'planned_altitude' => '15000',
                'transponder' => '1234',
                'planned_flighttype' => 'I',
                'planned_route' => 'DIRECT',
            ],
            [
                'callsign' => 'LON_S_CTR',
                'clienttype' => 'ATC',
                'latitude' => 'abc',
                'longitude' => 'def',
                'altitude' => '35123',
                'groundspeed' => '123',
                'planned_aircraft' => 'B738',
                'planned_depairport' => 'EGKK',
                'planned_destairport' => 'EGPH',
                'planned_altitude' => '15000',
                'transponder' => '1234',
                'planned_flighttype' => 'I',
                'planned_route' => 'DIRECT',
            ],
        ],
    ];

    /**
     * @var Client|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $client = Mockery::mock(Client::class);
        $this->service = new NetworkDataService($client);
        Carbon::setTestNow(Carbon::now());
        Date::setTestNow(Carbon::now());

        $mockStream = Mockery::mock(StreamInterface::class);
        $mockStream->allows('__toString')->andReturn(json_encode(self::NETWORK_DATA));
        $mockMessage = Mockery::mock(MessageInterface::class);
        $mockMessage->allows('getBody')->andReturn($mockStream);
        $client->allows('get')->with(NetworkDataService::NETWORK_DATA_URL)->andReturn($mockMessage);
    }

    public function testItAddsNewAircraft()
    {
        $this->withoutEvents();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                array_filter(
                    self::NETWORK_DATA['clients'][0],
                    function ($value) { return $value !== 'clienttype';},
                    ARRAY_FILTER_USE_KEY
                ),
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            ),
        );
    }

    public function testItUpdatesExistingAircraft()
    {
        $this->withoutEvents();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                array_filter(
                    self::NETWORK_DATA['clients'][1],
                    function ($value) { return $value !== 'clienttype';},
                    ARRAY_FILTER_USE_KEY
                ),
                ['created_at' => '2020-05-30 17:30:00', 'updated_at' => Carbon::now()]
            ),
        );
    }

    public function testItDoesntAddAtc()
    {
        $this->withoutEvents();
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
           [
               'callsign' => 'LON_S_CTR'
           ]
        );
    }

    public function testItTimesOutAircraft()
    {
        $this->expectsEvents(NetworkAircraftDisconnectedEvent::class);
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'BAW789'
            ]
        );
    }
}
