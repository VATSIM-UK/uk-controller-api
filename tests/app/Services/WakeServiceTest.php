<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\WakeCategoryScheme;

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
        $expected = [
            WakeCategoryScheme::uk()->first()->toArray(),
            WakeCategoryScheme::recat()->first()->toArray(),
        ];
        $this->assertEquals($expected, $this->service->getWakeSchemesDependency());
    }
}
