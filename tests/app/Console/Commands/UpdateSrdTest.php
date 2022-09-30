<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Services\SrdService;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class UpdateSrdTest extends BaseUnitTestCase
{
    private SrdService $serviceMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->serviceMock = Mockery::mock(SrdService::class);
        $this->app->instance(SrdService::class, $this->serviceMock);
    }

    public function testItRunsSrdUpdate()
    {
        $this->serviceMock->shouldReceive('srdNeedsUpdating')->andReturnTrue();
        $this->serviceMock->shouldReceive('updateSrdData')->once();
        Artisan::call('srd:update');
    }

    public function testItDoesntRunSrdUpdateIfNothingToUpdate()
    {
        $this->serviceMock->shouldReceive('srdNeedsUpdating')->andReturnFalse();
        $this->serviceMock->shouldNotReceive('updateSrdData');
        Artisan::call('srd:update');
    }
}
