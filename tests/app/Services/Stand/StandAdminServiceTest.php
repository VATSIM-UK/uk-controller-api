<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandType;
use App\Services\Stand\StandAdminService;

class StandAdminServiceTest extends BaseFunctionalTestCase
{
    private StandAdminService $service;

    public function setUp() : void
    {
        parent::setUp();

        $this->service = $this->app->make(StandAdminService::class);
    }

    public function testItCanListStandTypes()
    {
        $standTypes = StandType::all();

        $this->assertEquals($this->service::standTypes(), $standTypes);
    }
}
