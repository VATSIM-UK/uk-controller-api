<?php
namespace App\Providers;

use App\Helpers\AltimeterSettingRegions\AltimeterSettingRegion as RegionHelper;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion as RegionModel;
use App\Services\MetarService;
use App\Services\RegionalPressureService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider for regional pressure settings.
 *
 * Class RegionalPressureServiceProvider
 * @package App\Providers
 */
class RegionalPressureServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Registers the RegionalPressureService with the app.
     */
    public function register()
    {
        $this->app->bind(RegionalPressureService::class, function ($app) {
            // Create dependencies
            $metarUri = env('APP_REGIONAL_PRESSURES_URL', '');
            $http = new Client();
            $metarParser = new MetarService();

            // Get all the altimeter setting regions
            $regions = [];
            $allRegions = RegionModel::all();
            foreach ($allRegions as $region) {
                $regions[] = new RegionHelper($region->name, $region->variation, json_decode($region->station));
            }

            return new RegionalPressureService($http, $metarUri, $metarParser, $regions);
        });
    }

    /**
     * Tells the framework what services this provider provides.
     *
     * @return array Array of provided services.
     */
    public function provides()
    {
        return [RegionalPressureService::class];
    }
}
