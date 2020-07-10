<?php

namespace App\Services;

use App\BaseUnitTestCase;
use InvalidArgumentException;

class SectorfileServiceTest extends BaseUnitTestCase
{
    /**
     * @dataProvider invalidSectorfileLatitudeFormatProvider
     */
    public function testItThrowsExceptionInvalidSectorfileLatitude(string $latitude, string $longitude)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid sectorfile latitude format ' . $latitude);
        SectorfileService::coordinateFromSectorfile($latitude, $longitude);
    }

    public function invalidSectorfileLatitudeFormatProvider(): array
    {
        return [
            ['', ''],
            ['absdeasdsadas', 'E000.15.42.000'],
            ['054.38.58.000', 'E000.15.42.000'],
            ['N54.38.58.000', 'E000.15.42.000'],
            ['N5.38.58.000', 'E000.15.42.000'],
            ['N054.8.58.000', 'E000.15.42.000'],
            ['N054.38.8.000', 'E000.15.42.000'],
            ['N054.38.58.00', 'E000.15.42.000'],
            ['N054.38.58.0', 'E000.15.42.000'],
            ['N054.38.58', 'E000.15.42.000'],
            ['N054,38,58,000', 'E000.15.42.000'],
            ['M054 38 58 000', 'E000.15.42.000'],
            ['N054.038.058.000', 'E000.15.42.000'],
        ];
    }

    /**
     * @dataProvider invalidSectorfileLongitudeFormatProvider
     */
    public function testItThrowsExceptionInvalidSectorfileLongitude(string $latitude, string $longitude)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid sectorfile longitude format ' . $longitude);
        SectorfileService::coordinateFromSectorfile($latitude, $longitude);
    }

    public function invalidSectorfileLongitudeFormatProvider(): array
    {
        return [
            ['N050.56.44.000', ''],
            ['N050.56.44.000', 'asdsadsadsa'],
            ['N050.56.44.000', '000.15.42.000'],
            ['N050.56.44.000', 'E00.15.42.000'],
            ['N050.56.44.000', 'E0.15.42.000'],
            ['N050.56.44.000', 'E000.1.42.000'],
            ['N050.56.44.000', 'E000.15.4.000'],
            ['N050.56.44.000', 'E000.15.42.00'],
            ['N050.56.44.000', 'E000.15.42.0'],
            ['N050.56.44.000', 'E000.15.42'],
            ['N050.56.44.000', 'E000,15,42,000'],
            ['N050.56.44.000', 'E000 15 42 000'],
            ['N050.56.44.000', 'E000.015.042.000'],
        ];
    }

    /**
     * @dataProvider invalidDegreesMinuteSecondProvider
     */
    public function testItFailsOnInvalidDegreesMinuteSecondData(
        string $latitude,
        string $longitude,
        string $expectedMessage
    ) {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        SectorfileService::coordinateFromSectorfile($latitude, $longitude);
    }

    public function invalidDegreesMinuteSecondProvider(): array
    {
        return [
            ['N090.00.00.001', 'E000.15.42.000', 'Cannot have more than 90 degrees of latitude'],
            ['N090.00.01.000', 'E000.15.42.000', 'Cannot have more than 90 degrees of latitude'],
            ['N090.01.00.000', 'E000.15.42.000', 'Cannot have more than 90 degrees of latitude'],
            ['N091.00.00.000', 'E000.15.42.000', 'Cannot have more than 90 degrees of latitude'],
            ['N050.56.44.000', 'W180.00.00.001', 'Cannot have more than 180 degrees of longitude'],
            ['N050.56.44.000', 'W180.00.01.000', 'Cannot have more than 180 degrees of longitude'],
            ['N050.56.44.000', 'W180.01.00.000', 'Cannot have more than 180 degrees of longitude'],
            ['N050.56.44.000', 'W181.00.00.000', 'Cannot have more than 180 degrees of longitude'],
            ['N050.56.44.000', 'W170.61.00.000', 'Cannot have more than 60 minutes'],
            ['N050.56.44.000', 'W170.00.61.000', 'Cannot have more than 60 seconds'],
            ['N050.56.44.000', 'W170.00.60.001', 'Cannot have more than 60 seconds'],
            ['N080.61.00.000', 'E000.15.42.000', 'Cannot have more than 60 minutes'],
            ['N080.00.61.000', 'E000.15.42.000', 'Cannot have more than 60 seconds'],
            ['N080.00.60.001', 'E000.15.42.000', 'Cannot have more than 60 seconds'],
        ];
    }

    /**
     * @dataProvider validCoordinatesProvider
     */
    public function testItLoadsFromSectorfileCoordinates(
        string $latitude,
        string $longitude,
        float $expectedLatitude,
        float $expectedLongitude
    ) {
        $coordinate = SectorfileService::coordinateFromSectorfile($latitude, $longitude);
        $this->assertEqualsWithDelta($expectedLatitude, $coordinate->getLat(), 0.001);
        $this->assertEqualsWithDelta($expectedLongitude, $coordinate->getLng(), 0.001);
    }

    public function validCoordinatesProvider(): array
    {
        return [
            ['N050.56.44.000', 'E000.15.42.000', 50.94556, 0.26167], // TIMBA
            ['S050.59.06.000', 'W000.11.30.000', -50.985, -0.19167], // WILLO down under
        ];
    }
}
