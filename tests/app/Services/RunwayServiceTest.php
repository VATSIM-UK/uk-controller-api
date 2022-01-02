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

class RunwayServiceTest extends BaseFunctionalTestCase
{
    private RunwayService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(RunwayService::class);
    }

    /**
     * @dataProvider goodDataProvider
     */
    public function testItCreatesRunways(
        string $airfield,
        string $firstIdentifier,
        int $firstHeading,
        Coordinate $firstThreshold,
        string $secondIdentifier,
        int $secondHeading,
        Coordinate $secondThreshold
    ) {
        $this->service::addRunwayPair(
            $airfield,
            $firstIdentifier,
            $firstHeading,
            $firstThreshold,
            $secondIdentifier,
            $secondHeading,
            $secondThreshold
        );

        $this->assertDatabaseCount('runways', 2);
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

    public function goodDataProvider(): array
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

    /**
     * @dataProvider badDataProvider
     */
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
    ) {
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
            $this->assertDatabaseCount('runways', 0);
            $this->assertDatabaseCount('runway_runway', 0);
            $this->assertEquals($expectedException, get_class($exception));
            $this->assertEquals($expectedExceptionMessage, $exception->getMessage());
            return;
        }

        $this->fail('Expected exception but none thrown');
    }

    public function badDataProvider(): array
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
        $first = Runway::create(
            [
                'airfield_id' => 1,
                'heading' => 2,
                'identifier' => '27L',
                'threshold_latitude' => 3,
                'threshold_longitude' => 4,
            ]
        );
        $second = Runway::create(
            [
                'airfield_id' => 1,
                'heading' => 5,
                'identifier' => '09R',
                'threshold_latitude' => 6,
                'threshold_longitude' => 7,
            ]
        );
        $first->inverses()->sync($second->id);
        $second->inverses()->sync($first->id);

        $expected = [
            [
                'id' => $first->id,
                'airfield_id' => 1,
                'heading' => 2,
                'identifier' => '27L',
                'threshold_latitude' => 3,
                'threshold_longitude' => 4,
                'inverse_runway_id' => $second->id,
            ],
            [
                'id' => $second->id,
                'airfield_id' => 1,
                'heading' => 5,
                'identifier' => '09R',
                'threshold_latitude' => 6,
                'threshold_longitude' => 7,
                'inverse_runway_id' => $first->id,
            ],
        ];

        $this->assertEquals($expected, $this->service->getRunwaysDependency());
    }
}
