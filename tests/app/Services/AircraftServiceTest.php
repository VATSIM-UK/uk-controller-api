<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\WakeCategory;

class AircraftServiceTest extends BaseFunctionalTestCase
{
    private AircraftService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AircraftService::class);
    }

    public function testItGeneratesDependency()
    {
        $expected = [
            [
                'id' => 1,
                'icao_code' => 'B738',
                'wake_categories' => [
                    WakeCategory::where('code', 'LM')->firstOrFail()->id,
                    WakeCategory::where('code', 'D')->firstOrFail()->id,
                ],
            ],
            [
                'id' => 2,
                'icao_code' => 'A333',
                'wake_categories' => [
                    WakeCategory::where('code', 'H')->firstOrFail()->id,
                    WakeCategory::where('code', 'B')->firstOrFail()->id,
                ],
            ],
        ];

        $this->assertEquals($expected, $this->service->getAircraftDependency());
    }
}
