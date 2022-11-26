<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Airfield\Airfield;

class AirfieldStandServiceTest extends BaseFunctionalTestCase
{
    private readonly AirfieldStandService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AirfieldStandService::class);
    }

    public function testItGetsStandsByAirfield()
    {
        $expected = collect([
            Airfield::with('stands')->find(1),
            Airfield::with('stands')->find(2)
        ]);

        $this->assertEquals($expected, $this->service->getAllStandsByAirfield());
    }
}
