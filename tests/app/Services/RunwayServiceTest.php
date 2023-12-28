<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Exceptions\Airfield\AirfieldNotFoundException;
use App\Exceptions\Runway\RunwayHeadingInvalidException;
use App\Exceptions\Runway\RunwayIdentifierInvalidException;
use App\Exceptions\Runway\RunwayThresholdInvalidException;
use App\Helpers\Sectorfile\Coordinate;
use App\Models\Airfield\Airfield;
use App\Models\Runway\Runway;
use Exception;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

class RunwayServiceTest extends BaseFunctionalTestCase
{
    private RunwayService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(RunwayService::class);
    }

    #[DataProvider('goodDataProvider')]
    public function testItCreatesRunways(
        string $airfield,
        string $firstIdentifier,
        int $firstHeading,
        Coordinate $firstThreshold,
        string $secondIdentifier,
        int $secondHeading,
        Coordinate $secondThreshold
    )
    {
        $initialCount = DB::table('runways')->count();
        $initialCountPairs = DB::table('runway_runway')->count();
        $this->service::addRunwayPair(
            $airfield,
            $firstIdentifier,
            $firstHeading,
            $firstThreshold,
            $secondIdentifier,
            $secondHeading,
            $secondThreshold
        );

        $this->assertDatabaseCount('runways', $initialCount + 2);
        $this->assertDatabaseCount('runway_runway', $initialCountPairs + 2);
        $firstCoordinate = SectorfileService::coordinateFromSectorfile(
            $firstThreshold->getLatitude(),
            $firstThreshold->getLongitude()
        );
        $this->assertDatabaseHas(
            'runways',
            [
                'airfield_id' => Airfield::where('code', $airfield)->first()->id,
                'identifier' => $firstIdentifier,
                'heading' => $firstHeading,
                'threshold_latitude' => number_format($firstCoordinate->getLat(), 8),
                'threshold_longitude' => number_format($firstCoordinate->getLng(), 8),
            ]
        );

        $secondCoordinate = SectorfileService::coordinateFromSectorfile(
            $secondThreshold->getLatitude(),
            $secondThreshold->getLongitude()
        );
        $this->assertDatabaseHas(
            'runways',
            [
                'airfield_id' => Airfield::where('code', $airfield)->first()->id,
                'identifier' => $secondIdentifier,
                'heading' => $secondHeading,
                'threshold_latitude' => number_format($secondCoordinate->getLat(), 8),
                'threshold_longitude' => number_format($secondCoordinate->getLng(), 8),
            ]
        );

        $this->assertDatabaseHas(
            'runway_runway',
            [
                'first_runway_id' => Runway::where('identifier', $firstIdentifier)->first()->id,
                'second_runway_id' => Runway::where('identifier', $secondIdentifier)->first()->id,
            ]
        );
        $this->assertDatabaseHas(
            'runway_runway',
            [
                'first_runway_id' => Runway::where('identifier', $secondIdentifier)->first()->id,
                'second_runway_id' => Runway::where('identifier', $firstIdentifier)->first()->id,
            ]
        );
    }

    public static function goodDataProvider(): array
    {
        // Coordinates are actually EGKK's 08R/26L
        return [
            'Normal headings' => [
                'EGLL',
                '26L',
                257,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08R',
                77,
                new Coordinate('N051.08.45.100', 'W000.12.24.590')
            ],
            'Zero headings' => [
                'EGLL',
                '26L',
                0,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08R',
                180,
                new Coordinate('N051.08.45.100', 'W000.12.24.590')
            ],
        ];
    }

    #[DataProvider('badDataProvider')]
    public function testItThrowsExceptionOnBadRunwayCreationData(
        string $airfield,
        string $firstIdentifier,
        int $firstHeading,
        Coordinate $firstThreshold,
        string $secondIdentifier,
        int $secondHeading,
        Coordinate $secondThreshold,
        string $expectedException,
        string $expectedExceptionMessage
    )
    {
        $initialCount = DB::table('runways')->count();
        $initialCountPairs = DB::table('runway_runway')->count();

        try {
            $this->service::addRunwayPair(
                $airfield,
                $firstIdentifier,
                $firstHeading,
                $firstThreshold,
                $secondIdentifier,
                $secondHeading,
                $secondThreshold
            );
        } catch (Exception $exception) {
            $this->assertDatabaseCount('runways', $initialCount);
            $this->assertDatabaseCount('runway_runway', $initialCountPairs);
            $this->assertEquals($expectedException, get_class($exception));
            $this->assertEquals($expectedExceptionMessage, $exception->getMessage());
            return;
        }

        $this->fail('Expected exception but none thrown');
    }

    public static function badDataProvider(): array
    {
        // Coordinates are actually EGKK's 08R/26L
        return [
            'Unknown airfield' => [
                'EGKK',
                '26L',
                257,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08R',
                77,
                new Coordinate('N051.08.45.100', 'W000.12.24.590'),
                AirfieldNotFoundException::class,
                'Airfield with icao EGKK not found',
            ],
            'Invalid first identifier' => [
                'EGLL',
                '26X',
                257,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08R',
                77,
                new Coordinate('N051.08.45.100', 'W000.12.24.590'),
                RunwayIdentifierInvalidException::class,
                'Runway identifier 26X is not valid',
            ],
            'Invalid first heading' => [
                'EGLL',
                '26L',
                361,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08R',
                77,
                new Coordinate('N051.08.45.100', 'W000.12.24.590'),
                RunwayHeadingInvalidException::class,
                'Runway heading 361 is not valid',
            ],
            'Invalid first threshold' => [
                'EGLL',
                '26L',
                257,
                new Coordinate('N999.09.02.430', 'W000.10.18.930'),
                '08R',
                77,
                new Coordinate('N051.08.45.100', 'W000.12.24.590'),
                RunwayThresholdInvalidException::class,
                'Runway threshold for 26L is invalid: Cannot have more than 90 degrees of latitude'
            ],
            'Invalid second identifier' => [
                'EGLL',
                '26L',
                257,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08X',
                77,
                new Coordinate('N051.08.45.100', 'W000.12.24.590'),
                RunwayIdentifierInvalidException::class,
                'Runway identifier 08X is not valid',
            ],
            'Invalid second heading' => [
                'EGLL',
                '26L',
                257,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08R',
                -5,
                new Coordinate('N051.08.45.100', 'W000.12.24.590'),
                RunwayHeadingInvalidException::class,
                'Runway heading -5 is not valid',
            ],
            'Invaid second threshold' => [
                'EGLL',
                '26L',
                257,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08R',
                77,
                new Coordinate('N999.08.45.100', 'W000.12.24.590'),
                RunwayThresholdInvalidException::class,
                'Runway threshold for 08R is invalid: Cannot have more than 90 degrees of latitude',
            ],
            'Headings not inverse' => [
                'EGLL',
                '26L',
                257,
                new Coordinate('N051.09.02.430', 'W000.10.18.930'),
                '08R',
                76,
                new Coordinate('N051.08.45.100', 'W000.12.24.590'),
                RunwayHeadingInvalidException::class,
                'Runway headings 257 and 76 are not inverse',
            ],
        ];
    }

    public function testItReturnsRunwaysDependency()
    {
        $expected = [
            [
                'id' => 1,
                'airfield_id' => 1,
                'heading' => 270,
                'identifier' => '27L',
                'threshold_latitude' => 1,
                'threshold_longitude' => 2,
                'inverse_runway_id' => 2,
                'threshold_elevation' => 4,
                'glideslope_angle' => 3,
            ],
            [
                'id' => 2,
                'airfield_id' => 1,
                'heading' => 90,
                'identifier' => '09R',
                'threshold_latitude' => 3,
                'threshold_longitude' => 4,
                'inverse_runway_id' => 1,
                'threshold_elevation' => 5,
                'glideslope_angle' => 4,
            ],
            [
                'id' => 3,
                'airfield_id' => 2,
                'heading' => 330,
                'identifier' => '33',
                'threshold_latitude' => 5,
                'threshold_longitude' => 6,
                'inverse_runway_id' => null,
                'threshold_elevation' => 6,
                'glideslope_angle' => 5,
            ],
        ];

        $this->assertEquals($expected, $this->service->getRunwaysDependency());
    }

    public function testItThrowsExceptionIfRunwayIdentifierIsInvalidForInverseCalculation()
    {
        $this->expectException(RunwayIdentifierInvalidException::class);
        RunwayService::inverseRunwayIdentifier('foo');
    }

    #[DataProvider('runwayIdentifierProvider')]
    public function testItCalculatesReverseRunwayIdentifier(string $identifier, string $expected)
    {
        $this->assertEquals(
            $expected,
            RunwayService::inverseRunwayIdentifier($identifier)
        );
    }

    public static function runwayIdentifierProvider(): array
    {
        return [
            'Single digit, no side' => ['09', '27'],
            'Double digit, no side' => ['27', '09'],
            'Runway 18, no side' => ['18', '36'],
            'Runway 36, no side' => ['36', '18'],
            'Runway 01, no side' => ['01', '19'],
            'Runway 19, no side' => ['19', '01'],
            'Runway 35L' => ['35L', '17R'],
            'Runway 08R' => ['08R', '26L'],
            'Runway 05L' => ['05L', '23R'],
            'Runway 15G' => ['15G', '33G'],
            'Runway 33G' => ['33G', '15G'],
            'Runway 06C' => ['06C', '24C'],
            'Runway 24C' => ['24C', '06C'],
        ];
    }
}
