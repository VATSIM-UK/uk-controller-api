<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;

class AirlineServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var AirlineService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AirlineService::class);
    }

    /**
     * @dataProvider aircraftProvider
     */
    public function testItReturnsAirlinesForAircraft(string $callsign, string $expectedAirline)
    {
        $this->assertEquals(
            $expectedAirline,
            $this->service->getAirlineForAircraft(NetworkAircraft::create(['callsign' => $callsign]))->icao_code
        );
    }

    public function aircraftProvider(): array
    {
        return [
            'British Airways' => [
                'BAW6356',
                'BAW',
            ],
            'British Airways Domestic' => [
                'SHT12A',
                'SHT',
            ],
            'Virgin' => [
                'VIR27F',
                'VIR',
            ],
        ];
    }

    public function testItReturnsNullOnInvalidAirline()
    {
        $this->assertNull($this->service->getAirlineForAircraft(NetworkAircraft::create(['callsign' => '***'])));
    }
}
