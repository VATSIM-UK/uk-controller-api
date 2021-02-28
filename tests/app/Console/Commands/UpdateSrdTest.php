<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Services\SrdService;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class UpdateSrdTest extends BaseUnitTestCase
{
    private $serviceMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->serviceMock = Mockery::mock(SrdService::class);
        $this->app->instance(SrdService::class, $this->serviceMock);
    }

    public function testRunsSrdUpdateWithDataUpdate()
    {
        $this->serviceMock->shouldReceive('updateSrdData')->once()->andReturn(true);
        Artisan::call('srd:update');
    }

    public function testRunsSrdUpdateWithNoDataUpdate()
    {
        $this->serviceMock->shouldReceive('updateSrdData')->once()->andReturn(false);
        Artisan::call('srd:update');
    }
}
