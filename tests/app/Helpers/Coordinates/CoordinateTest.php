<?php

namespace App\Helpers\Coordinates;

use App\BaseUnitTestCase;
use InvalidArgumentException;

class CoordinateTest extends BaseUnitTestCase
{
    public function testItThrowsExceptionIfInvalidLatitudeFromDecimalPositive()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot have more than 90 degrees of latitude, got 90.00001');
        Coordinate::latitudeFromDecimal(90.00001);
    }

    public function testItThrowsExceptionIfInvalidLatitudeFromDecimalNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot have more than 90 degrees of latitude, got -90.00001');
        Coordinate::latitudeFromDecimal(-90.00001);
    }

    /**
     * @dataProvider latitudeDecimalProvider
     */
    public function testItConstructsLatitudeFromDecimal(float $decimal)
    {
        $coordinate = Coordinate::latitudeFromDecimal($decimal);
        $this->assertEquals($decimal, $coordinate->getDecimal());
        $this->assertEquals(Coordinate::TYPE_LATITUDE, $coordinate->getType());
    }

    public function latitudeDecimalProvider(): array
    {
        return [
            [0.0],
            [90.000],
            [89.9999],
            [45.000],
            [-0.001],
            [-90.000],
            [-89.9999],
            [-45.000],
        ];
    }

    public function testItThrowsExceptionIfInvalidLongitudeFromDecimalPositive()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot have more than 180 degrees of longitude, got 180.00001');
        Coordinate::longitudeFromDecimal(180.00001);
    }

    public function testItThrowsExceptionIfInvalidLongitudeFromDecimalNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot have more than 180 degrees of longitude, got -180.00001');
        Coordinate::longitudeFromDecimal(-180.00001);
    }

    /**
     * @dataProvider longitudeDecimalProvider
     */
    public function testItConstructsLongitudeFromDecimal(float $decimal)
    {
        $coordinate = Coordinate::longitudeFromDecimal($decimal);
        $this->assertEquals($decimal, $coordinate->getDecimal());
        $this->assertEquals(Coordinate::TYPE_LONGITUDE, $coordinate->getType());
    }

    public function longitudeDecimalProvider(): array
    {
        return [
            [0.0],
            [90.000],
            [89.9999],
            [45.000],
            [180.000],
            [179.999],
            [-0.001],
            [-90.000],
            [-89.9999],
            [-45.000],
            [-180.000],
            [-179.999],
        ];
    }

    /**
     * @dataProvider invalidSectorfileFormatProvider
     */
    public function testItThrowsExceptionInvalidSectorfileCoordinateFormat(string $coordinate)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid sectorfile coordinate format ' . $coordinate);
        Coordinate::fromSectorfileFormat($coordinate);
    }

    public function invalidSectorfileFormatProvider(): array
    {
        return [
            [''],
            ['absdeasdsadas'],
            ['054.38.58.000'],
            ['N54.38.58.000'],
            ['N5.38.58.000'],
            ['N054.8.58.000'],
            ['N054.38.8.000'],
            ['N054.38.58.00'],
            ['N054.38.58.0'],
            ['N054.38.58'],
            ['N054,38,58,000'],
            ['M054 38 58 000'],
            ['N054.038.058.000'],
        ];
    }

    /**
     * @dataProvider invalidDegreesMinuteSecondProvider
     */
    public function testItFailsOnInvalidDegreesMinuteSecondData(string $coordinate, string $expectedMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        Coordinate::fromSectorfileFormat($coordinate);
    }

    public function invalidDegreesMinuteSecondProvider(): array
    {
        return [
            ['N090.00.00.001', 'Cannot have more than 90 degrees of latitude'],
            ['N090.00.01.000', 'Cannot have more than 90 degrees of latitude'],
            ['N090.01.00.000', 'Cannot have more than 90 degrees of latitude'],
            ['N091.00.00.000', 'Cannot have more than 90 degrees of latitude'],
            ['W180.00.00.001', 'Cannot have more than 180 degrees of longitude'],
            ['W180.00.01.000', 'Cannot have more than 180 degrees of longitude'],
            ['W180.01.00.000', 'Cannot have more than 180 degrees of longitude'],
            ['W181.00.00.000', 'Cannot have more than 180 degrees of longitude'],
            ['W170.61.00.000', 'Cannot have more than 60 minutes'],
            ['W170.00.61.000', 'Cannot have more than 60 seconds'],
            ['W170.00.60.001', 'Cannot have more than 60 seconds'],
        ];
    }

    /**
     * @dataProvider validCoordinatesProvider
     */
    public function testItLoadsFromSectorfileCoordinates(string $coordinate, int $expectedType, float $expected)
    {
        $coordinate = Coordinate::fromSectorfileFormat($coordinate);
        $this->assertEquals($expectedType, $coordinate->getType());
        $this->assertEqualsWithDelta($expected, $coordinate->getDecimal(), 0.001);
    }

    public function validCoordinatesProvider(): array
    {
        return [
            ['N050.56.44.000', Coordinate::TYPE_LATITUDE, 50.94556], // TIMBA W000.11.30.000
            ['E000.15.42.000', Coordinate::TYPE_LONGITUDE, 0.26167], // TIMBA
            ['S050.59.06.000', Coordinate::TYPE_LATITUDE, -50.985], // WILLO down under
            ['W000.11.30.000', Coordinate::TYPE_LONGITUDE, -0.19167], // WILLO down under
        ];
    }

    /**
     * @dataProvider sectorfileCoordinatesProvider
     */
    public function testItConvertsToSectorfileFormat(string $coordinate)
    {
        $this->assertEquals($coordinate, Coordinate::fromSectorfileFormat($coordinate)->getSectorFileFormat());
    }

    public function sectorfileCoordinatesProvider(): array
    {
        return [
            ['N050.56.44.000'], // TIMBA W000.11.30.000
            ['E000.15.42.000'], // TIMBA
            ['S050.59.06.000'], // WILLO down under
            ['W000.11.30.000'], // WILLO down under
        ];
    }

    public function testItConvertsToRadians()
    {
        $this->assertEquals(deg2rad(123.45), Coordinate::longitudeFromDecimal(123.45)->convertToRadians());
    }
}
