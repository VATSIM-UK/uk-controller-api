<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\Aircraft\AircraftDataUpdatedEvent;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use Illuminate\Support\Facades\Cache;

class AircraftServiceTest extends BaseFunctionalTestCase
{
    private AircraftService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AircraftService::class);

        // Call this to ensure the cache is cleared before each test
        $this->service->aircraftDataUpdated();
    }

    public function testItGeneratesDependency()
    {
        $ukScheme = WakeCategoryScheme::where('key', 'UK')
            ->firstOrFail()
            ->id;

        $recatScheme = WakeCategoryScheme::where('key', 'RECAT_EU')
            ->firstOrFail()
            ->id;

        $expected = [
            [
                'id' => 1,
                'icao_code' => 'B738',
                'wake_categories' => [
                    WakeCategory::where('code', 'LM')->where('wake_category_scheme_id', $ukScheme)->firstOrFail()->id,
                    WakeCategory::where('code', 'M')->where('wake_category_scheme_id', $recatScheme)->firstOrFail()->id,
                ],
            ],
            [
                'id' => 2,
                'icao_code' => 'A333',
                'wake_categories' => [
                    WakeCategory::where('code', 'H')->where('wake_category_scheme_id', $ukScheme)->firstOrFail()->id,
                    WakeCategory::where('code', 'H')->where('wake_category_scheme_id', $recatScheme)->firstOrFail()->id,
                ],
            ],
        ];

        $this->assertEquals($expected, $this->service->getAircraftDependency());
    }

    public function testItGetsAircraftIdFromCode()
    {
        $this->assertEquals(1, $this->service->getAircraftIdFromCode('B738'));
        $this->assertEquals(2, $this->service->getAircraftIdFromCode('A333'));
        $this->assertNull($this->service->getAircraftIdFromCode('A332'));
    }

    public function testAircraftIfFromCodeIsCached()
    {
        $this->assertEquals(1, $this->service->getAircraftIdFromCode('B738'));
        Aircraft::withoutEvents(function ()
        {
            Aircraft::where('code', 'B738')->update(['code' => 'B799']);
        });
        $this->assertEquals(1, $this->service->getAircraftIdFromCode('B738'));
        $this->assertNull($this->service->getAircraftIdFromCode('B799'));
    }

    public function testItClearsAircraftCodeCacheOnAircraftUpdated()
    {
        $this->assertEquals(1, $this->service->getAircraftIdFromCode('B738'));
        Aircraft::withoutEvents(function ()
        {
            Aircraft::where('code', 'B738')->update(['code' => 'B799']);
        });
        $this->service->aircraftDataUpdated();

        $this->assertNull($this->service->getAircraftIdFromCode('B738'));
        $this->assertEquals(1, $this->service->getAircraftIdFromCode('B799'));
    }

    public function testItClearsAircraftCodeCacheOnEvent()
    {
        $this->assertEquals(1, $this->service->getAircraftIdFromCode('B738'));
        Aircraft::where('code', 'B738')->update(['code' => 'B799']);
        $this->assertEquals(1, $this->service->getAircraftIdFromCode('B738'));
        $this->assertNull($this->service->getAircraftIdFromCode('B799'));

        event(new AircraftDataUpdatedEvent);

        $this->assertNull($this->service->getAircraftIdFromCode('B738'));
        $this->assertEquals(1, $this->service->getAircraftIdFromCode('B799'));
    }
}
