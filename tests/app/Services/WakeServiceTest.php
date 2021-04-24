<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use Illuminate\Support\Facades\DB;

class WakeServiceTest extends BaseFunctionalTestCase
{
    private WakeService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(WakeService::class);
    }

    public function testItReturnsWakeSchemesDependency()
    {
        $expected = array_merge(
            [WakeCategoryScheme::where('key', 'UK')->first()->toArray()],
            [WakeCategoryScheme::where('key', 'RECAT_EU')->first()->toArray()],
        );

        $this->assertEquals($expected, $this->service->getWakeSchemesDependency());
    }
}
