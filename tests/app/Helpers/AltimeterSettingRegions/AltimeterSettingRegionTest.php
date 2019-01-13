<?php
namespace App\Helpers\AltimeterSettingRegions;

use App\BaseUnitTestCase;

class AltimeterSettingRegionTest extends BaseUnitTestCase
{
    public function testItConstructs()
    {
        $region = new AltimeterSettingRegion('Cotswold', 0, []);
        $this->assertInstanceOf(AltimeterSettingRegion::class, $region);
    }

    public function testItKnowsItsName()
    {
        $region = new AltimeterSettingRegion('Cotswold', 0, []);
        $this->assertEquals('Cotswold', $region->getName());
    }

    public function testItReturnsDefaultIfNoAirfields()
    {
        $region = new AltimeterSettingRegion('Cotswold', 0, []);
        $this->assertEquals($region::DEFAULT_MIN_QNH, $region->calculateRegionalPressure([]));
    }

    public function testItIgnoresNonExistentPressures()
    {
        $region = new AltimeterSettingRegion('Cotswold', 0, ['EGGD']);
        $this->assertEquals($region::DEFAULT_MIN_QNH, $region->calculateRegionalPressure([]));
    }

    public function testItIgnoresNonExistentAirfields()
    {
        $region = new AltimeterSettingRegion('Cotswold', 0, ['EGGD']);
        $this->assertEquals($region::DEFAULT_MIN_QNH, $region->calculateRegionalPressure(['EGFF Q1009']));
    }

    public function testItIgnoresVariationIfNoPressures()
    {
        $region = new AltimeterSettingRegion('Cotswold', 9999, ['EGGD']);
        $this->assertEquals($region::DEFAULT_MIN_QNH, $region->calculateRegionalPressure([]));
    }

    public function testItTakesTheLowestPressureInTheRegion()
    {
        $region = new AltimeterSettingRegion('Cotswold', 0, ['EGGD', 'EGFF']);
        $this->assertEquals(
            998,
            $region->calculateRegionalPressure(['EGGD' => 1001, 'EGFF' => 998])
        );
    }

    public function testItVariesThePressure()
    {
        $variation = 2;
        $region = new AltimeterSettingRegion('Cotswold', $variation, ['EGGD', 'EGFF']);

        $this->assertLessThanOrEqual(2, abs(998 - $region->calculateRegionalPressure(['EGGD' => 1001, 'EGFF' => 998])));
        $this->assertLessThanOrEqual(2, abs(998 - $region->calculateRegionalPressure(['EGGD' => 1001, 'EGFF' => 998])));
        $this->assertLessThanOrEqual(2, abs(998 - $region->calculateRegionalPressure(['EGGD' => 1001, 'EGFF' => 998])));
        $this->assertLessThanOrEqual(2, abs(998 - $region->calculateRegionalPressure(['EGGD' => 1001, 'EGFF' => 998])));
    }
}
