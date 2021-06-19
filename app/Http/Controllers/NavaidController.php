<?php

namespace App\Http\Controllers;

use App\Models\Navigation\Navaid;
use App\Services\SectorfileService;
use Illuminate\Http\JsonResponse;

class NavaidController extends BaseController
{
    public function __invoke(): JsonResponse
    {
        $navaids = Navaid::all()->map(function (Navaid $navaid) {
            $sectorfileCoordinate = SectorfileService::convertToSectorfileCoordinate(
                $navaid->latitude,
                $navaid->longitude
            );
            return [
                'id' => $navaid->id,
                'identifier' => $navaid->identifier,
                'latitude' => $sectorfileCoordinate->getLatitude(),
                'longitude' => $sectorfileCoordinate->getLongitude(),
            ];
        });
        return response()->json($navaids);
    }
}
