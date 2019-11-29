<?php
namespace App\Providers;

use App\Helpers\AltimeterSettingRegions\AltimeterSettingRegion as RegionHelper;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion as RegionModel;
use App\Services\MetarService;
use App\Services\RegionalPressureService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

/**
 * Service Provider for regional pressure settings.
 *
 * Class RegionalPressureServiceProvider
 * @package App\Providers
 */
class RegionalPressureServiceProvider extends ServiceProvider
{
    /**
     * Registers the RegionalPressureService with the app.
     */
    public function register()
    {
        $this->app->bind(RegionalPressureService::class, function (Application $app) {
            // Create dependencies
            $metarUri = env('APP_REGIONAL_PRESSURES_URL', '');
            $http = new Client();
            $metarParser = $app->make(MetarService::class);
            return new RegionalPressureService($http, $metarUri, $metarParser);
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
