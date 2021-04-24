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
        $this->serviceMock->shouldReceive('newSrdShouldBeAvailable')->andReturnTrue();
        $this->serviceMock->shouldReceive('updateSrdData')->once()->andReturnFalse();
        Artisan::call('srd:update');
    }

    public function testRunsSrdUpdateWithNoDataUpdate()
    {
        $this->serviceMock->shouldReceive('newSrdShouldBeAvailable')->andReturnTrue();
        $this->serviceMock->shouldReceive('updateSrdData')->once()->andReturnFalse();
        Artisan::call('srd:update');
    }

    public function testItDoesntRunSrdUpdateIfNothingToUpdate()
    {
        $this->serviceMock->shouldReceive('newSrdShouldBeAvailable')->andReturnFalse();
        $this->serviceMock->shouldNotReceive('updateSrdData');
        Artisan::call('srd:update');
    }
}
