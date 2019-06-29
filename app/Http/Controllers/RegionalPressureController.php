<?php
namespace App\Http\Controllers;

use App\Services\RegionalPressureService;

/**
 * A controller for handling requests in relation to regional pressure settings.
 *
 * Class RegionalPressureController
 * @package App\Http\Controllers
 */
class RegionalPressureController extends BaseController
{
    // The cache key
    const RPS_CACHE_KEY = 'regional_pressures';

    /**
     * Returns all the regional pressure settings as JSON.
     *
     * @param  RegionalPressureService $service Service for getting regional pressures.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegionalPressures(RegionalPressureService $service)
    {
        // Get the pressures from cache
        $pressures = $service->getRegionalPressuresFromCache();

        // Set success depending on whether they exist, JSON encode and return
        return response()->json(
            [
                'data' => $pressures,
            ],
            (!is_array($pressures) || empty($pressures)) ? 503 : 200,
            [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
