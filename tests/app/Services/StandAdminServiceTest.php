<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandType;
use App\Services\StandAdminService;

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