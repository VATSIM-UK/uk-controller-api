<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\FlightInformationRegion\FlightInformationRegion;
use App\Models\FlightInformationRegion\FlightInformationRegionBoundary;

class FlightInformationRegionServiceTest extends BaseFunctionalTestCase
{
    public function testItReturnsTheRegionBoundaryPolygon()
    {
        $region = FlightInformationRegion::create(['identification_code' => 'EGXX']);
        $willoTimba = new FlightInformationRegionBoundary(
            [
                'flight_information_region_id' => $region->id,
                'start_latitude' => 'N050.59.06.000',
                'start_longitude' => 'W000.11.30.000',
                'finish_latitude' => 'N050.56.44.000',
                'finish_longitude' => 'E000.15.42.000',
                'description' => 'Test',
            ]
        );

        $timbaBig = new FlightInformationRegionBoundary(
            [
                'flight_information_region_id' => $region->id,
                'start_latitude' => 'N050.56.44.000',
                'start_longitude' => 'E000.15.42.000',
                'finish_latitude' => 'N051.19.51.150',
                'finish_longitude' => 'E000.02.05.320',
                'description' => 'Test',
            ]
        );

        $bigOck = new FlightInformationRegionBoundary(
            [
                'flight_information_region_id' => $region->id,
                'start_latitude' => 'N051.19.51.150',
                'start_longitude' => 'E000.02.05.320',
                'finish_latitude' => 'N051.18.18.000',
                'finish_longitude' => 'W000.26.50.000',
                'description' => 'Test',
            ]
        );

        $ockWillo = new FlightInformationRegionBoundary(
            [
                'flight_information_region_id' => $region->id,
                'start_latitude' => 'N051.18.18.000',
                'start_longitude' => 'W000.26.50.000',
                'finish_latitude' => 'N050.59.06.000',
                'finish_longitude' => 'W000.11.30.000',
                'description' => 'Test',
            ]
        );

        $region->boundaries()->saveMany([$willoTimba, $timbaBig, $bigOck, $ockWillo]);

        $polygon = FlightInformationRegionService::getBoundaryPolygon('EGXX');
        $this->assertEquals(4, $polygon->getNumberOfPoints());
        $this->assertEqualsWithDelta(50.985, $polygon->getPoints()[0]->getLat(), 0.001);
        $this->assertEqualsWithDelta(-0.19167, $polygon->getPoints()[0]->getLng(), 0.001);
        $this->assertEqualsWithDelta(50.94556, $polygon->getPoints()[1]->getLat(), 0.001);
        $this->assertEqualsWithDelta(0.26167, $polygon->getPoints()[1]->getLng(), 0.001);
        $this->assertEqualsWithDelta(51.33087, $polygon->getPoints()[2]->getLat(), 0.001);
        $this->assertEqualsWithDelta(0.034811, $polygon->getPoints()[2]->getLng(), 0.001);
        $this->assertEqualsWithDelta(51.30500, $polygon->getPoints()[3]->getLat(), 0.001);
        $this->assertEqualsWithDelta(-0.44722, $polygon->getPoints()[3]->getLng(), 0.001);
    }
}
