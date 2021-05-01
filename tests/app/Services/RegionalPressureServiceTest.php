<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\RegionalPressuresUpdatedEvent;
use App\Models\AltimeterSettingRegions\RegionalPressureSetting;
use App\Models\Metars\Metar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

class RegionalPressureServiceTest extends BaseFunctionalTestCase
{
    private RegionalPressureService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(RegionalPressureService::class);
        Event::fake();
    }

    public function testItGeneratesNewRegionalPressures()
    {
        $metars = collect(
            [
                new Metar(['airfield_id' => 1, 'qnh' => 1015]),
                new Metar(['airfield_id' => 2, 'qnh' => 1014]),
                new Metar(['airfield_id' => 3, 'qnh' => 1013]),
            ]
        );
        $this->service->updateRegionalPressuresFromMetars($metars);

        $this->assertDatabaseCount('regional_pressure_settings', 2);
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 1,
                'value' => 1012,
            ],
        );
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 2,
                'value' => 1013,
            ],
        );

        Event::assertDispatched(RegionalPressuresUpdatedEvent::class, function ($event) {
            return $event->pressures === ['ASR_BOBBINGTON' => 1012, 'ASR_TOPPINGTON' => 1013];
        });
    }

    public function testItGeneratesUpdatesRegionalPressures()
    {
        RegionalPressureSetting::create(['altimeter_setting_region_id' => 1, 'value' => 1015]);
        RegionalPressureSetting::create(['altimeter_setting_region_id' => 2, 'value' => 1019]);

        $metars = collect(
            [
                new Metar(['airfield_id' => 1, 'qnh' => 1015]),
                new Metar(['airfield_id' => 2, 'qnh' => 1014]),
                new Metar(['airfield_id' => 3, 'qnh' => 1013]),
            ]
        );
        $this->service->updateRegionalPressuresFromMetars($metars);

        $this->assertDatabaseCount('regional_pressure_settings', 2);
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 1,
                'value' => 1012,
            ],
        );
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 2,
                'value' => 1013,
            ],
        );

        Event::assertDispatched(RegionalPressuresUpdatedEvent::class, function ($event) {
            return $event->pressures === ['ASR_BOBBINGTON' => 1012, 'ASR_TOPPINGTON' => 1013];
        });
    }

    public function testItHandlesNoRelevantMetars()
    {
        $metars = collect(
            [
                new Metar(['airfield_id' => 1, 'qnh' => 1015]),
                new Metar(['airfield_id' => 3, 'qnh' => 1013]),
            ]
        );
        $this->service->updateRegionalPressuresFromMetars($metars);

        $this->assertDatabaseCount('regional_pressure_settings', 1);
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 1,
                'value' => 1012,
            ],
        );

        Event::assertDispatched(RegionalPressuresUpdatedEvent::class, function ($event) {
            return $event->pressures === ['ASR_BOBBINGTON' => 1012];
        });
    }

    public function testItOnlyUpdatesChangedPressure()
    {
        RegionalPressureSetting::create(['altimeter_setting_region_id' => 1, 'value' => 1012]);
        RegionalPressureSetting::create(['altimeter_setting_region_id' => 2, 'value' => 1014]);

        $metars = collect(
            [
                new Metar(['airfield_id' => 1, 'qnh' => 1015]),
                new Metar(['airfield_id' => 2, 'qnh' => 1014]),
                new Metar(['airfield_id' => 3, 'qnh' => 1013]),
            ]
        );
        $this->service->updateRegionalPressuresFromMetars($metars);

        $this->assertDatabaseCount('regional_pressure_settings', 2);
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 1,
                'value' => 1012,
            ],
        );
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 2,
                'value' => 1013,
            ],
        );

        Event::assertDispatched(RegionalPressuresUpdatedEvent::class, function ($event) {
            return $event->pressures === ['ASR_TOPPINGTON' => 1013];
        });
    }

    public function testItDoesntUpdateIfNothingChanged()
    {
        RegionalPressureSetting::create(['altimeter_setting_region_id' => 1, 'value' => 1012]);
        RegionalPressureSetting::create(['altimeter_setting_region_id' => 2, 'value' => 1013]);

        $metars = collect(
            [
                new Metar(['airfield_id' => 1, 'qnh' => 1015]),
                new Metar(['airfield_id' => 2, 'qnh' => 1014]),
                new Metar(['airfield_id' => 3, 'qnh' => 1013]),
            ]
        );
        $this->service->updateRegionalPressuresFromMetars($metars);

        $this->assertDatabaseCount('regional_pressure_settings', 2);
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 1,
                'value' => 1012,
            ],
        );
        $this->assertDatabaseHas(
            'regional_pressure_settings',
            [
                'altimeter_setting_region_id' => 2,
                'value' => 1013,
            ],
        );

        Event::assertNotDispatched(RegionalPressuresUpdatedEvent::class);
    }

    public function testItReturnsRegionalPressureSettings()
    {
        $expected = [
            'ASR_BOBBINGTON' => 986,
            'ASR_TOPPINGTON' => 988,
        ];

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

        $this->assertEquals($expected, $this->service->getRegionalPressureArray());
    }
}
