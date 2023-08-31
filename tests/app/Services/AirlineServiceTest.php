<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\Airline\AirlinesUpdatedEvent;
use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;
use PHPUnit\Framework\Attributes\DataProvider;

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

    public function testItReturnsNullIfCallsignDoesNotMatchAirline()
    {
        $this->assertNull($this->service->airlineIdForCallsign('ABC123'));
    }

    public function testItReturnsAirlineIdForCallsign()
    {
        $airline = Airline::factory()->create();
        $this->assertEquals(
            $airline->id,
            $this->service->airlineIdForCallsign($airline->icao_code . '123')
        );
    }

    public function testItCachesAirlineIdForCallsignResult()
    {
        $airline = Airline::factory()->create();
        $originalIcaoCode = $airline->icao_code;
        $this->assertEquals(
            $airline->id,
            $this->service->airlineIdForCallsign($airline->icao_code . '123')
        );

        $airline->update(['icao_code' => 'XXX']);

        $this->assertEquals(
            $airline->id,
            $this->service->airlineIdForCallsign($originalIcaoCode . '123')
        );
    }

    public function testItClearsAirlineIdForCallsignCacheWhenAirlinesUpdated()
    {
        $airline = Airline::factory()->create();
        $originalIcaoCode = $airline->icao_code;
        $this->assertEquals(
            $airline->id,
            $this->service->airlineIdForCallsign($originalIcaoCode . '123')
        );

        $airline->update(['icao_code' => 'XXX']);
        $this->assertEquals($airline->id, $this->service->airlineIdForCallsign($originalIcaoCode . '123'));
        $this->assertNull($this->service->airlineIdForCallsign('XXX123'));

        $this->service->airlinesUpdated();

        $this->assertNull($this->service->airlineIdForCallsign($originalIcaoCode . '123'));
        $this->assertEquals($airline->id, $this->service->airlineIdForCallsign('XXX123'));
    }

    public function testItClearsAirlineIdForCallsignCacheOnEvent()
    {
        $airline = Airline::factory()->create();
        $originalIcaoCode = $airline->icao_code;
        $this->assertEquals(
            $airline->id,
            $this->service->airlineIdForCallsign($airline->icao_code . '123')
        );

        event(new AirlinesUpdatedEvent());

        $this->assertEquals($airline->id, $this->service->airlineIdForCallsign($originalIcaoCode . '123'));
        $this->assertNull($this->service->airlineIdForCallsign('XXX123'));
    }

    // TODO: Add events to filament

    #[DataProvider('aircraftProvider')]
    public function testItReturnsAirlinesForAircraft(string $callsign, string $expectedAirline)
    {
        $this->assertEquals(
            $expectedAirline,
            $this->service->getAirlineForAircraft(NetworkAircraft::create(['callsign' => $callsign]))->icao_code
        );
    }

    public static function aircraftProvider(): array
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

    #[DataProvider('slugProvider')]
    public function testItReturnsCallsignSlugs(string $callsign, string $expectedSlug)
    {
        $this->assertEquals(
            $expectedSlug,
            $this->service->getCallsignSlugForAircraft(NetworkAircraft::create(['callsign' => $callsign]))
        );
    }

    public static function slugProvider(): array
    {
        return [
            'Unknown airline' => [
                'X1X123',
                'X1X123',
            ],
            'Known airline' => [
                'BAW123AF',
                '123AF',
            ],
        ];
    }
}
