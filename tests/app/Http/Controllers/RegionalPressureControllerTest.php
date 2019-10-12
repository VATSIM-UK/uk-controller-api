<?php


namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\AltimeterSettingRegions\RegionalPressureSetting;

class RegionalPressureControllerTest extends BaseApiTestCase
{
    public function testItReturnsPressures()
    {
        RegionalPressureSetting::create(
            [
                'altimeter_setting_region_id' => 1,
                'value' => 986,
            ]
        );

        RegionalPressureSetting::create(
            [
                'altimeter_setting_region_id' => 2,
                'value' => 988,
            ]
        );

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'regional-pressure')
            ->assertJson(
                [
                    'ASR_BOBBINGTON' => 986,
                    'ASR_TOPPINGTON' => 988,
                ]
            )
            ->assertStatus(200);
    }

    public function testItDoesntAcceptPost()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'regional-pressure')->assertStatus(405);
    }
}
