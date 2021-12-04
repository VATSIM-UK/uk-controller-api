<?php

namespace App\Services;

use App\BaseUnitTestCase;
use InvalidArgumentException;

class SectorfileServiceTest extends BaseUnitTestCase
{
    const VALID_TIMBA_LATITUDE = 'N050.56.44.000';
    const VALID_TIMBA_LONGITUDE = 'E000.15.42.000';
    const LATITUDE_DEGREES_ERROR_MESSAGE = 'Cannot have more than 90 degrees of latitude';
    const LONGITUDE_DEGREES_ERROR_MESSAGE = 'Cannot have more than 180 degrees of longitude';
    const MINUTES_ERROR_MESSAGE = 'Cannot have more than 60 minutes';
    const SECONDS_ERROR_MESSAGE = 'Cannot have more than 60 seconds';

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
            ['absdeasdsadas', self::VALID_TIMBA_LONGITUDE],
            ['054.38.58.000', self::VALID_TIMBA_LONGITUDE],
            ['N54.38.58.000', self::VALID_TIMBA_LONGITUDE],
            ['N5.38.58.000', self::VALID_TIMBA_LONGITUDE],
            ['N054.8.58.000', self::VALID_TIMBA_LONGITUDE],
            ['N054.38.8.000', self::VALID_TIMBA_LONGITUDE],
            ['N054.38.58.00', self::VALID_TIMBA_LONGITUDE],
            ['N054.38.58.0', self::VALID_TIMBA_LONGITUDE],
            ['N054.38.58', self::VALID_TIMBA_LONGITUDE],
            ['N054,38,58,000', self::VALID_TIMBA_LONGITUDE],
            ['M054 38 58 000', self::VALID_TIMBA_LONGITUDE],
            ['N054.038.058.000', self::VALID_TIMBA_LONGITUDE],
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
            [self::VALID_TIMBA_LATITUDE, ''],
            [self::VALID_TIMBA_LATITUDE, 'asdsadsadsa'],
            [self::VALID_TIMBA_LATITUDE, '000.15.42.000'],
            [self::VALID_TIMBA_LATITUDE, 'E00.15.42.000'],
            [self::VALID_TIMBA_LATITUDE, 'E0.15.42.000'],
            [self::VALID_TIMBA_LATITUDE, 'E000.1.42.000'],
            [self::VALID_TIMBA_LATITUDE, 'E000.15.4.000'],
            [self::VALID_TIMBA_LATITUDE, 'E000.15.42.00'],
            [self::VALID_TIMBA_LATITUDE, 'E000.15.42.0'],
            [self::VALID_TIMBA_LATITUDE, 'E000.15.42'],
            [self::VALID_TIMBA_LATITUDE, 'E000,15,42,000'],
            [self::VALID_TIMBA_LATITUDE, 'E000 15 42 000'],
            [self::VALID_TIMBA_LATITUDE, 'E000.015.042.000'],
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
            ['N090.00.00.001', self::VALID_TIMBA_LONGITUDE, self::LATITUDE_DEGREES_ERROR_MESSAGE],
            ['N090.00.01.000', self::VALID_TIMBA_LONGITUDE, self::LATITUDE_DEGREES_ERROR_MESSAGE],
            ['N090.01.00.000', self::VALID_TIMBA_LONGITUDE, self::LATITUDE_DEGREES_ERROR_MESSAGE],
            ['N091.00.00.000', self::VALID_TIMBA_LONGITUDE, self::LATITUDE_DEGREES_ERROR_MESSAGE],
            [self::VALID_TIMBA_LATITUDE, 'W180.00.00.001', self::LONGITUDE_DEGREES_ERROR_MESSAGE],
            [self::VALID_TIMBA_LATITUDE, 'W180.00.01.000', self::LONGITUDE_DEGREES_ERROR_MESSAGE],
            [self::VALID_TIMBA_LATITUDE, 'W180.01.00.000', self::LONGITUDE_DEGREES_ERROR_MESSAGE],
            [self::VALID_TIMBA_LATITUDE, 'W181.00.00.000', self::LONGITUDE_DEGREES_ERROR_MESSAGE],
            [self::VALID_TIMBA_LATITUDE, 'W170.61.00.000', self::MINUTES_ERROR_MESSAGE],
            [self::VALID_TIMBA_LATITUDE, 'W170.00.61.000', self::SECONDS_ERROR_MESSAGE],
            [self::VALID_TIMBA_LATITUDE, 'W170.00.60.001', self::SECONDS_ERROR_MESSAGE],
            ['N080.61.00.000', self::VALID_TIMBA_LONGITUDE, self::MINUTES_ERROR_MESSAGE],
            ['N080.00.61.000', self::VALID_TIMBA_LONGITUDE, self::SECONDS_ERROR_MESSAGE],
            ['N080.00.60.001', self::VALID_TIMBA_LONGITUDE, self::SECONDS_ERROR_MESSAGE],
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
            [self::VALID_TIMBA_LATITUDE, self::VALID_TIMBA_LONGITUDE, 50.94556, 0.26167], // TIMBA
            ['S050.59.06.000', 'W000.11.30.000', -50.985, -0.19167], // WILLO down under
        ];
    }

    /**
     * @dataProvider sectorfileFormatProvider
     */
    public function testItConvertsToSectorfileFormat(float $latitude, float $longitude, string $expected)
    {
        $this->assertSame(
            $expected,
            (string)SectorfileService::convertToSectorfileCoordinate($latitude, $longitude)
        );
    }

    public function sectorfileFormatProvider()
    {
        return [
            'North east' => [
                50.9455556,
                0.2616667,
                'N050.56.44.000 E000.15.42.000'
            ],
            'South west' => [
                -50.9455556,
                -0.2616667,
                'S050.56.44.000 W000.15.42.000'
            ],
            'Sixty seconds' => [
                50.5205556,
                -1.3333333,
                'N050.31.14.000 W001.20.00.000'
            ],  // This is KATHY
        ];
    }

    /**
     * @dataProvider badNatsLatitudeFormatProvider
     */
    public function testItThrowsAnExceptionOnBadNatsLatitudeFormat(string $latitude)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid nats latitude format');
        SectorfileService::coordinateFromNats($latitude, '0080000W');
    }

    public function badNatsLatitudeFormatProvider(): array
    {
        return [
            'No direction modifier' => [
                '525005',
            ],
            'Direction modifier for longitude' => [
                '525005E',
            ],
            'Direction modifier invalid' => [
                '525005X',
            ],
            'Too short' => [
                '52505N',
            ],
            'Too long' => [
                '1525005N',
            ],
        ];
    }

    /**
     * @dataProvider badNatsLongitudeFormatProvider
     */
    public function testItThrowsAnExceptionOnBadNatsLongitudeFormat(string $longitude)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid nats longitude format');
        SectorfileService::coordinateFromNats('525005N', $longitude);
    }

    public function badNatsLongitudeFormatProvider(): array
    {
        return [
            'No direction modifier' => [
                '0024613',
            ],
            'Direction modifier for latitude' => [
                '0024613N',
            ],
            'Direction modifier invalid' => [
                '0024613X',
            ],
            'Too short' => [
                '024613W',
            ],
            'Too long' => [
                '00254613W',
            ],
        ];
    }

    public function testItThrowsAnExceptionOnNatsCoordinateInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot have more than 60 seconds');
        SectorfileService::coordinateFromNats('525065N', '0024613W');
    }

    /**
     * @dataProvider validNatsFormatProvider
     */
    public function testItConvertsFromNatsFormat(
        string $latitude,
        string $longitude,
        float $expectedLatitude,
        float $expectedLongitude
    ) {
        $coordinate = SectorfileService::coordinateFromNats($latitude, $longitude);

        $this->assertEqualsWithDelta($expectedLatitude, $coordinate->getLat(), 0.0001);
        $this->assertEqualsWithDelta($expectedLongitude, $coordinate->getLng(), 0.0001);
    }

    public function validNatsFormatProvider(): array
    {
        return [
            'North east' => [
                '505644N',
                '0001542E',
                50.9455556,
                0.2616667,
            ],
            'South west' => [
                '505644S',
                '0001542W',
                -50.9455556,
                -0.2616667,
            ],
            'Sixty seconds' => [
                '503114N',
                '0012000W',
                50.5205556,
                -1.3333333,
            ],  // This is KATHY
        ];
    }
}
