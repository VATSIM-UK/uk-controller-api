<?php
namespace App\Http\Controllers;

use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use App\Services\RegionalPressureService;
use Illuminate\Http\JsonResponse;

/**
 * A controller for handling requests in relation to regional pressure settings.
 *
 * Class RegionalPressureController
 * @package App\Http\Controllers
 */
class RegionalPressureController extends BaseController
{
    /**
     * @var RegionalPressureService
     */
    private $regionalPressureService;

    /**
     * RegionalPressureController constructor.
     * @param RegionalPressureService $regionalPressureService
     */
    public function __construct(RegionalPressureService $regionalPressureService)
    {
        $this->regionalPressureService = $regionalPressureService;
    }

    public function getRegionalPressures() : JsonResponse
    {
        return response()->json($this->regionalPressureService->getRegionalPressureArray());
    }

    public function getAltimeterSettingRegions() : JsonResponse
    {
        return response()->json(AltimeterSettingRegion::all());
    }
}
