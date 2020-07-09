<?php

namespace App\Helpers\Coordinates;

use App\BaseUnitTestCase;

class CoordinatePairTest extends BaseUnitTestCase
{
    public function testItLoadsFromSectorfileCoordinates()
    {
        $pair = CoordinatePair::fromSectorfileCoordinates('N050.56.44.000', 'E000.15.42.000');
        $this->assertEqualsWithDelta(Coordinate::latitudeFromDecimal(50.94556), $pair->getLatitude(), 0.001);
        $this->assertEqualsWithDelta(Coordinate::longitudeFromDecimal(0.26167), $pair->getLongitude(), 0.001);
    }

    public function testItLoadsFromDecimalCoordinates()
    {
        $pair = CoordinatePair::fromDecimal(50.94556, 0.26167);
        $this->assertEqualsWithDelta(Coordinate::latitudeFromDecimal(50.94556), $pair->getLatitude(), 0.001);
        $this->assertEqualsWithDelta(Coordinate::longitudeFromDecimal(0.26167), $pair->getLongitude(), 0.001);
    }

    public function testItGetsSectorfileFormat()
    {
        $pair = CoordinatePair::fromSectorfileCoordinates('N050.56.44.000', 'E000.15.42.000');
        $this->assertEquals('N050.56.44.000 E000.15.42.000', $pair->getSectorfileFormat());
    }

    /**
     * @dataProvider latLongProvider
     */
    public function testGetDistance(float $latOne, float $longOne, float $latTwo, float $longTwo, float $expected)
    {
        $this->fail('Make this negative');
        $this->assertEqualsWithDelta(
            $expected,
            CoordinatePair::fromDecimal($latOne, $longOne)->getDistance(CoordinatePair::fromDecimal($latTwo, $longTwo)),
            0.001
        );
    }

    public function latLongProvider(): array
    {
        return [
            [50.94556, 0.26167, 50.985, -0.19167, 17.304878], // TIMBA then WILLO
            [50.985, -0.19167, 50.94556, 0.26167, 17.304878] // WILLO then TIMBA
        ];
    }

    /**
     * @dataProvider absoluteLatLongProvider
     */
    public function testGetAbsoluteDistance(float $latOne, float $longOne, float $latTwo, float $longTwo, float $expected)
    {
        $this->assertEqualsWithDelta(
            $expected,
            CoordinatePair::fromDecimal($latOne, $longOne)->getAbsoluteDistance(CoordinatePair::fromDecimal($latTwo, $longTwo)),
            0.001
        );
    }

    public function absoluteLatLongProvider(): array
    {
        return [
            [50.94556, 0.26167, 50.985, -0.19167, 17.304878], // TIMBA then WILLO
            [50.985, -0.19167, 50.94556, 0.26167, 17.304878] // WILLO then TIMBA
        ];
    }
}
