<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;

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
}
