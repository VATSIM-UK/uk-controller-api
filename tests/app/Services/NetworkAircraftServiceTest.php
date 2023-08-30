<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\NetworkDataUpdatedEvent;
use App\Jobs\Network\AircraftDisconnected;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Mockery;

class NetworkAircraftServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var array[]
     */
    private array $pilotData;
    private NetworkAircraftService $service;
    private NetworkDataService $mockDataService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pilotData = [
            $this->getPilotData('VIR25A', true),
            $this->getPilotData('BAW123', false, null, null, '1234'),
            $this->getPilotData('RYR824', true),
            $this->getPilotData('LOT551', true, 44.372, 26.040),
            $this->getPilotData('BMI221', true, null, null, '777'),
            $this->getPilotData('BMI222', true, null, null, '12a4'),
            $this->getPilotData('BMI223', true, null, null, '7778'),
            $this->getPilotData('BAW999', true, aircraftType: 'XYZ'),
        ];

        Bus::fake();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        Date::setTestNow(Carbon::now());
        $this->mockDataService = Mockery::mock(NetworkDataService::class);
        $this->app->instance(NetworkDataService::class, $this->mockDataService);
        $this->service = $this->app->make(NetworkAircraftService::class);
    }

    private function fakeNetworkDataReturn(): void
    {
        $this->mockDataService->shouldReceive('getNetworkAircraftData')->andReturn(collect($this->pilotData));
    }

    public function testItAddsNewAircraftFromDataFeed()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $this->getTransformedPilotData('VIR25A'),
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'transponder_last_updated_at' => Carbon::now()
                ]
            ),
        );
    }

    public function testItAddsNewAircraftWithUnknownAircraftType()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $this->getTransformedPilotData('BAW999', aircraftType: 'XYZ'),
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'transponder_last_updated_at' => Carbon::now()
                ]
            ),
        );
    }

    public function testItUpdatesExistingAircraftFromDataFeed()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $this->getTransformedPilotData('BAW123', false, '1234'),
                [
                    'created_at' => '2020-05-30 17:30:00',
                    'updated_at' => Carbon::now()
                ]
            ),
        );
    }

    public function testItUpdatesExistingAircraftTransponderChangedAtFromDataFeed()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $this->getTransformedPilotData('RYR824', false),
                [
                    'created_at' => '2020-05-30 17:30:00',
                    'updated_at' => Carbon::now(),
                    'transponder_last_updated_at' => Carbon::now(),
                ]
            ),
        );
    }

    public function testItDoesntUpdateExistingAircraftTransponderChangedAtFromDataFeedIfSame()
    {
        // Update the transponder 15 minutes ago
        $transponderUpdatedAt = Carbon::now()->subMinutes(15);
        DB::table('network_aircraft')->where('callsign', 'BAW123')->update(
            ['transponder_last_updated_at' => $transponderUpdatedAt]
        );

        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $this->getTransformedPilotData('BAW123', false, '1234'),
                [
                    'created_at' => '2020-05-30 17:30:00',
                    'updated_at' => Carbon::now(),
                    'transponder_last_updated_at' => $transponderUpdatedAt,
                ]
            ),
        );
    }

    public function testItUpdatesExistingAircraftOnTheGroundFromDataFeed()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $this->getTransformedPilotData('RYR824'),
                ['created_at' => '2020-05-30 17:30:00', 'updated_at' => Carbon::now()]
            ),
        );
    }

    public function testItDoesntAddAtcFromDataFeed()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'LON_S_CTR'
            ]
        );
    }

    public function testItDoesntUpdateAircraftOutOfRangeFromTheDataFeed()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'LOT551',
            ],
        );
    }

    public function testItDoesntUpdateAircraftWithInvalidTransponderFromDataFeed()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'BMI221',
            ],
        );
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'BMI222',
            ],
        );
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'BMI223',
            ],
        );
    }

    public function testItTimesOutAircraftFromDataFeed()
    {
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        Bus::assertNotDispatchedSync(AircraftDisconnected::class, function (AircraftDisconnected $job)
        {
            return $job->aircraft->callsign === 'BAW123';
        });
        Bus::assertNotDispatchedSync(AircraftDisconnected::class, function (AircraftDisconnected $job)
        {
            return $job->aircraft->callsign === 'BAW456 ';
        });
        Bus::assertDispatchedSync(AircraftDisconnected::class, function (AircraftDisconnected $job)
        {
            return $job->aircraft->callsign === 'BAW789';
        });
    }

    public function testItFiresUpdatedEventsOnDataFeed()
    {
        Event::fake();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        Event::assertDispatched(NetworkDataUpdatedEvent::class);
    }

    public function testItCreatesNetworkAircraft()
    {
        $expectedData = $this->getTransformedPilotData('AAL123');
        $actual = NetworkAircraftService::createOrUpdateNetworkAircraft('AAL123', $expectedData);
        $actual->refresh();
        $expected = NetworkAircraft::find('AAL123');
        $this->assertEquals($expected->toArray(), $actual->toArray());
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $expectedData,
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            ),
        );
    }

    public function testItCreatesNetworkAircraftCallsignOnly()
    {
        $actual = NetworkAircraftService::createOrUpdateNetworkAircraft('AAL123');
        $actual->refresh();
        $expected = NetworkAircraft::find('AAL123');
        $this->assertEquals($expected->toArray(), $actual->toArray());
        $this->assertDatabaseHas(
            'network_aircraft',
            [
                'callsign' => 'AAL123',
            ]
        );
    }

    public function testItUpdatesNetworkAircraft()
    {
        $expectedData = $this->getTransformedPilotData('AAL123');
        NetworkAircraft::create($expectedData);
        $actual = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'AAL123',
            ['groundspeed' => '456789']
        );
        $expected = NetworkAircraft::find('AAL123');
        $this->assertEquals($expected->toArray(), $actual->toArray());

        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $expectedData,
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'groundspeed' => '456789'
                ]
            ),
        );
    }

    public function testItUpdatesNetworkAircraftCallsignOnly()
    {
        $expectedData = $this->getTransformedPilotData('AAL123');
        NetworkAircraft::create($expectedData);
        $actual = NetworkAircraftService::createOrUpdateNetworkAircraft('AAL123');
        $expected = NetworkAircraft::find('AAL123');
        $this->assertEquals($expected->toArray(), $actual->toArray());

        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $expectedData,
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            ),
        );
    }

    public function testItCreatesPlaceholderAircraft()
    {
        NetworkAircraftService::createPlaceholderAircraft('AAL123');
        $actual = NetworkAircraft::find('AAL123');
        $actual->refresh();
        $expected = NetworkAircraft::find('AAL123');
        $this->assertEquals($expected->toArray(), $actual->toArray());
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                ['callsign' => 'AAL123'],
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            ),
        );
    }

    public function testItFindsNetworkAircraftInCreatePlaceholder()
    {
        Carbon::setTestNow(Carbon::now());
        $expectedData = $this->getTransformedPilotData('AAL123');
        $expected = NetworkAircraft::create($expectedData);
        $expected->created_at = Carbon::now()->subHours(2);
        $expected->updated_at = Carbon::now()->subMinutes(5);
        $expected->save();
        $expected->refresh();
        NetworkAircraftService::createPlaceholderAircraft('AAL123');
        $placeholder = NetworkAircraft::find('AAL123');
        $this->assertEquals(
            $expected->toArray(),
            $placeholder->toArray()
        );

        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $expectedData,
                [
                    'created_at' => Carbon::now()->subHours(2)->toDateTimeString(),
                    'updated_at' => Carbon::now()->subMinutes(5)->toDateTimeString(),
                ]
            ),
        );
    }

    private function getPilotData(
        string $callsign,
        bool $hasFlightplan,
        float $latitude = null,
        float $longitude = null,
        string $transponder = null,
        string $aircraftType = 'B738',
    ): array
    {
        return [
            'callsign' => $callsign,
            'cid' => self::ACTIVE_USER_CID,
            'latitude' => $latitude ?? 54.66,
            'longitude' => $longitude ?? -6.21,
            'altitude' => 35123,
            'groundspeed' => 123,
            'transponder' => $transponder ?? '0457',
            'flight_plan' => $hasFlightplan
            ? [
                'aircraft' => sprintf('H/%s/M', $aircraftType),
                'aircraft_short' => $aircraftType,
                'departure' => 'EGKK',
                'arrival' => 'EGPH',
                'altitude' => '15001',
                'flight_rules' => 'I',
                'route' => 'DIRECT',
                'remarks' => 'hi'
            ]
            : null,
        ];
    }

    private function getTransformedPilotData(
        string $callsign,
        bool $hasFlightplan = true,
        string $transponder = null,
        string $aircraftType = 'B738'
    ): array
    {
        $pilot = $this->getPilotData($callsign, $hasFlightplan, null, null, $transponder, $aircraftType);
        $baseData = [
            'callsign' => $pilot['callsign'],
            'cid' => $pilot['cid'],
            'latitude' => $pilot['latitude'],
            'longitude' => $pilot['longitude'],
            'altitude' => $pilot['altitude'],
            'groundspeed' => $pilot['groundspeed'],
            'transponder' => Str::padLeft($pilot['transponder'], '0', 4),
        ];

        if ($hasFlightplan) {
            $baseData = array_merge(
                $baseData,
                [
                    'planned_aircraft' => $pilot['flight_plan']['aircraft'],
                    'planned_aircraft_short' => $pilot['flight_plan']['aircraft_short'],
                    'planned_depairport' => $pilot['flight_plan']['departure'],
                    'planned_destairport' => $pilot['flight_plan']['arrival'],
                    'planned_altitude' => $pilot['flight_plan']['altitude'],
                    'planned_flighttype' => $pilot['flight_plan']['flight_rules'],
                    'planned_route' => $pilot['flight_plan']['route'],
                    'remarks' => $pilot['flight_plan']['remarks'],
                    'aircraft_id' => $pilot['flight_plan']['aircraft_short'] === 'B738' ? 1 : null,
                ]
            );
        }

        return $baseData;
    }
}
