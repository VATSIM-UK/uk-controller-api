<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Mockery;
use PDOException;

class NetworkDataServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var array[]
     */
    private $networkData;

    /**
     * @var NetworkDataService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->networkData = [
            'pilots' => [
                $this->getPilotData('VIR25A', true),
                $this->getPilotData('BAW123', false),
                $this->getPilotData('RYR824', true),
            ]
        ];

        Carbon::setTestNow(Carbon::now());
        Date::setTestNow(Carbon::now());
        $this->service = $this->app->make(NetworkDataService::class);
    }

    private function fakeNetworkDataReturn(): void
    {
        Http::fake(
            [
                NetworkDataService::NETWORK_DATA_URL => Http::response(json_encode($this->networkData), 200)
            ]
        );
    }

    public function testItHandlesErrorCodesFromNetworkDataFeed()
    {
        $this->doesntExpectEvents(NetworkAircraftUpdatedEvent::class);
        Http::fake(
            [
                NetworkDataService::NETWORK_DATA_URL => Http::response('', 500)
            ]
        );
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'VIR25A',
            ]
        );
    }

    public function testItHandlesExceptionsFromNetworkDataFeed()
    {
        $this->doesntExpectEvents(NetworkAircraftUpdatedEvent::class);
        Http::fake(
            function () {
                throw new Exception('LOL');
            }
        );
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'VIR25A',
            ]
        );
    }

    public function testItHandlesMissingClientData()
    {
        $this->doesntExpectEvents(NetworkAircraftUpdatedEvent::class);
        Http::fake(
            [
                NetworkDataService::NETWORK_DATA_URL => Http::response(json_encode(['not_clients' => '']), 200)
            ]
        );
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'VIR25A',
            ]
        );
    }

    public function testItAddsNewAircraftFromDataFeed()
    {
        $this->withoutEvents();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $this->getTransformedPilotData('VIR25A'),
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            ),
        );
    }

    public function testItUpdatesExistingAircraftFromDataFeed()
    {
        $this->withoutEvents();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $this->getTransformedPilotData('BAW123', false),
                ['created_at' => '2020-05-30 17:30:00', 'updated_at' => Carbon::now()]
            ),
        );
    }

    public function testItUpdatesExistingAircraftOnTheGroundFromDataFeed()
    {
        $this->withoutEvents();
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
        $this->withoutEvents();
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'LON_S_CTR'
            ]
        );
    }

    public function testItTimesOutAircraftFromDataFeed()
    {
        $this->expectsEvents(NetworkAircraftDisconnectedEvent::class);
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
        $this->assertDatabaseMissing(
            'network_aircraft',
            [
                'callsign' => 'BAW789'
            ]
        );
    }

    public function testItFiresUpdatedEventsOnDataFeed()
    {
        $this->expectsEvents(NetworkAircraftUpdatedEvent::class);
        $this->expectsEvents(NetworkAircraftUpdatedEvent::class);
        $this->fakeNetworkDataReturn();
        $this->service->updateNetworkData();
    }

    public function testItCreatesNetworkAircraft()
    {
        $expectedData = $this->getTransformedPilotData('AAL123');
        $actual = NetworkDataService::createOrUpdateNetworkAircraft('AAL123', $expectedData);
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
        $actual = NetworkDataService::createOrUpdateNetworkAircraft('AAL123');
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
        $actual = NetworkDataService::createOrUpdateNetworkAircraft(
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
        $actual = NetworkDataService::createOrUpdateNetworkAircraft('AAL123');
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

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsExistingModelIfDuplicateInsert()
    {
        $mock = Mockery::mock('overload:App\\Models\\Vatsim\\NetworkAircraft');
        $mock->shouldReceive('updateOrCreate')
            ->andReturnUsing(
                function () {
                    $pdoException = new PDOException();
                    $pdoException->errorInfo = [1 => 1062];
                    throw new QueryException('', [], $pdoException);
                }
            );
        $original = new NetworkAircraft(['callsign' => 'AAL123']);
        $mock->shouldReceive('find')->andReturn($original);

        $this->assertEquals($original, NetworkDataService::createOrUpdateNetworkAircraft('AAL123'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItThrowsQueryExceptionIfNotDuplicateViolation()
    {
        $this->expectException(QueryException::class);
        $mock = Mockery::mock('overload:App\\Models\\Vatsim\\NetworkAircraft');
        $mock->shouldReceive('updateOrCreate')
            ->andReturnUsing(
                function () {
                    $pdoException = new PDOException();
                    $pdoException->errorInfo = [1 => 9999];
                    throw new QueryException('', [], $pdoException);
                }
            );
        $original = new NetworkAircraft(['callsign' => 'AAL123']);
        $mock->shouldReceive('find')->andReturn($original);

        NetworkDataService::createOrUpdateNetworkAircraft('AAL123');
    }

    public function testItCreatesNetworkAircraftInFirstOrCreate()
    {
        $expectedData = $this->getTransformedPilotData('AAL123');
        $actual = NetworkDataService::firstOrCreateNetworkAircraft('AAL123', $expectedData);
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

    public function testItCreatesNetworkAircraftInFirstOrCreateCallsignOnly()
    {
        $actual = NetworkDataService::firstOrCreateNetworkAircraft('AAL123');
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

    public function testItFindsNetworkAircraftFirstOrCreateWithData()
    {
        Carbon::setTestNow(Carbon::now());
        $expectedData = $this->getTransformedPilotData('AAL123');
        $expected = NetworkAircraft::create($expectedData);
        $expected->created_at = Carbon::now()->subHour(2);
        $expected->updated_at = Carbon::now()->subHour(1);
        $expected->save();
        $expected->refresh();
        $this->assertEquals(
            $this->filterBySet($expected->toArray()),
            NetworkDataService::firstOrCreateNetworkAircraft(
                'AAL123',
                ['groundspeed' => '234567']
            )->toArray()
        );

        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $expectedData,
                [
                    'created_at' => Carbon::now()->subHour(2)->toDateTimeString(),
                    'updated_at' => Carbon::now()->subHour(1)->toDateTimeString(),
                ]
            ),
        );
    }

    public function testItFindsNetworkAircraftFirstOrCreateCallsignOnly()
    {
        Carbon::setTestNow(Carbon::now());
        $expectedData = $this->getTransformedPilotData('AAL123');
        $expected = NetworkAircraft::create($expectedData);
        $expected->created_at = Carbon::now()->subHour(2);
        $expected->updated_at = Carbon::now()->subHour(1);
        $expected->save();
        $expected->refresh();

        $this->assertEquals(
            $this->filterBySet($expected->toArray()),
            NetworkDataService::firstOrCreateNetworkAircraft('AAL123')->toArray()
        );

        $this->assertDatabaseHas(
            'network_aircraft',
            array_merge(
                $expectedData,
                [
                    'created_at' => Carbon::now()->subHour(2)->toDateTimeString(),
                    'updated_at' => Carbon::now()->subHour(1)->toDateTimeString(),
                ]
            ),
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsExistingModelIfDuplicateInsertOnFirstOrCreate()
    {
        $mock = Mockery::mock('overload:App\\Models\\Vatsim\\NetworkAircraft');
        $mock->shouldReceive('firstOrCreate')
            ->andReturnUsing(
                function () {
                    $pdoException = new PDOException();
                    $pdoException->errorInfo = [1 => 1062];
                    throw new QueryException('', [], $pdoException);
                }
            );
        $original = new NetworkAircraft(['callsign' => 'AAL123']);
        $mock->shouldReceive('find')->andReturn($original);

        $this->assertEquals($original, NetworkDataService::firstOrCreateNetworkAircraft('AAL123'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItThrowsQueryExceptionIfNotDuplicateViolationOnFirstOrCreate()
    {
        $this->expectException(QueryException::class);
        $mock = Mockery::mock('overload:App\\Models\\Vatsim\\NetworkAircraft');
        $mock->shouldReceive('firstOrCreate')
            ->andReturnUsing(
                function () {
                    $pdoException = new PDOException();
                    $pdoException->errorInfo = [1 => 9999];
                    throw new QueryException('', [], $pdoException);
                }
            );
        $original = new NetworkAircraft(['callsign' => 'AAL123']);
        $mock->shouldReceive('find')->andReturn($original);

        NetworkDataService::firstOrCreateNetworkAircraft('AAL123');
    }

    private function getPilotData(string $callsign, bool $hasFlightplan): array
    {
        return [
            'callsign' => $callsign,
            'latitude' => 54.66,
            'longitude' => -6.21,
            'altitude' => 35123,
            'groundspeed' => 123,
            'transponder' => 1234,
            'flight_plan' => $hasFlightplan
                ? [
                    'aircraft' => 'B738',
                    'departure' => 'EGKK',
                    'arrival' => 'EGPH',
                    'altitude' => '15001',
                    'flight_rules' => 'I',
                    'route' => 'DIRECT',
                ]
                : null,
        ];
    }

    private function getTransformedPilotData(string $callsign, bool $hasFlightplan = true): array
    {
        $pilot = $this->getPilotData($callsign, $hasFlightplan);
        $baseData = [
            'callsign' => $pilot['callsign'],
            'latitude' => $pilot['latitude'],
            'longitude' => $pilot['longitude'],
            'altitude' => $pilot['altitude'],
            'groundspeed' => $pilot['groundspeed'],
            'transponder' => $pilot['transponder'],
        ];

        if ($hasFlightplan) {
            $baseData = array_merge(
                $baseData,
                [
                    'planned_aircraft' => $pilot['flight_plan']['aircraft'],
                    'planned_depairport' => $pilot['flight_plan']['departure'],
                    'planned_destairport' => $pilot['flight_plan']['arrival'],
                    'planned_altitude' => $pilot['flight_plan']['altitude'],
                    'planned_flighttype' => $pilot['flight_plan']['flight_rules'],
                    'planned_route' => $pilot['flight_plan']['route'],
                ]
            );
        }

        return $baseData;
    }

    private function filterBySet(array $data): array
    {
        return array_filter(
            $data,
            function ($value) {
                return isset($value);
            }
        );
    }
}
