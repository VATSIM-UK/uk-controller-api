<?php

namespace App\Services;

use App\Models\FlightInformationRegion\FlightInformationRegion;
use App\Models\FlightInformationRegion\FlightInformationRegionBoundary;
use Location\Polygon;

class FlightInformationRegionService
{
    public static function getBoundaryPolygon(string $key): Polygon
    {
        $fir = FlightInformationRegion::where('identification_code', $key)
            ->firstOrFail();

        $addedPoints = [];
        $polygon = new Polygon();
        $fir->boundaries()->orderBy('id', 'ASC')->get()->each(function (FlightInformationRegionBoundary $boundary) use (
            $polygon,
            &$addedPoints
        ) {
            $firstPointKey = $boundary->start_latitude . ' ' . $boundary->start_longitude;

            if (!in_array($firstPointKey, $addedPoints)) {
                $addedPoints[] = $firstPointKey;
                $polygon->addPoint(
                    SectorfileService::coordinateFromSectorfile($boundary->start_latitude, $boundary->start_longitude)
                );
            }

            $secondPointKey = $boundary->finish_latitude . ' ' . $boundary->finish_longitude;
            if (!in_array($secondPointKey, $addedPoints)) {
                $addedPoints[] = $secondPointKey;
                $polygon->addPoint(
                    SectorfileService::coordinateFromSectorfile($boundary->finish_latitude, $boundary->finish_longitude)
                );
            }
        });

        return $polygon;
    }
}
